# Sistema de Radio - Documentación Técnica

## Arquitectura General

El sistema de radio de Tseyor.org implementa una radio web continua que reproduce contenido de audio de forma secuencial, incluyendo comunicados, meditaciones, talleres y jingles. El sistema está diseñado para funcionar de manera autónoma, gestionando automáticamente las transiciones entre audios.

De este modo los usuarios están escuchado sincronizadamente el mismo audio.

## Componentes Principales

### 1. RadioController

Controlador principal que maneja la lógica de reproducción y estado de la radio.

#### Métodos principales:

- **`index()`**: Página de índice que muestra las emisoras disponibles
- **`emisora()`**: Punto de entrada principal para una emisora específica
- **`gestionarEstadoRadio()`**: Orquesta todo el proceso de gestión del estado

### 2. RadioImport

Clase responsable de importar y procesar todos los contenidos de audio para la radio.

#### Proceso de importación:

1. **Jingles**: Procesa archivos MP3 de la carpeta `/almacen/medios/radio`
2. **Comunicados**: Importa audios de comunicados desde la base de datos
3. **Contenido adicional**: Meditaciones, talleres y cuentos con distribución proporcional

### 3. RadioItem (Modelo)

Modelo de base de datos que representa cada elemento de radio.

**Campos principales:**
- `titulo`: Título del audio
- `url`: URL del archivo de audio
- `duracion`: Duración en segundos
- `categoria`: Categoría del contenido (Comunicados, Jingles, etc.)
- `desactivado`: Flag para deshabilitar elementos

## Flujo de Funcionamiento

### Gestión Temporal y Sincronización

El sistema de radio se basa en cálculos temporales precisos para mantener la sincronización entre todos los usuarios. El funcionamiento temporal sigue este esquema:

#### Tiempo de Servidor como Referencia

- **Tiempo base**: Se usa `time()` (timestamp Unix) del servidor como referencia absoluta
- **Arranque de audio**: Al iniciar un audio, se almacena `arranco_en` con el timestamp actual
- **Cálculo de progreso**: `tiempo_transcurrido = tiempo_actual - arranco_en`
- **Tiempo restante**: `tiempo_restante = duracion_audio - tiempo_transcurrido`

#### Determinación del Cambio de Audio

El sistema evalúa constantemente si es momento de cambiar al siguiente audio:

```php
// Cálculo en tiempo real
$ahora = time();
$tiempoTranscurrido = $ahora - $estadoRadio['arranco_en'];
$tiempoRestante = $duracionActual - $tiempoTranscurrido;

if ($tiempoRestante < 0) {
    // Es momento de cambiar de audio
    seleccionarNuevoAudio();
}
```

### Estado de la Radio

El estado de cada emisora se almacena en la tabla `settings` con la clave `radio_{emisora}` y contiene:

```json
{
  "reproduciendo_jingle": false,
  "audio_actual": {
    "id": 123,
    "titulo": "Comunicado ejemplo",
    "url": "/almacen/medios/...",
    "duracion": "625",
    "categoria": "Comunicados"
  },
  "arranco_en": 1753177648,
  "audio_siguiente": null,
  "jingle_idx": 0
}
```

**Campos clave para temporización:**
- **`arranco_en`**: Timestamp Unix cuando comenzó el audio actual
- **`duracion`**: Duración del audio en segundos (desde metadatos MP3)
- **`audio_actual`**: Información completa del audio en reproducción

### Cálculo de Tiempos de Finalización

#### En el Frontend (respuesta JSON):

```json
{
  "emisora": "comunicados",
  "audio_actual": {...},
  "tiempo_sistema": 1753177705,
  "arranco_en": 1753177648,
  "termina_en": 1753180605,
  "posicion_actual": 57,
  "segundos_restantes": 2900,
  "duracion_en_segundos": 2957
}
```

**Cálculos realizados:**
- **`termina_en`**: `arranco_en + duracion_en_segundos`
- **`posicion_actual`**: `min(tiempo_sistema - arranco_en, duracion_en_segundos)`
- **`segundos_restantes`**: `duracion_en_segundos - posicion_actual`

### Algoritmo de Selección de Audio

El proceso de decisión para cambiar de audio sigue esta secuencia:

#### 1. Verificación Temporal
```php
$ahora = time();
$arranqueEn = $estadoRadio['arranco_en'] ?? 0;
$duracionActual = $this->convertirASegundos($audioActual['duracion'] ?? 0);
$tiempoTranscurrido = $ahora - $arranqueEn;
$tiempoRestante = $duracionActual - $tiempoTranscurrido;
```

#### 2. Control de Concurrencia
Para evitar cambios simultáneos en entornos multi-usuario:
```php
$lockKey = 'radio_cambio_audio_' . $emisoraLimpia;
if (Cache::add($lockKey, true, Carbon::now()->addSeconds(5))) {
    // Proceder con el cambio de audio
}
```

#### 3. Criterios de Cambio
- **Tiempo agotado**: `$tiempoRestante < 0`
- **Duración inválida**: `$duracionActual <= 0`
- **Audio no existe**: `$audioActual === null`

#### 4. Proceso de Selección
1. **Verificación de audio siguiente**: Si hay jingle programado
2. **Búsqueda secuencial**: Siguiente ID en la misma categoría
3. **Validación de duración**: El nuevo audio debe tener duración válida
4. **Intercalación de jingles**: Insertar jingle si está disponible
5. **Actualización de estado**: Nuevo `arranco_en = time()`

#### 5. Persistencia del Estado
```php
Setting::updateOrCreate(
    ['name' => 'radio_' . $emisora],
    ['value' => json_encode($estadoRadio)]
);
```

### Cálculo de Duraciones

#### Tecnología utilizada:

1. **ffprobe**: Método único y confiable usando @ffprobe-installer/ffprobe
   - Instalar con: `npm install @ffprobe-installer/ffprobe`
   - Ubicación: `node_modules/@ffprobe-installer/win32-x64/ffprobe.exe`
   - Comando: `ffprobe -v quiet -show_entries format=duration -of csv=p=0 "archivo.mp3"`

2. **Mantenimiento de valor actual**: Si ffprobe falla, se mantiene la duración existente sin cambios

#### Validaciones:

- Rango válido: 1 segundo a 24 horas (86400 segundos)
- Verificación de existencia de archivos antes del procesamiento
- Logging detallado de errores y procesos de cálculo
- No se modifican duraciones si ffprobe falla

#### Problemas comunes:

- **Archivos no disponibles localmente**: El sistema los omite automáticamente
- **Metadatos corruptos**: Se mantiene la duración actual si ffprobe falla
- **Formatos no estándar**: ffprobe maneja la mayoría de variaciones de MP3

### Conversión de Formatos de Tiempo

La función `convertirASegundos()` maneja múltiples formatos:

- **Enteros**: Segundos directos
- **Strings numéricos**: "625" → 625 segundos
- **Formato tiempo**: "HH:MM:SS" o "MM:SS"

## Gestión de Jingles

### Estrategia de Intercalación

- Los jingles se insertan entre contenido principal
- Rotación secuencial usando `jingle_idx`
- El audio principal se guarda en `audio_siguiente` durante el jingle

### Flujo de Transición

1. Selecciona nuevo contenido principal
2. Si hay jingles disponibles:
   - Guarda el contenido en `audio_siguiente`
   - Reproduce el jingle actual
   - Incrementa `jingle_idx`
3. Al terminar el jingle, transiciona al contenido guardado

## Control de Concurrencia

### Locks de Cache

Para evitar condiciones de carrera en entornos multi-usuario:

```php
$lockKey = 'radio_cambio_audio_' . $emisoraLimpia;
Cache::add($lockKey, true, Carbon::now()->addSeconds(5))
```

### Validaciones de Estado

- Verificación doble del tiempo transcurrido
- Validación de duraciones antes de usar
- Recuperación automática en casos de error

## Categorías y Emisoras

### Tipos de Contenido

1. **Comunicados**: Canalizaciones principales de Tseyor
2. **Jingles**: Elementos de transición ("Radio Tseyor")
3. **Meditaciones, cuentos y Talleres**: Contenido complementario

### Emisoras Disponibles

Las emisoras se definen por las categorías disponibles en la base de datos, excluyendo "Jingles" que se usa para la transición entre audios.

## Logging y Monitoreo

### Información Registrada para Temporización

El sistema registra información detallada sobre los cálculos temporales:

```json
{
  "audio_actual": {
    "id": 628,
    "titulo": "698. La tríada o ágora del junantal",
    "duracion": "2957"
  },
  "duracion_actual_segundos": 2957,
  "arranco_en": 1753177648,
  "ahora": 1753177705,
  "transcurrido": 57,
  "quedan": 2900
}
```

### Eventos Monitoreados

- **Verificaciones de cambio**: Cada request evalúa si cambiar audio
- **Errores de duración**: Cuando el cálculo de duración falla
- **Problemas de archivos**: Archivos MP3 no encontrados o corruptos
- **Transiciones de estado**: Cambios entre jingles y contenido principal
- **Locks de concurrencia**: Bloqueos para evitar condiciones de carrera

### Debugging de Sincronización

Para diagnosticar problemas de sincronización:

1. **Verificar timestamps**: `arranco_en` vs `time()` actual
2. **Validar duraciones**: Comparar duración calculada vs real
3. **Monitorear transiciones**: Logs de cambios de audio
4. **Control de concurrencia**: Verificar locks y timeouts

## API y Endpoints

### `/radio`
Página principal con lista de emisoras

### `/radio/{emisora}`
Reproductor de emisora específica con estado actual del audio

**Respuesta incluye:**
- Audio actual en reproducción
- Tiempo de inicio y duración
- Posición actual de reproducción
- Audio siguiente (si es jingle)
- Estado de reproducción

## Mantenimiento

### Comando de Verificación de Duraciones de los audios

Es vital que el campo "duracion" en la tabla "radio" contenga exactamente el nº de segundos reales de cada audio. Este cálculo se puede generar y regenerar con el siguiente comando.

```bash
php artisan radio:verificar-duraciones [opciones]
```

#### Opciones disponibles:

- `--fix`: Corregir automáticamente las duraciones encontradas
- `--per-page=N`: Número de elementos por página (por defecto: 50)
- `--page=N`: Página específica a procesar (por defecto: 1)
- `--all`: Procesar todos los elementos, no solo los problemáticos
- `--id=N`: Procesar solo el elemento con ID específico

#### Ejemplos de uso:

**Verificar elementos problemáticos (sin cambios):**
```bash
php artisan radio:verificar-duraciones --page=1 --per-page=25
```

**Corregir primera página de elementos problemáticos:**
```bash
php artisan radio:verificar-duraciones --fix --page=1 --per-page=50
```

**Procesar todos los elementos por páginas:**
```bash
php artisan radio:verificar-duraciones --fix --all --page=1 --per-page=100
```

**Corregir elemento específico:**
```bash
php artisan radio:verificar-duraciones --fix --id=1116
```

#### Funcionalidades:

- **Paginación**: Procesa elementos por lotes para evitar timeouts
- **Detección automática**: Identifica elementos con duraciones problemáticas (≤0 o >86400 segundos)
- **Corrección con ffprobe**: Usa ffprobe para calcular duraciones precisas
- **Omisión inteligente**: Salta archivos no disponibles localmente sin marcarlos como errores
- **Navegación asistida**: Sugiere el comando para la siguiente página
- **Logging detallado**: Registra todos los procesos para debugging

#### Información mostrada:

- Total de elementos encontrados
- Información de paginación (página actual, total de páginas)
- Estado de cada elemento procesado
- Resumen final con estadísticas:
  - Elementos actualizados
  - Elementos sin cambios
  - Elementos omitidos (archivo no disponible)
  - Elementos con errores

#### Estrategia recomendada para grandes volúmenes:

1. **Verificar alcance**: `php artisan radio:verificar-duraciones --per-page=10`
2. **Procesar por lotes**: `php artisan radio:verificar-duraciones --fix --page=1 --per-page=50`
3. **Continuar con páginas siguientes**: El comando sugiere automáticamente el siguiente comando
4. **Monitorear progreso**: Revisar logs y estadísticas de cada página

## Consideraciones de Rendimiento y Sincronización

### Almacenamiento de Estado

- **Base de datos**: Estados persistidos en tabla `settings` (no cache volátil)
- **Formato JSON**: Serialización para facilitar lectura/escritura
- **Atomicidad**: Uso de transacciones para consistencia

### Gestión de Concurrencia

- **Locks con timeout**: Evitan bloqueos indefinidos (5 segundos máximo)
- **Validaciones dobles**: Verificación antes y después de locks
- **Recuperación automática**: Sistema se auto-corrige en casos de error

### Precisión Temporal

- **Resolución de segundo**: Suficiente para transiciones de audio
- **Sincronización del servidor**: Todos los usuarios siguen el mismo reloj
- **Tolerancia a desfases**: El sistema maneja pequeñas variaciones de red

### Optimizaciones

- **Validaciones mínimas**: Solo lo necesario en cada request
- **Logs estructurados**: Facilitan debugging sin impacto en rendimiento
- **Cache de metadatos**: Duraciones calculadas se almacenan permanentemente

## Limitaciones Conocidas

1. **Dependencia de archivos físicos**: Requiere acceso al filesystem
2. **Sincronización entre usuarios**: Estado compartido puede causar desinc
3. **Formato MP3**: Limitado a archivos MP3 para metadatos
4. **Estimación de duración**: Método de fallback es aproximado

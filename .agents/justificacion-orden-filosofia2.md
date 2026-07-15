# Justificación del orden narrativo — Filosofia.vue

## Principio rector

La página está diseñada para **3 perfiles de visitante** con distintos niveles de
implicación. El orden de secciones permite que cada perfil recorra la página a
su manera sin sentirse abrumado ni perder el hilo.

---

## Narrativa progresiva: de menos a más

```
ENTRY → PILARES → URGENCIA → EXPLORAR → ACTUAR
```

Este arco cubre al **curioso**, al **escéptico** y al **buscador**
simultáneamente.

### 1. Entry — "No creer, comprobar"

- **Función**: Gancho. El título contradice la expectativa de una página
  filosófica (que pediría fe). Engancha a los 3 perfiles por igual.
- **Contenido mínimo**: solo el lema + tagline. Sin botones, sin distracciones.
  El visitante decide si sigue leyendo por inercia narrativa.

### 2. Los pilares

- **Función**: Fundamentos accesibles. Tras el gancho, el visitante necesita
  entender QUÉ es TSEYOR antes de preguntarse POR QUÉ AHORA.
- **Contenido**: 4 bloques (hermandad, humildad, colectividad, guías). Cada uno
  es autocontenido; el visitante puede leerlos en orden o saltar al que le
  resuene.
- **Por qué aquí**: Son conceptos más tangibles y cercanos que el discurso
  cósmico. Anclan al visitante en tierra firme antes de lanzarlo a la urgencia.
- **Sin botones de acción**: La sección fluye directamente hacia "¿Por qué
  ahora?" sin interrupciones. Los botones "Explorar temas clave" y "Conocer a
  los Guías" se eliminaron porque rompían el flujo narrativo — no había contexto
  aún para que esas acciones tuvieran sentido.

### 3. ¿Por qué ahora?

- **Función**: Urgencia contextualizada. Ahora que el visitante sabe lo que es
  TSEYOR, la pregunta "¿por qué ahora?" tiene peso.
- **Contenido**: 4 sub-bloques:
  1. Rayo Sincronizador y cierre de ciclo cósmico
  2. La especialización como preparación
  3. El despertar del ser humano atlante
  4. **El rol de los Guías Estelares** — puente entre el momento cósmico y la
     ayuda disponible. Explica por qué nos ayudan (siempre ha sido así, es ley
     natural entre civilizaciones), qué hacen (referencias, mensajes,
     motivación, no hacen el trabajo por nosotros), el plan cósmico para la
     Tierra, y la cadena de ayuda interdimensional: ayudándonos a despertar,
     ellos también son impulsados en su evolución.
- **Por qué aquí y no antes**: Este bloque es conceptualmente denso (final de
  ciclo macrocósmico, interdimensionalidad, alquimia). Si se mostrara justo
  después del entry, abrumaría. Con los pilares como base, el visitante tiene
  contexto para procesarlo.
- **Para el curioso**: da profundidad cósmica que despierta la imaginación.
- **Para el escéptico**: si viene desde la comprobación, entiende el "para qué".
- **Para el buscador**: encuentra el marco que da sentido a todo.

### 4. Grid de temas Descubre

- **Función**: Exploración. El usuario ya tiene contexto (urgencia) y base
  (pilares). Ahora puede elegir dónde profundizar.
- **Organización**: Fundamentos → Herramientas → Visión de futuro. El curioso
  empieza por fundamentos, el avanzado va directo a visión.

### 5. CTA final — "Este es tu lugar"

- **Función**: Síntesis y cierre. Recoge los 3 vectores del método
  (comprobación, experimentación, retroalimentación) y los une con la dimensión
  comunitaria.
- **Contenido**: Explica que el camino no es solo individual sino grupal —
  cofradía, hermanamiento, espejo colectivo.
- **Perfiles**:
  - El **curioso** encuentra una invitación abierta a empezar.
  - El **escéptico** ve que no se le pide fe, sino experimentación acompañada.
  - El **buscador** encuentra el espacio que necesita para avanzar.
- **Dos botones**: curso (acción principal) y biblioteca (exploración autónoma).

---

## Cobertura por perfil

| Perfil      | Camino típico en la página                          |
|-------------|------------------------------------------------------|
| Curioso     | 1 → 2 → 4 (explora temas que le llaman) → 5         |
| Escéptico   | 1 → 2 → 3 (el bloque de Guías da el contexto) → 5   |
| Buscador    | 1 → 2 → 3 → 4 → 5 (recorrido completo)              |

Cada perfil puede entrar y salir cuando quiera. No hay jerarquía forzada.

---

## Historial de cambios

| Fecha       | Cambio                                                |
|-------------|-------------------------------------------------------|
| 2026-07-13  | Creación inicial del orden narrativo                 |
| 2026-07-13  | CTA final ampliada con retroalimentación, experimentación y dimensión comunitaria |
| 2026-07-13  | Eliminada sección "Conciencia y autodescubrimiento" (redundante con pilares + grid) |
| 2026-07-13  | Movido "¿Por qué ahora?" de sección 2 a sección 3 (después de pilares). La urgencia cósmica necesita contexto previo para no abrumar |
| 2026-07-13  | Añadido 4º sub-bloque en "¿Por qué ahora?": "Y los Guías Estelares, ¿por qué nos ayudan?" — puente entre el momento cósmico y el rol de los Guías (plan cósmico, ayuda entre civilizaciones, motivación, no hacen el trabajo por nosotros) |
| 2026-07-13  | Añadida cadena de ayuda interdimensional al bloque de Guías: ayudándonos a despertar, ellos también son impulsados. Concepto de interconexión vibratoria y hermanamiento cósmico |
| 2026-07-13  | Eliminados botones "Explorar temas clave" y "Conocer a los Guías Estelares" del final de pilares. Rompían el flujo narrativo. Eliminada función scrollToGrid (huérfana) |
| 2026-07-13  | Eliminada sección "La comprobación como método". El concepto "no creer, comprobar" ya lo cubren el título (entry) y el CTA final. Era redundante y rompía el flujo grid → CTA |
| 2026-07-13  | Reorganización de archivos: Presentacion/ → raíz. Filosofia2.vue → Filosofia.vue. QuienesSomos2.vue → QuienesSomos/index.vue. OrigenesTseyor.vue → QuienesSomos/origenesDeTseyor.vue. Eliminados archivos viejos (Filosofia.vue, QuienesSomos.vue) y carpeta Presentacion/ |

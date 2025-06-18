# TASKS.md

## Tareas actuales
- [x] Mejorar y detallar el manual de despliegue en _DEPLOYMENT.md (15/05/2025)
- [x] Migrar putty.cmd a putty.ps1 usando variables en .env para mayor seguridad (15/05/2025)
- [ ] Terminar la portada del sitio web (17/05/2025)
- [x] Crear script bash boletin_generar.sh para lanzar boletín vía CURL con token y periodicidad (19/05/2025)
- [x] Implementar estructura inicial de servidor MCP y tools para comunicados, entradas y noticias (11/06/2025)
- [x] Refactorizar ComunicadosTool y NoticiasTool siguiendo los principios de EntradasTool (11/06/2025)
- [x] Migrar endpoint /mcp para usar opgginc/laravel-mcp-server manteniendo tokens y tools actuales, sin eliminar archivos previos MCP (13/06/2025)
- [ ] Todas las tools MCP usan capabilities.php para description, inputSchema y annotations (13/06/2025)
- [x] Crear tools MCP para todos los modelos de App\Models (Audio, Centro, Contacto, Equipo, Entrada, Evento, Grupos, Guia, Informe, Libro, Lugar, Meditacion, Noticia, Normativa, Pagina, Psicografia, Sala, Termino, Tutorial, Video): listar, ver, crear, editar, eliminar y ver campos. (13/06/2025) Se seguirán los preceptos comentados a continuación:

### Detalles y reglas para la creación de nuevas Tools MCP para modelos

   - Cada Tool debe basarse en la estructura y principios de las Tools existentes en App\Mcp\ComunicadosTools (herencia de las diferentes clases de Base*Tool).
   - Cada grupo de Tools de un modelo está en una carpeta, como en App\Mcp\ComunicadosTools
   - Cada Tool nueva se basa en definir la clase padre y los valores de atributos adecuados para adaptar el funcionamiento de la clase.
   - Se debe añadir a capabilities.php la información del modelo que falte, si no existe ya.
   - No se define el atributo $name ni $description en ninguna tool, ya que eso se genera dinámicamente en BaseTool.
   - Seguir al pie de la letra el mismo esqueleto que en las Tool de App\Mcp\ComunicadosTools
   - En las Tool de Ver y Listar se especificará la clase Controller asociada al modelo, igual que en VerComunicadosTool. Si no existe controller asociado al modelo, se prescindirá de definirlo.


- [ ] Crear test para las nuevas tool MCP para todos los modelos de App\Models faltantes (Audio, Centro, Contacto, Equipo, Entrada, Evento, Grupos, Guia, Informe, Libro, Lugar, Meditacion, Noticia, Normativa, Pagina, Psicografia, Sala, Termino, Tutorial, Video): listar, ver, crear, editar, eliminar y ver campos,  (14/06/2025) Se seguirá el mismo esquema que ComunicadosToolTest
- [ ] Unificar paginación en todos los Controllers: usar static $ITEMS_POR_PAGINA y $page de request en index con paginate, como en LibrosController, ComunicadosController y PaginasController (16/06/2025)
- [ ] Crear sección para que usuarios autenticados puedan generar y ver su token JWT MCP desde el área de perfil (18/06/2025)

## Discovered During Work
- [ ] Eliminar clases específicas de la carpeta AudioTools (CamposAudioTool.php, CrearAudioTool.php, EditarAudioTool.php, EliminarAudioTool.php, ListarAudiosTool.php, VerAudioTool.php) tras refactorización a tool genérica (15/06/2025)
- [x] Añadir tests para tools MCP de Evento: listar, ver, crear, editar, eliminar (16/06/2025)


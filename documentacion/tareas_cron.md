# Tareas Cron en tseyor.org

| Tarea                              | Programación                   | Comando                                                                                                                                          |
| ---------------------------------- | ------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| tseyor.org database backup daily   | 03:05 diario                   | php /home/$USER/tseyor.org/current/artisan db:backup dreamhost​                                                                              |
| tseyor.org Sitemap.xml diario      | 02:05 diario                   | php /home/$USER/tseyor.org/current/artisan sitemap:generate dreamhost​                                                                       |
| tseyor.org Start Worker            | Cada hora                      | /home/$USER/tseyor.org/current/bash/worker-start.sh -q                                                   |
| muular database backup daily       | 04:05 diario                   | php /home/$USER/muular.tseyor.org/db_backup.php dreamhost​                                                                                   |
| check-mail notificaciones daily    | 06:10 diario                   | php /home/$USER/tseyor.org/current/artisan check-bounced notificaciones --hours=26 dreamhost​                                                |
| tseyor.org SSR start               | 01 cada hora                   | /home/$USER/tseyor.org/current/bash/ssr.sh start                                                         |
| tseyor.org boletin mensual         | 04:40 día 1                    | /home/$USER/tseyor.org/current/bash/boletin_preparar.sh $TOKEN mensual    |
| tseyor.org boletines enviar diario | @daily                         | /home/$USER/tseyor.org/current/bash/boletin_enviar_pendientes.sh $TOKEN       |
| tseyor.org boletin quincenal       | 06:10 días 1,15                | /home/$USER/tseyor.org/current/bash/boletin_preparar.sh $TOKEN quincenal  |
| tseyor.org boletin semanal         | 02:30 sábados                  | /home/$USER/tseyor.org/current/bash/boletin_preparar.sh $TOKEN semanal    |
| Gestionar inscripciones diario     | 05:30 diario                   | php /home/$USER/tseyor.org/current/artisan inscripciones:gestionar dreamhost​                                                                |
| check_system_load                  | Cada 2 horas (30 min)          | /home/$USER/tseyor.org/current/bash/check_system_load.sh >> .../logs/system_load.log & stackoverflow​                                        |
| notificaciones saldo muular        | 00:22,01:22,02:22,03:22 diario | /home/$USER/muular.tseyor.org/notificar_saldo.sh stackoverflow​                                                                              |

# Descripción de las tareas y porqué son necesarias

## Copias de seguridad

- **tseyor.org database backup daily**: Realiza una copia de seguridad diaria de la base de datos para prevenir pérdida de datos.
- **muular database backup daily**: Realiza una copia de seguridad diaria de la base de datos de muular.tseyor.org

## Infraestructura y rendimiento

- **tseyor.org SSR start**: Inicia el servidor de renderizado del lado del servidor (SSR) cada hora para mejorar el rendimiento del sitio web.
- **tseyor.org Start Worker**: Inicia el worker cada hora para procesar tareas en segundo plano (envío de correos, conversión de audios mp3, etc.).
- **check_system_load**: Hace un volcado de la carga del sistema (lista de procesos activos) en un archivo de log.

## SEO y optimización

- **tseyor.org Sitemap.xml diario**: Genera el archivo sitemap.xml diariamente para mejorar el SEO del sitio web.

## Comunicaciones y boletines

- **tseyor.org boletin mensual**: Prepara el boletín mensual a los suscriptores.
- **tseyor.org boletin quincenal**: Prepara el boletín quincenal a los suscriptores.
- **tseyor.org boletin semanal**: Prepara el boletín semanal a los suscriptores.
- **tseyor.org boletines enviar diario**: Envía los boletines pendientes diariamente.
- **check-mail notificaciones daily**: Verifica los correos rebotados en las notificaciones diarias para mantener la lista de correos limpia.

## Gestión de inscripciones y cursos

- **Gestionar inscripciones diario**: Hace un informe diario de las inscripciones a cursos para detectar aquellas que no están siendo supervisadas por parte de los tutores.

## Sistema Muular

- **notificaciones saldo muular**: Envía notificaciones sobre el saldo de muular.tseyor.org periódicamente para mantener a los usuarios informados.

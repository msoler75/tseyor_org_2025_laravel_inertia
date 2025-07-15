<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Suscriptor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

define('MARCAR_COMO_LEIDOS', true);
define('DEV_TESTING_MODE', false); // si es pone a true, leemos y luego guardamos los datos en un archivo .json

class CheckBouncedEmails extends Command
{
    protected $signature = 'check-bounced {mailbox} {--hours=48}';
    protected $description = 'Revisa todos los mensajes de las ultimas {hours} horas del buzón indicado y marca el estado de los suscriptores según el tipo de rebote. Elimina los suscriptores que devolvieron un error definitivo.';

    public function handle()
    {
        // si faltan argumentos, mostrar ayuda:
        if (!$this->argument('mailbox')) {
            $this->error('Falta el argumento "mailbox".');
            $this->info($this->getSynopsis());
            return;
        }

        $emailWebmaster = config('mail.webmaster_email');
        if (!$emailWebmaster || !is_string($emailWebmaster) || trim($emailWebmaster) === '') {
            $this->error("EMAIL_WEBMASTER no está definido correctamente en el .env");
            Log::channel('mailing')->error("EMAIL_WEBMASTER no está definido correctamente en el .env");
            return;
        }

        $mailbox = $this->argument('mailbox');
        $maxHours = (int) $this->option('hours');
        $since = Carbon::now()->subHours($maxHours);
        $totalRead = 0;

        // Para el informe
        $classified = [
            'buzon_lleno' => ['elementos' => [], 'conteo' => 0],
            'no_existe' => ['elementos' => [], 'conteo' => 0],
            'deshabilitado' => ['elementos' => [], 'conteo' => 0],
            'no_disponible' => ['elementos' => [], 'conteo' => 0],
            'dominio_invalido' => ['elementos' => [], 'conteo' => 0],
            'bloqueado' => ['elementos' => [], 'conteo' => 0],
            'comunicacion_fallida' => ['elementos' => [], 'conteo' => 0],
            'smtp_error' => ['elementos' => [], 'conteo' => 0],
            'no_clasificado' => ['elementos' => [], 'conteo' => 0],
            'recibidos_usuario' => ['elementos' => [], 'conteo' => 0], // NUEVA CATEGORÍA
        ];
        $falsos_positivos = [];

        $fromJson = false;
        if (DEV_TESTING_MODE && Storage::disk('local')->exists('bounced_debug.json')) {
            $this->info('Modo Desarrollo: Leyendo mensajes de prueba desde el archivo bounced_debug.json');
            $messages = json_decode(Storage::disk('local')->get('bounced_debug.json'), true);
            $fromJson = true;
        } else {

            // Configuración: marcar como leídos tras procesar (true en desarrollo, false en producción)
            $marcarComoLeidos = MARCAR_COMO_LEIDOS ?? false;

            $client = Client::account($mailbox);
            try {
                $client->connect();
                $this->info("Conexión IMAP OK con '$mailbox'");
                Log::channel('mailing')->info("Conexión IMAP OK con '$mailbox'");
            } catch (\Exception $e) {
                $this->error("Error conectando a IMAP: " . $e->getMessage());
                Log::channel('mailing')->error("Error conectando a IMAP: " . $e->getMessage());
                return;
            }

            $folder = $client->getFolder('INBOX');
            if (!$folder) {
                $this->error("No se encontró la carpeta INBOX.");
                Log::channel('mailing')->error("No se encontró la carpeta INBOX.");
                return;
            }

            $totalInbox = $folder->messages()->all()->count();
            $this->info("Total mensajes en INBOX: $totalInbox");
            Log::channel('mailing')->info("Total mensajes en INBOX: $totalInbox");

            // Solo consultar correos no leídos (unseen)
            $messages = $folder->messages()->unseen()->since($since)->get();
            $this->info("Mensajes NO LEÍDOS recuperados con filtro since($maxHours h): " . $messages->count());
            Log::channel('mailing')->info("Mensajes NO LEÍDOS recuperados con filtro since($maxHours h): " . $messages->count());

            // Si sigue sin mensajes, prueba sin filtro de fecha pero solo no leídos
            if ($messages->count() === 0) {
                $messages = $folder->messages()->unseen()->get();
                $this->warn("No se encontraron mensajes no leídos recientes. Recuperando TODOS los NO LEÍDOS: " . $messages->count());
                Log::channel('mailing')->warning("No se encontraron mensajes no leídos recientes. Recuperando TODOS los NO LEÍDOS: " . $messages->count());
            }


            $this->info("Conectado a IMAP. Mensajes recuperados: " . $messages->count());
            Log::channel('mailing')->info("Conectado a IMAP. Mensajes recuperados: " . $messages->count());

            // Guardar mensajes procesados en un JSON para debug en desarrollo
            //if (App::environment('local', 'development')) {
            if (DEV_TESTING_MODE) {
                $this->info('Modo Desarrollo: Guardamos mensajes al archivo bounced_debug.json');
                $debugMessages = [];
                foreach ($messages as $message) {
                    $debugMessages[] = [
                        'subject' => (string) $message->getSubject(),
                        'from' => $message->getFrom()[0]->mail ?? '',
                        'body' => $message->getTextBody(),
                        'date' => $message->getDate() ? (string) $message->getDate() : null,
                    ];
                }
                Storage::disk('local')->put('bounced_debug.json', json_encode($debugMessages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }

        // Definición de patrones y acciones
        $patterns = [
            // detectado posible spam con oportunidad de desbloquear falso positivo
            [
                'patterns' => [
                    '/false[\s\r\n]positive/i',
                ],
                'estado' => 'bloqueado',
                'accion' => 'manual',
                'log' => 'Mensaje bloqueado o posible falso positivo. Revisar manualmente.',
            ],
            // Buzón lleno / Quota excedida (temporal)
            [
                'patterns' => [
                    '/out[\s\r\n]+of[\s\r\n]+storage[\s\r\n]+space/i',
                    '/storage[\s\r\n]+is[\s\r\n]+full/i',
                    '/mailbox[\s\r\n]+full/i',
                    '/mailbox[\s\r\n]+is[\s\r\n]+full/i',
                    '/quota[\s\r\n]+exceeded/i',
                    '/over.?quota/i',
                    '/QuotaExceededException/i',
                    '/can\'t[\s\r\n]+accept[\s\r\n]+messages[\s\r\n]+now/i',
                ],
                'estado' => 'buzon_lleno',
                'accion' => 'temporal',
                'log' => 'Buzón lleno o cuota excedida. Reintentar más tarde.',
            ],
            // Cuenta no existe / Usuario desconocido (definitivo)
            [
                'patterns' => [
                    '/does[\s\r\n]+not[\s\r\n]+exist/i',
                    '/user[\s\r\n]+unknown/i',
                    '/no[\s\r\n]+such[\s\r\n]+user/i',
                    '/Recipient[\s\r\n]+address[\s\r\n]+rejected:[\s\r\n]+User[\s\r\n]+unknown/i',
                    '/wasn\'t[\s\r\n]+found/i',
                    '/User[\s\r\n]+unknown[\s\r\n]+in[\s\r\n]+virtual[\s\r\n]+alias[\s\r\n]+table/i',
                    '/Recipient[\s\r\n]+address[\s\r\n]+rejected/i',
                    '/could[\s\r\n]+not[\s\r\n]+be[\s\r\n]+delivered/i',
                ],
                'estado' => 'no_existe',
                'accion' => 'definitivo',
                'log' => 'Cuenta no existe o usuario desconocido. No volver a intentar.',
            ],
            // Buzón deshabilitado (definitivo)
            [
                'patterns' => [
                    '/mailbox[\s\r\n]+is[\s\r\n]+disabled/i',
                    '/This[\s\r\n]+mailbox[\s\r\n]+is[\s\r\n]+disabled/i',
                ],
                'estado' => 'deshabilitado',
                'accion' => 'definitivo',
                'log' => 'Buzón deshabilitado. No volver a intentar.',
            ],
            // Mailbox unavailable (temporal o definitivo)
            [
                'patterns' => [
                    '/mailbox[\s\r\n]+unavailable/i',
                ],
                'estado' => 'no_disponible',
                'accion' => 'temporal',
                'log' => 'Buzón no disponible. Reintentar después.',
            ],
            // Error de dominio / Host no encontrado (definitivo)
            [
                'patterns' => [
                    '/Host[\s\r\n]+or[\s\r\n]+domain[\s\r\n]+name[\s\r\n]+not[\s\r\n]+found/i',
                    '/Name[\s\r\n]+service[\s\r\n]+error/i',
                    '/refused[\s\r\n]+to[\s\r\n]+talk[\s\r\n]+to[\s\r\n]+me:[\s\r\n]+421[\s\r\n]+Downstream[\s\r\n]+server[\s\r\n]+error/i',
                    '/Host[\s\r\n]+found[\s\r\n]+but[\s\r\n]+no[\s\r\n]+data[\s\r\n]+record[\s\r\n]+of[\s\r\n]+requested[\s\r\n]+type/i',
                    '/domain[\s\r\n]+does[\s\r\n]+not[\s\r\n]+exist/i',
                    '/Domain[\s\r\n]+not[\s\r\n]+found/i',
                    '/Domain[\s\r\n]+name[\s\r\n]+not[\s\r\n]+found/i',
                    '/No[\s\r\n]+such[\s\r\n]+domain/i',
                    '/Domain[\s\r\n]+name[\s\r\n]+not[\s\r\n]+found/i',
                ],
                'estado' => 'dominio_invalido',
                'accion' => 'manual',
                'log' => 'Dominio inválido o error de servidor. Posible corrección.',
            ],
            // Mensaje bloqueado / Falso positivo (requiere revisión manual)
            [
                'patterns' => [
                    '/Message[\s\r\n]+blocked/i',
                    '/false[\s\r\n]+positive/i',
                    '/5\.7\.1[\s\r\n]+\[CS\][\s\r\n]+Message[\s\r\n]+blocked/i',
                    '/5\.7\.1[\s\r\n]+\[CSR\][\s\r\n]+Account[\s\r\n]+blocked/i',
                    '/Account[\s\r\n]+blocked/i',
                    '/\[CSR\][\s\r\n]+Account[\s\r\n]+blocked/i',
                    '/blocked[\s\r\n]+by[\s\r\n]+security/i',
                    // '/security[\s\r\n]+policy/i',
                    // '/mailchannels/i', // MailChannels es el proveedor que genera este error
                ],
                'estado' => 'bloqueado',
                'accion' => 'manual',
                'log' => 'Mensaje bloqueado o posible falso positivo. Revisar manualmente.',
            ],
            // Error de comunicación (temporal)
            [
                'patterns' => [
                    '/A[\s\r\n]+communication[\s\r\n]+failure[\s\r\n]+occurred/i',
                ],
                'estado' => 'comunicacion_fallida',
                'accion' => 'temporal',
                'log' => 'Error de comunicación. Reintentar más tarde.',
            ],
            // Otros errores SMTP (analizar código)
            [
                'patterns' => [
                    '/5\.1\.1/i', // usuario no existe
                    '/5\.2\.2/i', // buzón lleno o inactivo
                    '/5\.5\.0/i', // acción no permitida
                    '/4\.2\.2/i', // buzón lleno temporal
                ],
                'estado' => 'smtp_error',
                'accion' => 'analizar',
                'log' => 'Error SMTP detectado. Analizar código para acción.',
            ],
        ];

        // Define aquí todos los correos propios que quieras descartar
        $ownEmails = [
            env('IMAP_USERNAME')
            // ...puedes añadir más si usas otros remitentes...
        ];



        foreach ($messages as $message) {
            $totalRead++;
            // Obtener datos según el origen
            if ($fromJson) {
                $subject = $message['subject'] ?? '';
                $from = $message['from'] ?? '';
                $body = $message['body'] ?? '';
                $date = isset($message['date']) ? Carbon::parse($message['date']) : null;
            } else {
                $date = $message->getDate();
                $subject = $message->getSubject();
                $from = $message->getFrom()[0]->mail ?? '';
                $body = $message->getTextBody();
            }
            if ($date instanceof Carbon && $date->lt($since)) {
                Log::channel('mailing')->debug("Correo omitido por antigüedad: " . $subject);
                continue;
            }

            Log::channel('mailing')->debug("Procesando correo: $subject | From: $from");

            // Extraer todas las direcciones de correo del cuerpo
            // primero buscará el patrón To: email@ejemplo.com
            preg_match_all('/To: ([a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/', $body, $ToEmails);
            $ToEmails = $ToEmails[1] ?? [];

            preg_match_all('/(Message-ID: <)?[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $body, $moreemails);
            $moreemails = $moreemails[0] ?? [];

            // fusionamos
            $allEmails = array_merge($ToEmails, $moreemails);

            // eliminamos los correos que empiezan por "Message-ID: <" porque provienen de: Message-ID: <91adb0626921197a37f4dea9e92f7d17@tseyor.org>
            $allEmails = array_filter($allEmails, function ($email) {
                return !preg_match('/^Message-ID: </', $email);
            });

            // Filtrar y descartar los correos propios
            $failedEmails = array_filter($allEmails, function ($email) use ($ownEmails) {
                return !in_array(strtolower($email), array_map('strtolower', $ownEmails));
            });

            // Elige solo los únicos (por si aparecen varias veces)
            $failedEmails = array_unique($failedEmails);

            $matched = false;
            foreach ($patterns as $rule) {
                foreach ($rule['patterns'] as $pattern) {
                    if (
                        preg_match($pattern, $subject) ||
                        preg_match($pattern, $from) ||
                        preg_match($pattern, $body)
                    ) {
                        $matched = true;
                        $estado = $rule['estado'];
                        $accion = $rule['accion'];
                        $classified[$estado]['conteo']++;
                        // Procesar cada email fallido encontrado
                        if (count($failedEmails) > 0) {
                            foreach ($failedEmails as $failedEmail) {
                                $logMsg = "[{$failedEmail}] {$rule['log']} (Asunto: $subject)";
                                $suscriptor = Suscriptor::where('email', $failedEmail)->first();
                                // Guardar para el informe
                                $classified[$estado]['elementos'][] = [
                                    'email' => $failedEmail,
                                    'subject' => $subject,
                                    'from' => $from,
                                ];
                                if ($accion === 'manual') {
                                    // Extraer enlace de reporte (multilínea, patrón "See ...\n...https")
                                    $reportLink = null;
                                    if (preg_match('/See\s*[\r\n ]+(https?:\/\/[^\s"\']+)/is', $body, $linkMatch)) {
                                        $reportLink = $linkMatch[1];
                                    } elseif (preg_match('/please report this.*?((https?:\/\/[^\s"\']+))/is', $body, $linkMatch)) {
                                        $reportLink = $linkMatch[1];
                                    } elseif (preg_match('/https?:\/\/[^\s"\']+/is', $body, $linkMatch)) {
                                        $reportLink = $linkMatch[0];
                                    }
                                    $msg = "[$estado] $failedEmail (revisión manual)";
                                    if ($reportLink) {
                                        $msg .= " - Enlace de reporte: $reportLink";
                                    }
                                    $this->warn($msg);
                                    Log::channel('mailing')->warning($msg);
                                    // Guardar para el informe
                                    $falsos_positivos[] = [
                                        'email' => $failedEmail,
                                        'subject' => $subject,
                                        'from' => $from,
                                        'enlace' => $reportLink,
                                    ];
                                }
                                if ($suscriptor) {
                                    // Estados según acción
                                    if ($accion === 'definitivo') {
                                        $suscriptor->estado = $estado;
                                        $suscriptor->delete();
                                        $this->info("[$estado] $failedEmail");
                                        Log::channel('mailing')->info("[$estado] $failedEmail");
                                    } elseif ($accion === 'temporal') {
                                        $suscriptor->estado = $estado;
                                        $suscriptor->save();
                                        $this->warn("[$estado] $failedEmail (temporal)");
                                        Log::channel('mailing')->warning("[$estado] $failedEmail (temporal)");
                                    } elseif ($accion === 'manual') {
                                        // Extraer enlace de reporte si existe
                                        if (preg_match('/false[\s\r\n]+positive/', $body, $linkMatch)) {
                                            $msg = "[$estado] $failedEmail (revisión manual)";
                                            $this->warn($msg);
                                            Log::channel('mailing')->warning($msg);
                                        } else {
                                            $this->warn("[$estado] $failedEmail (revisión manual)");
                                            Log::channel('mailing')->warning("[$estado] $failedEmail (revisión manual)");
                                        }
                                    } elseif ($accion === 'analizar') {
                                        // Analizar código SMTP para decidir
                                        if (preg_match('/5\.1\.1/', $body)) {
                                            $suscriptor->estado = 'no_existe';
                                            $suscriptor->delete();
                                            $this->info("[no_existe] $failedEmail");
                                            Log::channel('mailing')->warning("[no_existe] $failedEmail");
                                        } elseif (preg_match('/5\.2\.2/', $body)) {
                                            $suscriptor->estado = 'buzon_lleno';
                                            $suscriptor->save();
                                            $this->info("[buzon_lleno] $failedEmail");
                                            Log::channel('mailing')->info("[buzon_lleno] $failedEmail");
                                        } elseif (preg_match('/4\.2\.2/', $body)) {
                                            $suscriptor->estado = 'buzon_lleno';
                                            $suscriptor->save();
                                            $this->warn("[buzon_lleno] $failedEmail (temporal)");
                                            Log::channel('mailing')->info("[$estado] $failedEmail (temporal)");
                                        } else {
                                            $this->warn("[smtp_error] $failedEmail (requiere análisis)");
                                            Log::channel('mailing')->info("[$estado] $failedEmail (requiere análisis)");
                                        }
                                    }
                                } else {
                                    $this->warn("No se encontró suscriptor para: $failedEmail");
                                    Log::channel('mailing')->warning("No se encontró suscriptor para: $failedEmail");
                                }
                                Log::channel('mailing')->info($logMsg);
                            }
                        } else {
                            $this->warn("No se pudo extraer email fallido del mensaje con asunto: $subject");
                            Log::channel('mailing')->warning("No se pudo extraer email fallido del mensaje con asunto: $subject");
                        }
                        break 2;
                    }
                }
            }
            if (!$matched) {
                // Si no es un rebote, ni error, ni patrón conocido, lo consideramos recibido de usuario
                // Solo si el remitente NO es uno de los propios (no es notificaciones@tseyor.org)
                if (!in_array(strtolower($from), array_map('strtolower', $ownEmails))) {
                    $classified['recibidos_usuario']['elementos'][] = [
                        'from' => $from,
                        'subject' => $subject,
                        'body' => $body,
                    ];
                    $classified['recibidos_usuario']['conteo']++;
                    $this->info("[reply] $from | $subject");
                    Log::channel('mailing')->info("[reply] $from | $subject");
                } else {
                    $classified['no_clasificado']['conteo']++;
                    $classified['no_clasificado']['elementos'][] = [
                        'subject' => $subject,
                        'from' => $from,
                    ];
                    $this->warn("Mensaje no clasificado: $subject");
                    Log::channel('mailing')->warning("Mensaje no clasificado: $subject");
                }
            }

            // Marcar como leído si corresponde (solo si es objeto IMAP)
            if (!$fromJson && $marcarComoLeidos) {
                try {
                    $message->setFlag('Seen');
                    Log::channel('mailing')->debug("Correo marcado como leído: $subject");
                } catch (\Exception $e) {
                    Log::channel('mailing')->error("No se pudo marcar como leído: $subject - " . $e->getMessage());
                }
            }
        }

        $this->info('Revisión completada.');
        Log::channel('mailing')->info('Revisión completada.');

        // Mostrar estadísticas por consola
        $this->info("Correos leídos de IMAP: $totalRead");
        foreach ($classified as $tipo => $data) {
            $this->info("  $tipo: " . $data['conteo']);
        }
        Log::channel('mailing')->info("Estadísticas: " . json_encode($classified));

        // --------- Generar informe completo y enviar por correo ---------
        $informe = [];
        $fechaInforme = Carbon::now()->format('Y-m-d');
        $informe[] = "INFORME DE CORREOS RECIBIDOS\n";
        $informe[] = "Fecha: " . Carbon::now()->toDateTimeString();
        $informe[] = "Buzón: $mailbox";
        $informe[] = "Correos leídos: $totalRead";
        $informe[] = "Estadísticas:";
        foreach ($classified as $tipo => $data) {
            if ($data['conteo'] > 0) {
                $informe[] = "  $tipo: " . $data['conteo'];
            }
        }
        $informe[] = "\n--- Clasificación de correos por categoría ---";
        foreach ($classified as $cat => $data) {
            if ($cat === 'recibidos_usuario') continue; // Sección especial al final
            if (count($data['elementos']) === 0) continue; // Omitir categorías vacías
            $informe[] = strtoupper($cat) . " (" . count($data['elementos']) . ")";
            // Mostrar solo los emails, uno por línea
            foreach ($data['elementos'] as $item) {
                if (isset($item['email'])) {
                    $informe[] = $item['email'];
                }
            }
            $informe[] = "";
        }

        if(count($falsos_positivos) > 0)
            $informe[] = "--- FALSOS POSITIVOS DETECTADOS ---";
        foreach ($falsos_positivos as $fp) {
            $informe[] = "- " . $fp['email'] . " | " . $fp['subject'] . " | from: " . $fp['from'];
            if ($fp['enlace']) {
                $informe[] = "    Enlace de reporte: " . $fp['enlace'];
            }
        }
        if(count($classified['recibidos_usuario']['elementos']) > 0)
            $informe[] = "\n--- MENSAJES RECIBIDOS DE USUARIOS ---";
        foreach ($classified['recibidos_usuario']['elementos'] as $msg) {
            $informe[] = "De: " . $msg['from'];
            $informe[] = "Asunto: " . $msg['subject'];
            $informe[] = "Contenido:";
            $informe[] = $msg['body'];
            $informe[] = str_repeat("-", 40);
        }
        $informe[] = "\nAcceso directo al buzón webmail: " . (env('WEBMAIL_URL') ?: 'https://webmail.dreamhost.com/?clearSession=true&_user=notificaciones@tseyor.org');

        $informeTxt = implode("\n", $informe);

        if(!count($falsos_positivos) && !count($classified['recibidos_usuario']['elementos'])) {
            $this->info("No se encontraron rebotes ni mensajes de usuarios.");
            Log::channel('mailing')->info("No se encontraron rebotes ni mensajes de usuarios.");
            return;
        }

        Log::channel('mailing')->info("Informe generado:\n" . $informeTxt);
        // En entorno de desarrollo, guardar el informe en storage/app/informe.txt en vez de enviarlo por correo
        if (DEV_TESTING_MODE) {
            //if (App::environment('local', 'development')) {
            Storage::disk('local')->put('informe.txt', $informeTxt);
            $this->info("Informe guardado en storage/app/informe.txt");
        } else {
            // En producción, enviar por correo
            try {
                Mail::raw($informeTxt, function ($message) use ($mailbox, $emailWebmaster, $fechaInforme) {
                    $message->to(trim($emailWebmaster, '"'))
                        ->subject("Respuestas a notificaciones@tseyor.org {" . $fechaInforme . "}");
                });
                $this->info("Informe enviado a " . $emailWebmaster);
            } catch (\Exception $e) {
                $this->error("No se pudo enviar el informe por correo: " . $e->getMessage());
                Log::channel('mailing')->error("No se pudo enviar el informe por correo: " . $e->getMessage());
            }
        }
    }

    public function getSynopsis(bool $short = false): string
    {
        return "Uso: php artisan suscriptores:check-bounced <mailbox> [--hours=48]\n\n" .
               "Ejemplo: php artisan suscriptores:check-bounced notificaciones --hours=24";
    }

    public function getHelp(): string
    {
        return $this->getSynopsis();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportComunicados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una copia de seguridad de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("$this->description");

        // donde se guardarán los backups
        $carpeta = "backups";

        echo "Creando copia...\n";

        // Obtiene el nombre de la base de datos
        $databaseName = DB::getDatabaseName();


        // Obtiene todos los nombres de las tablas de la base de datos
        $tables = DB::select('SHOW TABLES');

        $sqlScript = '';

        // Array para almacenar las restricciones de clave externa
        $foreignKeys = [];

        foreach ($tables as $table) {
            $tableName = reset($table);

            // Obtiene el esquema de la tabla
            $tableSchema = DB::select("SHOW CREATE TABLE `$tableName`");
            $createTableStatement = $tableSchema[0]->{"Create Table"};

            $createTableStatement = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $createTableStatement);

            // Obtiene las restricciones de clave externa
            $foreignKeysResult = DB::select("SHOW CREATE TABLE `$tableName`");
            $foreignKeysStatement = $foreignKeysResult[0]->{"Create Table"};

            preg_match_all("/CONSTRAINT `(.*?)` FOREIGN KEY \(`(.*?)`\) REFERENCES `(.*?)` \(`(.*?)`\)/", $foreignKeysStatement, $matches);

            foreach ($matches[0] as $index => $match) {
                $constraintName = $matches[1][$index];
                $columnName = $matches[2][$index];
                $referencedTable = $matches[3][$index];
                $referencedColumn = $matches[4][$index];

                $foreignKeys[] = "ALTER TABLE `$tableName` ADD CONSTRAINT `$constraintName` FOREIGN KEY (`$columnName`) REFERENCES `$referencedTable` (`$referencedColumn`);";
            }

            // Remueve las restricciones de clave externa del comando CREATE TABLE
            $createTableStatement = preg_replace("/,\n\s*CONSTRAINT.*?FOREIGN KEY.*?\)/s", "", $createTableStatement);

            $sqlScript .= "\n\n" . $createTableStatement . ";\n\n";
        }

        // Agrega las restricciones de clave externa al script SQL
        $sqlScript .= implode("\n", $foreignKeys);

        if (!empty($sqlScript)) {
            // Guarda el script SQL en un archivo de respaldo
            $backupFileName = $databaseName . '_backup_' . Carbon::now()->timestamp . '.sql';
            $fileHandler = fopen($backupFileName, 'w+');
            $numberOfLines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);

            if ($numberOfLines !== false) {
                echo "Backup creado correctamente en el archivo: $backupFileName";
            } else {
                echo "Error al crear el backup.";
            }
        }


        foreach ($tables as $table) {
            continue;
            $tableName = reset($table);
            // Obtiene los datos de la tabla
            $tableData = DB::table($tableName)->get();

            if ($tableData->count() > 0) {
                $columnNames = array_keys((array) $tableData->first());
                $columnCount = count($columnNames);

                foreach ($tableData as $row) {
                    continue;
                    $sqlScript .= "INSERT INTO `$tableName` VALUES(";

                    foreach ($columnNames as $columnName) {
                        $value = $row->$columnName;

                        if (isset($value)) {
                            if (is_string($value)) {
                                $escapedValue = preg_replace("/\r?\n/m", "\\n", addslashes($value));
                                $sqlScript .= "'" . $escapedValue . "'";
                            } else {
                                $sqlScript .= $value;
                            }
                        } else {
                            $sqlScript .= "NULL";
                        }

                        if ($columnName !== end($columnNames)) {
                            $sqlScript .= ",";
                        }
                    }

                    $sqlScript .= ");\n";
                }
            }

            $sqlScript .= "\n";
        }

        if (!empty($sqlScript)) {
            // Guarda el script SQL en un archivo de respaldo
            $backupFileName = $carpeta . "/" . $databaseName . '_backup_' . Carbon::now()->timestamp . '.sql';
            $fileHandler = fopen($backupFileName, 'w+');
            $numberOfLines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);

            if ($numberOfLines !== false) {
                echo "Backup creado correctamente en el archivo: $backupFileName";
            } else {
                echo "Error al crear el backup.";
            }
        }


    }
}

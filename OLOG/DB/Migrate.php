<?php

namespace OLOG\DB;

/**
 * Common migration tasks
 */
class Migrate {

    const EXECUTED_QUERIES_TABLE_NAME = '_olog_phpdb_migrations';

    static public function executeQuery($space_id, $sql) {
        DB::query($space_id, $sql);

        DB::query(
                $space_id, 'insert into ' . self::EXECUTED_QUERIES_TABLE_NAME . ' (created_at_ts, sql_query) values (?, ?)', array(time(), $sql)
        );
    }

    static public function createMigrationsTable($space_id) {
        DB::query(
                $space_id, 'create table ' . self::EXECUTED_QUERIES_TABLE_NAME . ' (id int not null auto_increment primary key, created_at_ts int not null, sql_query text) engine InnoDB default charset utf8'
        );
    }

    static public function getSqlFileNameForDB($space_id, $project_root_path_in_filesystem = '') {
        if ($project_root_path_in_filesystem == '') {
            // detect path if not passed
            $project_root_path_in_filesystem = getcwd() . DIRECTORY_SEPARATOR;
        }

        $space = DBConfig::space($space_id);
        $db_config_sql_file = $space->getSqlFilePathInProjectRoot();

        $filename = $project_root_path_in_filesystem . $db_config_sql_file;

        return $filename;
    }

    static public function loadSqlArrForDB($space_id, $project_root_path_in_filesystem = '') {
        $filename = self::getSqlFileNameForDB($space_id, $project_root_path_in_filesystem);

        if (!file_exists($filename)) {
            echo "Не найден файл SQL запросов для БД " . $space_id . ": " . $filename . "\n";
            echo "Введите 1 чтобы создать файл SQL запросов, ENTER для выхода:\n";

            $command_str = trim(fgets(STDIN));

            if ($command_str == '1') {
                // TODO: check errors
                file_put_contents($filename, var_export([], true));
            } else {
                exit;
            }
        }

        // TODO: must open file from current project root
        $sql_file_str = file_get_contents($filename); // TODO: better errors check?
        if ($sql_file_str == '') {
            throw new \Exception('SQL queries file doesnt exist or empty.');
        }

        $sql_arr = array();
        eval('$sql_arr = ' . $sql_file_str . ';');
        ksort($sql_arr);

        return $sql_arr;
    }

    static public function addSqlToRegistry($db_name, $sql_str) {
        $sql_arr = self::loadSqlArrForDB($db_name);

        $sql_arr[] = $sql_str;

        //$exported_arr = var_export($sql_arr, true);
        // не используется var_export, потому что он сохраняет массив с индексами, а индексы могут конфликтовать при мерже если несколько разработчиков одновременно добавляют запросы

        $exported_arr = "array(\n";
        foreach ($sql_arr as $sql_str) {
            $sql_str = str_replace('\'', '\\\'', $sql_str);
            $exported_arr .= '\'' . $sql_str . '\',' . "\n";
        }
        $exported_arr .= ")\n";

        $filename = self::getSqlFileNameForDB($db_name);

        // TODO: check errors
        file_put_contents($filename, $exported_arr);
    }

}

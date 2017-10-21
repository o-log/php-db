<?php

namespace OLOG\DB;

/**
 * Common migration tasks
 */
class Migrate {

    const EXECUTED_QUERIES_TABLE_NAME = '_olog_phpdb_migrations';

    static public function executeMigration($space_id, $sql) {
        // ignore empty migrations
        if ($sql == ''){
            return;
        }
        
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

    static public function migrationsFileName($space_id) {
        $space = DBConfig::space($space_id);
        $db_config_sql_file = $space->getSqlFileFullPath();

        $filename = $db_config_sql_file;

        return $filename;
    }

    static public function executedMigrations($space_id){
        $executed_queries_sql_arr = [];
        
        try {
            $executed_queries_sql_arr = DB::readColumn(
                $space_id,
                'select sql_query from ' . Migrate::EXECUTED_QUERIES_TABLE_NAME
            );
        } catch (\Exception $e) {
            //echo CLIUtil::delimiter();
            //echo "Can not load the executed queries list from " . Migrate::EXECUTED_QUERIES_TABLE_NAME . " table:\n";
            //echo $e->getMessage() . "\n\n";

            //echo "Probably the " . Migrate::EXECUTED_QUERIES_TABLE_NAME . " table was not created, creating\n";
            Migrate::createMigrationsTable($space_id);
        }

        return $executed_queries_sql_arr;
    }
    
    static public function allMigrations($space_id) {
        $filename = self::migrationsFileName($space_id);

        if (!file_exists($filename)) {
            throw new \Exception('Migrations file "' . $filename . '" not found');
        }

        // TODO: must open file from current project root
        $sql_file_str = file_get_contents($filename); // TODO: errors check

        $sql_arr = preg_split('/\R/', $sql_file_str, -1, PREG_SPLIT_NO_EMPTY );

        return $sql_arr;
    }

    static public function executeMigrations($space_id) {
        $migrations = self::newMigrations($space_id);
        foreach ($migrations as $sql){
            Migrate::executeMigration($space_id, $sql);
        }
    }

    static public function newMigrations($space_id) {
        $executed_sql = Migrate::executedMigrations($space_id);

        $sql_arr = Migrate::allMigrations($space_id);

        $new_migrations = [];
        
        foreach ($sql_arr as $sql) {
            if (!in_array($sql, $executed_sql)) {
                $new_migrations[] = $sql;
            }
        }
        
        return $new_migrations;
    }

    static public function addMigration($space_id, $sql_str) {
        $sql_arr = self::allMigrations($space_id);

        $sql_arr[] = $sql_str;

        $filename = self::migrationsFileName($space_id);

        // TODO: check errors
        file_put_contents($filename, implode("\n", $sql_arr));
    }

}

<?php

namespace OLOG\DB;

class MigrateCLI
{
    static public function run()
    {
        $spaces = DBConfig::spaces();
        if (empty($spaces)){
            echo "No spaces in config\n";
        }

        foreach ($spaces as $space_id => $space) {
            echo "Space: " . $space_id . "\n";
            self::processSpace($space_id);
        }

        return;
    }

    static public function connectOrExit($space_id){
        try {
            $check = DB::readColumn(
                $space_id,
                'select "1"'
            );
        } catch (\Exception $e) {
            echo "Can't connect to database\n";
            echo $e->getMessage() . "\n\n";
            exit;
        }
    }
    
    static public function processSpace($space_id)
    {
        self::connectOrExit($space_id); // checking DB connectivity
        
        $executed_queries_sql_arr = [];
        try {
            $executed_queries_sql_arr = DB::readColumn(
                $space_id,
                'select sql_query from ' . Migrate::EXECUTED_QUERIES_TABLE_NAME
            );
        } catch (\Exception $e) {
            //echo CLIUtil::delimiter();
            echo "Can not load the executed queries list from " . Migrate::EXECUTED_QUERIES_TABLE_NAME . " table:\n";
            echo $e->getMessage() . "\n\n";

            echo "Probably the " . Migrate::EXECUTED_QUERIES_TABLE_NAME . " table was not created, creating\n";
            Migrate::createMigrationsTable($space_id);
        }

        $sql_arr = Migrate::loadSqlArrForDB($space_id);

        foreach ($sql_arr as $sql) {
            if (!in_array($sql, $executed_queries_sql_arr)) {
                echo $sql . "\n";
                Migrate::executeQuery($space_id, $sql);
            }
        }
    }
}
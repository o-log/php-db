<?php

namespace OLOG\DB;

class MigrateCLI
{
    static public function run()
    {
        $spaces = DBConfig::spaces();
        if (empty($spaces)){
            echo "No spaces in config\n";
            return;
        }

        foreach ($spaces as $space_id => $space) {
            echo "Space: " . $space_id . "\n";
            self::processSpace($space_id);
        }
    }

    static public function connectOrExit($space_id){
        try {
            DB::readColumn(
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

        // executeMigrations not used to echo migrations
        
        $sqls = Migrate::newMigrations($space_id);
        foreach ($sqls as $sql){
            echo $sql . "\n";
            Migrate::executeMigration($space_id, $sql);
        }
    }
}
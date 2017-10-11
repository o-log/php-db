<?php

use OLOG\DB\DBConfig;
use OLOG\DB\Space;
use OLOG\DB\DB;
use OLOG\DB\Migrate;

class MigrationsTest extends \PHPUnit_Framework_TestCase {
    const SPACE_TESTS = 'SPACE_TESTS';
    const MIGRATIONS_FILENAME = 'tests.sql';
    
    public function testMigrations(){
        DBDemo\DBDemoConfig::init();
        
        DBConfig::setSpace(self::SPACE_TESTS, new Space(DBDemo\DBDemoConfig::CONNECTOR_DBDEMO, self::MIGRATIONS_FILENAME));

        //
        // erase existing test migrations file or create new one
        //
        file_put_contents(self::MIGRATIONS_FILENAME, '');

        //
        // create tables and check result
        //

        $table1_name = 'dbtest1_' . rand(1000, 100000);
        $table2_name = 'dbtest2_' . rand(1000, 100000);

        // adding first migration
        Migrate::addMigration(self::SPACE_TESTS, 'create table ' . $table1_name . ' (id int)');
        
        // adding second migration: first migration must not be overwritten
        Migrate::addMigration(self::SPACE_TESTS, 'create table ' . $table2_name . ' (id int)');
        
        // execute migrations
        Migrate::executeMigrations(self::SPACE_TESTS);
        
        // check tables after creation
        $tables = DB::readColumn(self::SPACE_TESTS, 'show tables');
        $this->assertContains($table1_name, $tables);
        $this->assertContains($table2_name, $tables);

        //
        // drop tables and check
        //

        Migrate::addMigration(self::SPACE_TESTS, 'drop table ' . $table1_name);
        Migrate::addMigration(self::SPACE_TESTS, 'drop table ' . $table2_name);
        
        Migrate::executeMigrations(self::SPACE_TESTS);

        // check tables after removal
        $tables = DB::readColumn(self::SPACE_TESTS, 'show tables');
        $this->assertNotContains($table1_name, $tables);
        $this->assertNotContains($table2_name, $tables);

        //
        // remove test migrations file to have clean repo
        //

        unlink(self::MIGRATIONS_FILENAME);
    }
}

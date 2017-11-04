<?php

namespace OLOG\Tests;

use Config\Config;
use OLOG\DB\DB;

class DBOpsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests:
     * - DB::query()
     * - DB::lastInsertId()
     * - DB::readColumn()
     * - DB::readObject()
     * - DB::readObjects()
     */
    public function testIntertAndReads()
    {
        Config::init();

        $table = 'test';

        $test_title = 'test_' . rand(1, 99999);
        DB::query(Config::SPACE_DBDEMO, 'insert into ' . $table . ' (title) values (?)', [$test_title]);
        $id = DB::lastInsertId(Config::SPACE_DBDEMO);

        $ids = DB::readColumn(Config::SPACE_DBDEMO, 'select id from ' . $table);
        $this->assertEquals(true, is_array($ids));
        $this->assertContains($id, array_values($ids));

        $obj = DB::readObject(Config::SPACE_DBDEMO, 'select * from ' . $table . ' where id = ?', [$id]);
        $this->assertEquals(false, is_array($obj));
        $this->assertInstanceOf(\stdClass::class, $obj);
        $this->assertEquals($test_title, $obj->title);

        $objs = DB::readObjects(Config::SPACE_DBDEMO, 'select * from ' . $table . ' where id = ?', [$id]);
        $this->assertEquals(true, is_array($objs));
        $this->assertEquals(1, count($objs));
        $this->assertArrayHasKey(0, $objs);
        $obj = $objs[0];
        $this->assertInstanceOf(\stdClass::class, $obj);
        $this->assertEquals($test_title, $obj->title);

        $objs = DB::readObjects(Config::SPACE_DBDEMO, 'select * from ' . $table . ' where id = ?', [$id], 'id');
        $this->assertEquals(true, is_array($objs));
        $this->assertEquals(1, count($objs));
        $this->assertArrayHasKey($id, $objs);
        $obj = $objs[$id];
        $this->assertInstanceOf(\stdClass::class, $obj);
        $this->assertEquals($test_title, $obj->title);
    }
}
<?php

namespace OLOG\DB;

use OLOG\Assert;

class DBConfig
{
    static protected $spaces = [];
    static protected $connectors = [];

    static public function setConnector($connector_id, Connector $dbconnector_obj){
        self::$connectors[$connector_id] = $dbconnector_obj;
    }

    /**
     * @param $db_id
     * @return Connector
     */
    static public function connector($connector_id){
        Assert::assert(array_key_exists($connector_id, self::$connectors));

        return self::$connectors[$connector_id];
    }

    static public function setSpace($space_id, DBSettings $space){
        self::$spaces[$space_id] = $space;
    }

    /**
     * @param $space_id
     * @return DBSettings
     */
    static public function space($space_id): Space{
        Assert::assert(array_key_exists($space_id, self::$spaces));

        return self::$spaces[$space_id];
    }

    static public function spaces(){
        return self::$spaces;
    }
}
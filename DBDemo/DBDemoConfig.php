<?php

namespace DBDemo;

use \OLOG\DB\ConnectorMySQL;
use \OLOG\DB\DBConfig;
use \OLOG\DB\Space;

class DBDemoConfig {
    const CONNECTOR_DBDEMO = 'CONNECTOR_DBDEMO';
    const SPACE_DBDEMO = 'SPACE_DBDEMO';
    
    static public function init(){
        DBConfig::setConnector(self::CONNECTOR_DBDEMO, new ConnectorMySQL('127.0.0.1', 'dbdemo', 'root', '1234'));
        DBConfig::setSpace(self::SPACE_DBDEMO, new Space(self::CONNECTOR_DBDEMO, __DIR__ . '/../dbdemo.sql'));
    }
}
<?php

namespace DBDemo;

use \OLOG\DB\Connector;
use \OLOG\DB\DBConfig;
use \OLOG\DB\Space;

class DBDemoConfig {
    const CONNECTOR = 'CONNECTOR';
    const SPACE = 'SPACE';
    
    static public function init(){
        DBConfig::setConnector(self::CONNECTOR, new Connector('127.0.0.1', 'dbdemo', 'root', '1234'));
        DBConfig::setSpace(self::SPACE, new Space(self::CONNECTOR, 'migrations.sql'));
    }
}
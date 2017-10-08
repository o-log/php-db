<?php

namespace DBDemo;

use \OLOG\DB\Connector;
use \OLOG\DB\DBConfig;
use \OLOG\DB\Space;

class DBDemoConfig {
    const CONNECTOR = 'CONNECTOR';
    const SPACE = 'SPACE';
    
    static public function init(){
        DBConfig::setConnector(self::CONNECTOR, new Connector('localhost', 'dbdemo', 'root', '1234'));
        DBConfig::setSpace(self::SPACE, new Space(self::CONNECTOR, __FILE__ . '/../migrations'));
    }
}
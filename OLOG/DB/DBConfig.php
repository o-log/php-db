<?php

namespace OLOG\DB;

class DBConfig {

    static protected $spaces = [];
    static protected $connectors = [];

    static public function setConnector($connector_id, ConnectorMySQL $connector) {
        self::$connectors[$connector_id] = $connector;
    }

    static public function connector($connector_id) {
        if (!array_key_exists($connector_id, self::$connectors)) {
            throw new \Exception('Connector "' . $connector_id . '" not found');
        }

        return self::$connectors[$connector_id];
    }

    static public function setSpace($space_id, Space $space) {
        self::$spaces[$space_id] = $space;
    }

    static public function space($space_id): Space {
        if (!array_key_exists($space_id, self::$spaces)) {
            throw new \Exception();
        }

        return self::$spaces[$space_id];
    }

    static public function spaces() {
        return self::$spaces;
    }

}

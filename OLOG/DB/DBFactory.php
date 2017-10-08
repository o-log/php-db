<?php

namespace OLOG\DB;

/**
 * Class DBFactory
 * @package DB
 * A connection pool.
 */
class DBFactory
{
    /**
     * @param $db_id
     * @return null|\OLOG\DB\Space
     */
    static public function getDB($db_id)
    {
        static $pdo_arr = array();

        // check static cache
        if (isset($pdo_arr[$db_id])) {
            return $pdo_arr[$db_id];
        }

        $db_settings_obj = DBConfig::space($db_id);

        $pdo_arr[$db_id] = new \OLOG\DB\Space($db_settings_obj);
        return $pdo_arr[$db_id];
    }
}
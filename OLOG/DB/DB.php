<?php

namespace OLOG\DB;

class DB
{
    static public function query($space_id, $query, $params_arr = array())
    {
        $space = \OLOG\DB\DBConfig::space($space_id);

        return $space->query($query, $params_arr);
    }

    static public function readObjects($space_id, $query, $params_arr = array(), $field_name_for_keys = '')
    {
        $statement_obj = self::query($space_id, $query, $params_arr);

        $output_arr = array();

        while (($row_obj = $statement_obj->fetchObject()) !== false) {
            if ($field_name_for_keys != '') {
                $key = $row_obj->$field_name_for_keys;
                $output_arr[$key] = $row_obj;
            }
            else {
                $output_arr[] = $row_obj;
            }
        }

        return $output_arr;
    }

    static public function readObject($db_name, $query, $params_arr = array()) {
        $statement_obj = self::query($db_name, $query, $params_arr);

        return $statement_obj->fetch(\PDO::FETCH_OBJ);
    }

    static public function readColumn($db_id, $query, $params_arr = array())
    {
        $statement_obj = self::query($db_id, $query, $params_arr);

        $output_arr = array();

        while (($field = $statement_obj->fetch(\PDO::FETCH_COLUMN)) !== false) {
            $output_arr[] = $field;
        }

        return $output_arr;
    }

    /**
     * Возвращает false при ошибке или если нет записей.
     * @param $db_name
     * @param $query
     * @param array $params_arr
     * @return mixed
     */
    static public function readField($db_name, $query, $params_arr = array())
    {
        $statement_obj = self::query($db_name, $query, $params_arr);
        return $statement_obj->fetch(\PDO::FETCH_COLUMN);
    }

    static public function lastInsertId($db_name, $db_sequence_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        return $db_obj->lastInsertId($db_sequence_name);
   }

    static public function beginTransaction($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        return $db_obj->beginTransaction();
    }

    static public function inTransaction($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        return $db_obj->inTransaction();
    }

    static public function commit($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        $db_obj->commit();
    }

    static public function rollBack($db_name)
    {
        $db_obj = \OLOG\DB\DBFactory::getDB($db_name);
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        $db_obj->rollBack();
    }
}
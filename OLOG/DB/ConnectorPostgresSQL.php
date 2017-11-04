<?php

namespace OLOG\DB;

class ConnectorPostgresSQL extends ConnectorPDO
{
    public function query(string $query, $params_arr = array()): \PDOStatement
    {
        $statement_obj = $this->pdo()->prepare($query);

        $params_prepared_arr = array();
        foreach($params_arr as $key => $param_value) {
            if(is_object($param_value)){
                throw new \Exception($key . ' passed object');
            }
            /**
             * Хак для БД Postgres:
             * PDO кастит false в пустую строку и postgres не позволяет в поле типа boolean записать её.
             */
            if($param_value === false) {
                $params_prepared_arr[$key] = 'f';
            } elseif ($param_value === true) {
                $params_prepared_arr[$key] = 't';
            } else {
                $params_prepared_arr[$key] = $param_value;
            }
        }

        if (!$statement_obj->execute($params_prepared_arr)) {
            throw new \Exception('query execute failed');
        }

        return $statement_obj;
    }
}
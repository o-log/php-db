<?php

namespace OLOG\DB;

class ConnectorPostgresSQL extends ConnectorPDO implements ConnectorInterface
{
    public function query($query, $params_arr = array())
    {
        try {
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
        }
        catch(\PDOException $e) {
            $uri = '';

            
            if (array_key_exists('REQUEST_URI', $_SERVER)){ // may be not present in command line scripts
                $uri = "\r\nUrl: " . $_SERVER['REQUEST_URI'];
            }

            throw new \PDOException($uri . "\r\n" . $e->getMessage());
        }

        return $statement_obj;
    }
}
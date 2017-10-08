<?php

namespace OLOG\DB;

/**
* можно использовать одно подключение для нескольких
* объектов БД (если они смотрят на одну физическую базу) чтобы правильно 
* работали транзакции
 */
class Connector
{
    protected $server_host;
    protected $db_name;
    protected $user;
    protected $password;
    protected $pdo = null;
    protected $pdo_is_connected = false;

    public function __construct($server_host, $db_name, $user, $password)
    {
        $this->server_host = $server_host;
        $this->db_name = $db_name;
        $this->user = $user;
        $this->password = $password;
    }

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
                /*
                if($param_value === false) {
                    $params_prepared_arr[$key] = 'f';
                } elseif ($param_value === true) {
                    $params_prepared_arr[$key] = 't';
                } else {
                */
                    $params_prepared_arr[$key] = $param_value;
                //}
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

    public function isPdoIsConnected()
    {
        return $this->pdo_is_connected;
    }

    public function setPdoIsConnected($pdo_is_connected)
    {
        $this->pdo_is_connected = $pdo_is_connected;
    }

    /**
     * Подключается к серверу при первом обращении за объектом PDO.
     * @return null
     */
    public function pdo()
    {
        if ($this->isPdoIsConnected()) {
            return $this->pdo;
        }

        $pdo_obj = new \PDO('mysql:host=' . $this->getServerHost() . ';dbname=' . $this->getDbName() . ';charset=utf8', $this->getUser(), $this->getPassword());
        $pdo_obj->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->setPdo($pdo_obj);
        $this->setPdoIsConnected(true);

        return $this->pdo;
    }

    public function setPdo($pdo_obj)
    {
        $this->pdo = $pdo_obj;
    }

    public function getServerHost()
    {
        return $this->server_host;
    }

    public function setServerHost($server_host)
    {
        $this->server_host = $server_host;
    }

    public function getDbName()
    {
        return $this->db_name;
    }

    public function setDbName($db_name)
    {
        $this->db_name = $db_name;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function lastInsertId($db_sequence_name)
    {
        return $this->pdo()->lastInsertId($db_sequence_name);
    }

    public function inTransaction()
    {
        return $this->pdo()->inTransaction();
    }

    public function beginTransaction()
    {
        return $this->pdo()->beginTransaction();
    }

    public function commit()
    {
        $this->pdo()->commit();
    }

    public function rollBack()
    {
        $this->pdo()->rollBack();
    }
}
<?php

namespace OLOG\DB;

class ConnectorPDO {
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
            $statement = $this->pdo()->prepare($query);

            if (!$statement->execute($params_arr)) {
                throw new \Exception('Query execute failed');
            }
        }
        catch(\PDOException $e) {
            $uri = '';
            
            if (array_key_exists('REQUEST_URI', $_SERVER)){ // may be not present in command line scripts
                $uri = "[" . $_SERVER['REQUEST_URI'] . "] ";
            }

            throw new \PDOException($uri . $e->getMessage());
        }

        return $statement;
    }

    public function pdoIsConnected()
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
        if ($this->pdoIsConnected()) {
            return $this->pdo;
        }

        $pdo = new \PDO('mysql:host=' . $this->getServerHost() . ';dbname=' . $this->getDbName() . ';charset=utf8', $this->getUser(), $this->getPassword());
        // do not check result: "PDO::__construct() throws a PDOException if the attempt to connect to the requested database fails."
        
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); // to use ? for limit and offset values
        $this->setPdo($pdo);
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

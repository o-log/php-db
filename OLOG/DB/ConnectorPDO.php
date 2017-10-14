<?php

namespace OLOG\DB;

class ConnectorPDO
{
    protected $server_host;
    protected $db_name;
    protected $user;
    protected $password;
    protected $pdo = null;
    protected $pdo_is_connected = false;

    public function __construct(string $server_host, string $db_name, string $user, string $password)
    {
        $this->server_host = $server_host;
        $this->db_name = $db_name;
        $this->user = $user;
        $this->password = $password;
    }

    public function query(string $query, $params_arr = array()): \PDOStatement
    {
        $statement = $this->pdo()->prepare($query);

        if (!$statement->execute($params_arr)) {
            throw new \Exception('Query execute failed');
        }

        return $statement;
    }

    /**
     * Подключается к серверу при первом обращении за объектом PDO.
     */
    public function pdo(): \PDO
    {
        if ($this->pdo_is_connected) {
            return $this->pdo;
        }

        $pdo = new \PDO('mysql:host=' . $this->server_host . ';dbname=' . $this->db_name . ';charset=utf8', $this->user, $this->password);
        // do not check result: "PDO::__construct() throws a PDOException if the attempt to connect to the requested database fails."

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); // to use ? for limit and offset values
        $this->pdo = $pdo;
        $this->pdo_is_connected = true;

        return $this->pdo;
    }

    public function lastInsertId($db_sequence_name)
    {
        return $this->pdo()->lastInsertId($db_sequence_name);
    }

    public function inTransaction(): bool
    {
        return $this->pdo()->inTransaction();
    }

    public function beginTransaction()
    {
        return $this->pdo()->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo()->commit();
    }

    public function rollBack()
    {
        return $this->pdo()->rollBack();
    }
}

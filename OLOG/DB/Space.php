<?php

namespace OLOG\DB;

class Space
{
    protected $connector_id = '';
    protected $sql_file_full_path;

    function getSqlFileFullPath() {
        return $this->sql_file_full_path;
    }

    public function __construct(string $connector_id, string $sql_file_full_path)
    {
        $this->connector_id = $connector_id;
        $this->sql_file_full_path = $sql_file_full_path;
    }

    public function query(string $query, $params_arr = array()): \PDOStatement
    {
        $connector = DBConfig::connector($this->connector_id);
        return $connector->query($query, $params_arr);
    }

    public function lastInsertId($db_sequence_name)
    {
        $connector = DBConfig::connector($this->connector_id);
        return $connector->lastInsertId($db_sequence_name);
    }

    public function inTransaction()
    {
        $connector = DBConfig::connector($this->connector_id);
        return $connector->inTransaction();
    }

    public function beginTransaction()
    {
        $connector = DBConfig::connector($this->connector_id);
        return $connector->beginTransaction();
    }

    public function commit()
    {
        $connector = DBConfig::connector($this->connector_id);
        return $connector->commit();
    }

    public function rollBack()
    {
        $connector = DBConfig::connector($this->connector_id);
        return $connector->rollBack();
    }
}
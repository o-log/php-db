<?php

namespace OLOG\DB;
use OLOG\Assert;

class Space
{
    protected $connector_id = '';
    protected $sql_file_path_in_project_root;

    function getSqlFilePathInProjectRoot() {
        return $this->sql_file_path_in_project_root;
    }

    public function __construct($connector_id, $sql_file_path_in_project_root)
    {
        $this->connector_id = $connector_id;
        $this->sql_file_path_in_project_root = $sql_file_path_in_project_root;
    }

    public function query($query, $params_arr = array())
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
        $connector->commit();
    }

    public function rollBack()
    {
        $connector = DBConfig::connector($this->connector_id);
        $connector->rollBack();
    }
}
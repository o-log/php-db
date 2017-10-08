<?php

namespace OLOG\DB;
use OLOG\Assert;

class Space
{
    protected $connector_id = '';
    protected $sql_file_path_in_project_root;

    public function __construct($connector_id, $sql_file_path_in_project_root = '')
    {
        $this->connector_id = $connector_id;
        $this->sql_file_path_in_project_root = $sql_file_path_in_project_root;
    }

    public function query($query, $params_arr = array())
    {
        $connector = DBConfig::connector($this->connector_id);
    }

    public function lastInsertId($db_sequence_name)
    {
        return $this->getPdoObj()->lastInsertId($db_sequence_name);
    }

    public function inTransaction()
    {
        return $this->getPdoObj()->inTransaction();
    }

    public function beginTransaction()
    {
        return $this->getPdoObj()->beginTransaction();
    }

    public function commit()
    {
        $this->getPdoObj()->commit();
    }

    public function rollBack()
    {
        $this->getPdoObj()->rollBack();
    }
}
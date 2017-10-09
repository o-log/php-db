<?php

namespace OLOG\DB;

interface ConnectorInterface {
    public function query($query, $params_arr = array());
    public function lastInsertId($db_sequence_name);
    public function inTransaction();
    public function beginTransaction();
    public function commit();
    public function rollBack();
}

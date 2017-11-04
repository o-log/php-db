<?php

namespace OLOG\DB;

interface ConnectorInterface {
    public function query(string $query, $params_arr = array()): \PDOStatement;
    public function lastInsertId($db_sequence_name = null);
    public function inTransaction(): bool;
    public function beginTransaction();
    public function commit();
    public function rollBack();
}

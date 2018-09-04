<?php

namespace App\Model;

class SessionData extends Model
{
    public function getTableName() {
        return 'session_data';
    }

    public function getBySessionId($sessionId) {
        $sessions = $this->getByEntityData(['session_id' => $sessionId]);
        return count($sessions) ? $sessions[0] : false;
    }

    public function getNotEmptyAndOlderThan($timestamp) {
        $table = $this->getTableName();
        $rows = $this->getDatabase()->prepare("SELECT session_id FROM `$table` WHERE session_data <> '' AND last_update < :last_update")
            ->bindValue(':last_update', $timestamp)
            ->execute()->fetchAll();
        if(!$rows) return array();
        $builder = $this->builder;
        return array_map(function($row) use ($builder) {
            return $builder($row, $this);
        }, $rows);
    }
}

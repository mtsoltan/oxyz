<?php

namespace App\Model;

class Session extends Model
{
    const ACTIVE_INTERVAL = 1800;

    public function getTableName() {
        return 'sessions';
    }

    public function getByUserId($userId) {
        return $this->getByEntityData(['user_id' => $userId]);
    }

    public function getByUserIdAndSessionId($userId, $sessionId) {
        return $this->getByEntityData(['user_id' => $userId, 'session_id' => $sessionId], 1)[0];
    }

    public function getBySessionId($sessionId) {
        return $this->getByEntityData(['user_id' => $userId, 'session_id' => $sessionId], 1)[0];
    }

    public function destroyAllSessionsByUserId($userId) {
        return $this->deleteByEntityData(['user_id' => $userId]);
    }

    public function destroyByUserIdAndSessionId($userId, $sessionId) {
        return $this->deleteByEntityData(['user_id' => $userId, 'session_id' => $sessionId]);
    }

    public function destroyNonCurrentSessionsByUserId($userId, $currentSessionId) {
        $table = $this->getTableName();
        $this->getDatabase()->prepare("DELETE FROM `$table` WHERE user_id = :user_id AND session_id <> :session_id")
            ->bindValue(':user_id', $userId)
            ->bindValue(':session_id', $currentSessionId)
            ->execute();
    }

    public function getRecentByUserId($userId) {
        $table = $this->getTableName();
        $rows = $this->getDatabase()->prepare("SELECT ID FROM `$table` WHERE user_id = :user_id AND last_update > :last_update")
            ->bindValue(':user_id', $userId)
            ->bindValue(':last_update', time() - self::ACTIVE_INTERVAL)
            ->execute()->fetchAll();
        if(!$rows) return array();
        $builder = $this->builder;
        return array_map(function($row) use ($builder) {
            return $builder($row, $this);
        }, $rows);
    }
}

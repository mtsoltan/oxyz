<?php

namespace App\Model;

class LoginAttempt extends Model
{
    const MAXIMUM_LOGIN_ATTEMPTS = 6;
    const BAN_TIME = 21600; // 6 Hours

    public function getTableName() {
        return 'login_attempts';
    }

    public function getByIP($ip) {
        return $this->getByEntityData(['ip' => $ip]);
    }

    public function getByUserId($userId)
    {
        return $this->getByEntityData(['user_id' => $userId]);
    }

    /**
     * Deletes the entities for a given user and returns the number of rows affected.
     * @param integer $userId
     * @return integer
     */
    public function deleteByUserId($userId)
    {
        return $this->deleteByEntityData(['user_id' => $userId]);
    }

}

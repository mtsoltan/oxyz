<?php

namespace App\Model;

class User extends Model
{
    const CLASS_USER         = 2;
    const CLASS_ADMIN        = 1;
    const CLASS_ROOT         = 0;

    const INITIAL_PERMISSIONS = [
        self::CLASS_USER  => 0x000000, // Shouldn't even be available yet! Futureproof.
        self::CLASS_ADMIN => 0x0000ff,
        self::CLASS_ROOT  => 0xffffff];

    public function getTableName() {
        return 'users';
    }

    /**
     * Data contains username, passhash, state_text
     * @param array $data
     * @return \App\Entity\User
     */
    public function createUser($data) {
        $class = self::CLASS_USER;
        return $this->createEntity(array(
            'username' => $data['username'],
            'passhash' => $data['passhash'],
            'ip' => $data['ip'],
            'force_reset' => true,
            'recovery_key' => '',
            'last_login' => 0,
            'last_access' => 0,
            'session_id' => NULL,
            'class' => $class,
            'state' => self::STATE_ENABLED,
            'state_text' => $data['state_text'].' ~~ '.$this->di['user']->username,
            'permission' => self::INITIAL_PERMISSIONS[$class],
        ))->save();
    }

    public function getByUserId($id) {
        return $this->getById($id);
    }

    public function getByUsername($username) {
        return $this->getByEntityData(['username' => $username])[0];
    }
}

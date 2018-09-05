<?php
namespace App\Entity;

class User extends Entity
{
    const PERMISSIONS = [
        0x000001 => 'user_edit',
        0x000002 => 'file_edit',
        0x000004 => 'order_edit',
        0x000008 => '0x000008',
        0x000010 => '0x000010',
        0x000020 => '0x000020',
        0x000040 => '0x000040',
        0x000080 => '0x000080',
        0x000100 => 'user_add',
        0x000200 => '0x000200',
        0x000400 => '0x000400',
        0x000800 => '0x000800',
        0x001000 => '0x001000',
        0x002000 => '0x002000',
        0x004000 => '0x004000',
        0x008000 => '0x008000',
        0x010000 => '0x010000',
        0x020000 => '0x020000',
        0x040000 => '0x040000',
        0x080000 => '0x080000',
        0x100000 => '0x100000',
        0x200000 => '0x200000',
        0x400000 => '0x400000',
        0x800000 => 'root',
    ];

    public function getData() {
        $data = parent::getData();
        return $data;
    }

    public function hasPermission($permission) {
        if (is_string($permission)) {
            $permission = array_search($permission, $this::PERMISSIONS);
        }
        if (!$permission) {
            return false;
        }
        return ($this->permission & $permission) > 0;
    }

    public function isTechnical() {
        $model = $this->model;
        return
        $this->class == $model::CLASS_ADMIN ||
        $this->class == $model::CLASS_ROOT;
    }

    public function addStateText($text) {
        $this->state_text .= $text.' ~~ '.$this->di['user']->username;
        return $this->save();
    }
}
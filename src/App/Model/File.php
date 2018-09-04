<?php

namespace App\Model;

class File extends Model
{
    const TYPE_PRODUCT  = 0;
    const TYPE_ORDER    = 1;
    const TYPE_ADMIN    = 2;
    const TYPE_GALLERY  = 3; // TODO: Add include in gallery checkbox in file upload.

    const SALT_LENGTH = 16;

    public function getTableName() {
        return 'files';
    }

    public function getByHash($hash) {
        return $this->getByEntityData(['hash' => $hash]);
    }

    public function getByUploaderIpAndHash($uip, $hash) {
        return $this->getByEntityData(['uploader_ip' => $uip, 'hash' => $hash]);
    }

    public function getBySaltedName($name) {
        return $this->getByEntityData(['salted_name' => $name])[0];
    }
}

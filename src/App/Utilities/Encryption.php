<?php

namespace App\Utilities;

class Encryption
{
    protected $di;
    protected $encryption_key;

    public function __construct($di, $key = null) {
        $this->di = $di;
        // Try to use md5 hash of key(for 32 key length req of RIJNDAEL_128), otherwise session key
        $this->encryption_key = $key ? md5($key) : $this->di['config']['security.session_key'];
    }

    public function hash($hash, $salt = '') {
        return \hash('sha256', $hash . $salt);
    }

    public function encrypt($plainData, $customKey = NULL) {
        if (is_null($customKey)) $customKey = $this->encryption_key;
        srand();
        $paddedData=str_pad($plainData, 32-strlen($plainData));
        $initVector=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        $cryptString=mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $customKey, $paddedData, MCRYPT_MODE_CBC, $initVector);
        return base64_encode($initVector.$cryptString);
    }

    public function decrypt($encryptedData, $customKey = NULL) {
        if (is_null($customKey)) $customKey = $this->encryption_key;
        if ($encryptedData != "") {
            $data = false;
            try {
                $data = base64_decode($encryptedData);
                if ($data == false) return false;
                $initVector=substr($data,0,16);
                $r = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $customKey, substr($data,16), MCRYPT_MODE_CBC,$initVector);
                if (!$r) return false;
                if(strlen($r) == 32) return trim($r);
                return $r;
            } catch(\Exception $e) { return false; }
        } else {
            return "";
        }
    }

    public function decryptString($encryptedData, $customKey = NULL) {
        $result = $this->decrypt($encryptedData, $customKey);
        if (!$result) return false;
        $isUTF8 = preg_match('//u', $result);
        if (!$isUTF8) return false;
        return $result;
    }

    public function makeHash($password) {
        return \password_hash($password, PASSWORD_BCRYPT, array('cost' => $this->di['config']['security.bcrypt_cost']));
    }

    public function doesPasswordMatch($password, $hash) {
        return \password_verify($password, $hash);
    }
}

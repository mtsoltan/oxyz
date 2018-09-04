<?php

namespace App\Utilities;

class SessionHandler implements \SessionHandlerInterface
{
    const SESSION_ID_LENGTH = 32;

    private $di;

    public function __construct($di) {
        $this->di = $di;
    }

    private function encrypt($data) {
        return $this->di['utility.encryption']->encrypt($data);
    }

    private function decrypt($data) {
        return $this->di['utility.encryption']->decrypt($data);
    }

    /**
     * @return \App\Model\SessionData
     */
    private function getModel() {
        return $this->di['model.session_data'];
    }

    public function open($save_path, $name) {
        return true;
    }

    public function close() {
        return true;
    }

    public function read($session_id) {
        if (strlen($session_id) != self::SESSION_ID_LENGTH) {
            throw new \Exception("Tried to read session with wrong length ID '$session_id'");
            return '';
        }
        $session = $this->getModel()->getBySessionId($session_id);
        if ($session) {
            $data = $this->decrypt($session->session_data);
            return $data;
        } else {
            $this->write($session_id, '');
        }
        return '';
    }

    public function write($session_id, $session_data) {
        if (strlen($session_id) != self::SESSION_ID_LENGTH) {
            error_log("Tried to write session with wrong length ID '$session_id'");
            return false;
        }
        // if(strlen($session_data) > 20000) $session_data = '';
        $session_data = $this->encrypt($session_data);
        $session = $this->getModel()->getBySessionId($session_id);
        if ($session) {
            $session->session_data = $session_data;
            $session->last_update = time();
        } else {
            $session = $this->getModel()->createEntity([
                'session_id' => $session_id,
                'session_data' => $session_data,
                'last_update' => time(),
                'browser' => BrowserID::identify($this->di['environment']['HTTP_USER_AGENT'])->Browser,
            ]);
        }
        $session->save();
        return true;
    }

    public function destroy($session_id) {
        // throw new \Exception('Destroy Session');
        if (strlen($session_id) != self::SESSION_ID_LENGTH) {
            error_log("Tried to destroy session with wrong length ID '$session_id'");
            return false;
        }
        $session = $this->getModel()->getBySessionId($session_id);
        if ($session) {
            $session->delete();
        }
        return true;
    }

    public function gc($maxlifetime) {
        $dateLimit = new \DateTime("- $maxlifetime seconds");
        $sessions = $this->getModel()->getNotEmptyAndOlderThan($dateLimit->getTimestamp());
        foreach ($sessions as $session) {
            $session->delete();
        }
        return true;
    }

    public function create_sid() {
        $id = $this->di['utility.string']->generateRandomString(self::SESSION_ID_LENGTH);
        return $id;
    }

    public static function unserialize($session_data) {
        $session_data = trim($session_data);
        $method = ini_get("session.serialize_handler");
        switch ($method) {
            case "php":
                return self::unserialize_php($session_data);
                break;
            case "php_binary":
                return self::unserialize_phpbinary($session_data);
                break;
            default:
                throw new \Exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
        }
    }

    private static function unserialize_php($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new \Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    private static function unserialize_phpbinary($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            $num = ord($session_data[$offset]);
            $offset += 1;
            $varname = substr($session_data, $offset, $num);
            $offset += $num;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }
}

<?php
namespace App\Entity;

class File extends Entity
{
    const STORE_DEPTH = 3;

    const SAVE_SUCCESS = 0;
    const SAVE_NOBIN   = 2;
    const SAVE_NOWRITE = 3;
    const SAVE_NOCHMOD = 4;

    public function getData() {
        $data = parent::getData();
        return $data;
    }

    public function save() {
        $status = parent::save();
        if(!$status) {
            @unlink($this->getSavePath());
        }
        return $status;
    }

    public function getName() {
        return $this->name . '.' . $this->ext;
    }

    public function getSaltedName() {
        return $this->salted_name . '.' . $this->ext;
    }

    public function getServeUrl($full = false) {
        return ($full ? $this->di['config']['site.site_url'] : '') .
            $this->di['utility.view']->pathFor('file:serve', array(
                'file' => $this->salted_name,
                'ext' => $this->ext,
            ));
    }

    public function serveFromDisk() {
        $bin = file_get_contents($this->getSavePath());

        if (!$bin) return '';

        return $bin;
    }

    /**
     * Saves the binary data on disk at the path of this file entity.
     * @param string $bin The binary data to be saved.
     * @return int Exit status.
     */
    public function saveToDisk($bin) {
        $encUtil = $this->di['utility.encryption'];
        $strUtil = $this->di['utility.string'];
        $model = $this->model;
        $this->salt = $strUtil->generateRandomString($model::SALT_LENGTH);
        $this->size = strlen($bin);
        $this->hash = $encUtil->hash($bin);
        $this->salted_name = $encUtil->hash($this->hash, $this->salt);
        $savePath = $this->getSavePath();

        // Empty file or really weird bug.
        if (!$savePath || !strlen($bin)) return self::SAVE_NOBIN;

        // Don't try to save a file that already exists
        if(file_exists($savePath)) return self::SAVE_SUCCESS;

        $dir = dirname($savePath);
        if (!is_dir($dir)) mkdir($dir, 0750, true);

        // Cannot save file.
        if(!file_put_contents($savePath, $bin)) { // move_uploaded_file?
            return self::SAVE_NOWRITE;
        }

        // Cannot set permissions on file.
        if(!chmod($savePath, 0640)) {
            @unlink($savePath);
            return self::SAVE_NOCHMOD;
        }

        return self::SAVE_SUCCESS;
    }

    public function deleteFromDisk() {
        return unlink($this->getSavePath());
    }

    public function getSavePath() {
        if (strlen($this->hash) !== 64) return false;
        $dir = $this->di['config']['site.file_path'];
        for ($i = 0; $i < self::STORE_DEPTH; ++$i) {
            $dir .= '/' . $this->hash[$i];
        }
        $path = sprintf('%s/%s.%s', $dir, $this->hash, $this->ext);
        return $path;
    }

}
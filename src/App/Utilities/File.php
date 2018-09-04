<?php

namespace App\Utilities;

use \App\Entity\File as EntityFile;

class File
{
    private $di;
    private $saveStatusErrorMapping = array();

    public function __construct($di) {
        $this->saveStatusErrorMapping = [
            EntityFile::SAVE_NOBIN => $di['strings']['notices.file_empty'],
            EntityFile::SAVE_NOWRITE => $di['strings']['notices.file_write_denied'],
            EntityFile::SAVE_NOCHMOD => $di['strings']['notices.file_chmod_denied'],
        ];
        $this->di = $di;
    }

    /**
     * Attempts to save the file and returns it's id if successful
     * @param string $binaryData The binary content of the file.
     * @param string $resource The URL / File Name.
     * @param int $size Content size of the file.
     * @param array $errors Error array passed by reference.
     * @param \stdClass $entity An \stdClass containing the keys Type and ID. Mainly left null because files are created before their entities.
     * @return boolean|int
     */
    public function attemptFileSave($binaryData, $resource, $size, &$errors, $entity = null) {
        $fileModel = $this->di['model.file'];
        if (is_null($entity)) {
            $entity = new \stdClass();
            $entity->Type = $fileModel::TYPE_ADMIN;
            $entity->ID = 0;
        }
        $strings = $this->di['strings'];
        $saveAs = pathinfo($resource);
        $name = $saveAs['filename'];
        $ext = isset($saveAs['extension']) ? $saveAs['extension'] : 'txt';
        if (!$name || !$ext) {
            $errors = sprintf($strings['notices.file_invalid_name'], $name.'.'.$ext);
            return false;
        }
        if ($size > intval($this->di['config']['site.max_filesize'])) {
            $errors[] = sprintf($strings['notices.file_large'], $name.'.'.$ext);
            return false;
        }

        $uploaderIp = $this->di['ip'];
        $hash = $this->di['utility.encryption']->hash($binaryData);
        if ($file = $fileModel->getByUploaderIpAndHash($uploaderIp, $hash)) {
            // $errors[] = sprintf("The file %s was found. We inserted your own record of it.", $name.'.'.$ext); // Users no longer need to know about this soft error.
            return $file->id;
        }

        $file = $fileModel->createEntity(array(
            'state' => 1,
            'entity_type' => $entity->Type,
            'entity_id' => $entity->ID,
            'name' => $name,
            'salt' => '',
            'hash' => '',
            'ext' => strtolower($ext),
            'size' => 0,
            'uploader_ip' => $uploaderIp,
            'salted_name' => '',
        ));

        $saveStatus = $file->saveToDisk($binaryData);

        if (array_key_exists($saveStatus, $this->saveStatusErrorMapping)) {
            $errors[] = sprintf($this->saveStatusErrorMapping[$saveStatus], $file->name.$file->ext);
            return false;
        }

        $file = $file->save(); // For id.

        if (!$file) {
            $errors[] = sprintf($strings['notices.file_dberror'], $name.'.'.$ext);
            return false;
        }

        return $file->id;
    }

    /**
     * Attempts to curl into URL and get content for saving.
     * @param string $url
     * @throws \Exception
     * @return mixed[]
     */
    public function getDataFromUrl($url) {
        $strings = $this->di['strings'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception($strings['notices.url_invalid_url']);
        };

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        if (!in_array($scheme, self::ALLOWED_URL_SCHEMES)) {
            throw new \Exception($strings['notices.url_invalid_scheme']);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: '.$host, 'Referer: '.$host));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->di['config']['site.timeout']);
        /*
         if ($this->di['config']['site.proxy']) {
         curl_setopt($ch, CURLOPT_PROXY, $this->di['config']['site.proxy']);
         }
         */
        curl_setopt($ch, CURLOPT_CAINFO, $this->di['config']['site.curlcert']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

        $content = curl_exec($ch);
        $fullOpts = json_encode(curl_getinfo($ch));
        $content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $respcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$content_length) {
            throw new \Exception($strings['notices.url_empty_content']);
        }
        if (intval($respcode) >= 400) {
            throw new \Exception(sprintf($strings['notices.url_error_code'], $respcode));
        }

        if ($content_length > $this->di['config']['site.max_filesize']) {
            throw new \Exception($strings['notices.url_large_content']);
        }

        if (!$content) {
            throw new \Exception(sprintf($strings['notices.url_invalid_content'], $content_length));
        }

        if (strlen($content) > $this->di['config']['site.max_filesize']) {
            throw new \Exception($strings['notices.url_large_content']);
        }

        $data = @getimagesizefromstring($content);
        if (!$data || !isset($data['mime'])) {
            throw new \Exception($strings['notices.url_not_image']);
        }

        return [$content_length, $content];
    }
}

<?php
namespace App\Utilities;

class Assets // TODO: Check asset placement folder.
{
    const MANIFEST_FILE = "/public/static/manifest.json";
    const PUBLIC_PATH = "/static";

    private $compiledAssets = array();

    public function __construct($di) {
        $this->loadCompiledAssets();
    }

    private function loadCompiledAssets() {
        $file = BASE_ROOT . self::MANIFEST_FILE;
        if (!file_exists($file)) {
            throw new \App\Exception\NotFound($file);
        }

        $this->compiledAssets = json_decode(file_get_contents($file), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("Failed to parse manifest file");
        }
    }

    public function path($filename) {
        if (array_key_exists($filename, $this->compiledAssets)) {
            return self::PUBLIC_PATH . '/' . $this->compiledAssets[$filename];
        } else {
            throw new \App\Exception\NotFound($filename);
        }
    }
}

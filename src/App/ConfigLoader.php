<?php

namespace App;

class ConfigLoader
{
    private static function parseArray($array, $prefix) {
        $output = array();

        if ($prefix !== '')
            $prefix .= '.';

        foreach ($array as $k => $v) {
            if (is_array($v) && !isset($v[0])) {
                // if it's a subarray, and it *looks* associative
                $output = array_merge($output, self::parseArray($v, $prefix.$k));
            } else {
                $output[$prefix . $k] = $v;
            }
        }

        return $output;
    }

    public static function loadFile($path) {
        // load it as an ini
        if (!file_exists($path)) throw new \Exception("Couldn't find config file $path");
        $parsedFile = parse_ini_file($path, true, INI_SCANNER_RAW);

        return self::parseArray($parsedFile, '');
    }

    public static function load($configPath='config/') {
        if ($configPath[0] !== '/' && strpos($configPath, '://') === false) {
            $configPath = BASE_ROOT . '/' . $configPath;
        }
        $public = self::loadFile($configPath . 'public.ini');
        $private = self::loadFile($configPath . 'private.ini');

        return array_merge($public, $private);
    }
}

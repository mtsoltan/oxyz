<?php

namespace App\Utilities;

use UAParser\Parser;

class BrowserID
{
    public static function identifyOperatingSystem($userAgent) {
        $parser = Parser::create();
        $return = $parser->parse($userAgent)->os->toString();

        return $return?$return:'Unknown';
    }

    public static function identifyBrowser($userAgent) {
        $parser = Parser::create();
        $return = $parser->parse($userAgent)->ua->toString();

        return $return?$return:'Unknown';
    }

    public static function identify($userAgent) {
        $out = new \stdClass;
        $out->OS = self::identifyOperatingSystem($userAgent);
        $out->Browser = self::identifyBrowser($userAgent);

        return $out;
    }
}

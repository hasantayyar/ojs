<?php
namespace Ojs\CoreBundle\Helper;
/**
 * Class StringHelper
 * @package Ojs\CoreBundle\Helper
 */
class StringHelper {
    /**
     * Generate randomized , 64 char sized keys.
     * @return string
     */
    public static function generateKey()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $apikey = '';
        for ($i = 0; $i < 64; $i++) {
            $apikey .= $characters[rand(0, strlen($characters) - 1)];
        }
        $apikey = base64_encode(sha1(uniqid('ue'.rand(rand(), rand())).$apikey));
        return $apikey;
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * @param $string
     * @param bool|true $force_lowercase
     * @param bool|false $anal
     * @return mixed|string
     */
    function sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array(
            "~",
            "`",
            "!",
            "@",
            "#",
            "$",
            "%",
            "^",
            "&",
            "*",
            "(",
            ")",
            "_",
            "=",
            "+",
            "[",
            "{",
            "]",
            "}",
            "\\",
            "|",
            ";",
            ":",
            "\"",
            "'",
            "&#8216;",
            "&#8217;",
            "&#8220;",
            "&#8221;",
            "&#8211;",
            "&#8212;",
            "â€”",
            "â€“",
            ",",
            "<",
            ".",
            ">",
            "/",
            "?"
        );
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;

        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
} 

<?php

/*
 * Author: jan
 * Date: 24-mei-2014
 */

/**
 * Util
 *
 * @author jan
 */
class Util {
/*
    protected static function getShortPath($path) {

        $path = str_replace('\\', '/', $path);
        $doc_root = $_SERVER['DOCUMENT_ROOT'];  //C:/xampp/htdocs/movies13/public
        $pos = strpos($doc_root, '/public');
        if ($pos === false) {
            return $path;
        }
        $app_root = substr($doc_root, 0, $pos) . '/app'; //C:/xampp/htdocs/movies13/app

        return str_replace($app_root, '_APP_', $path);
    }
*/
    /**
     * @param mixed|string $arr
     * @param string|null $label
     */
    public static function debug_log($arr, $label='') {
        global $config;

        $set = $config->settings;

        $backtrace = debug_backtrace();
        $trace = $backtrace[0];
        $file = $trace['file'];
        if (isset($set['srcpath'])) {
            ///Users/orion/Public/htdocs/readtext/app/
            $file = str_replace($set['srcpath'], '.../', $trace['file']);
        }
        $msg = $file . '::' . $trace['line'] . '::';
        if (strlen($label)) {
            $msg .= $label . '=>';
        }
        $msg .= print_r($arr, true);
        error_log($msg);
    }

    public static function convertToUtf8($html) {
        //https://groups.google.com/forum/#!topic/fr.comp.lang.php/nTSqTMmCFUc
        //return utf8_encode($html);

        $encoding_from = 'Windows-1252';    // 'ISO-8859-15'
        $encoding_to = 'UTF-8';
        return mb_convert_encoding($html, $encoding_to, $encoding_from);
    }

    public static function getRedirect($url) {
        Util::debug_log($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $a = curl_exec($ch);
        if (!$a) {
            return array(
                'no code',
                curl_error($ch),
                null
            );
        }
        $lines = explode("\n", $a);
        $response_code = 'unknown code';
        $response_text = '';
        if (count($lines) > 0) {
            $w = explode(' ', $lines[0]);
            if (count($w) > 1) {
                $response_code = $w[1];
            }
            else {
                echo curl_error($ch);
            }
            $response_text = substr($lines[0], strpos($lines[0], ' '));
        }
        $location = null;
        if (preg_match('#Location: (.*)#', $a, $r)) {
            $location = trim($r[1]);
        }
        return array(
            'code' => $response_code,
            'text' => $response_text,
            'url' => $location
        );
    }

    protected static function getShortPath($path) {

        $path = str_replace('\\', '/', $path);
        $doc_root = $_SERVER['DOCUMENT_ROOT']; 
        $pos = strpos($doc_root, '/public');
        if ($pos === false) {
            return $path;
        }
        $app_root = substr($doc_root, 0, $pos) . '/app';

        return str_replace($app_root, '_APP_', $path);
    }

    protected static function thisTime() {
        return Date('d M H:i:s');
    }

    protected static function traceInfo($backtrace) {
        $trace = $backtrace[0];
        $file = self::getShortPath($trace['file']);
        return $file . '::' . $trace['line'] . '::';
    }

    protected static function log($msg) {
        file_put_contents("debug.log", $msg . PHP_EOL, FILE_APPEND);
    }

    public static function info_log($arr, $label = '') {
        $msg = self::thisTime() . ' ';
        $msg .= self::traceInfo(debug_backtrace());
        if (strlen($label)){
            $msg .= $label . '=' . "\n";
        }
        $msg .= print_r($arr, true);
        self::log($msg);
    }
    
    public static function getClassDiv($html, $cls) {
        // '*{<div\s+class=".*?article"\s*>((?:(?:(?!<div[^>]*>|</div>).)++|<div[^>]*>(?1)</div>)*)</div>}si',
        $pattern =  '{<div\s+class="' . $cls . 
            '"\s*>((?:(?:(?!<div[^>]*>|</div>).)++|<div[^>]*>(?1)</div>)*)</div>}si';
        return self::getMulti($html, $pattern);
    }

    public static function getMulti($html, $pattern) {
        Util::info_log($pattern, 'pattern');
        $a = self::getElements($html, $pattern);
        //self::info_log($a[1], 'multi elements parsed, first phase');
        //file_put_contents("multi.html", $html);
        if (isset($a[1][1])) return $a[1][1];
        return $a[1][0];
    }

    public static function getElement($html, $pattern)
    {
        if (empty($pattern)) {
            return null;
        }

        $n = preg_match($pattern, $html, $matches);

        if ($n > 0) {
            return $matches[1];
        }
        else {
            return '';
        }
    }

    public static function getElements($html, $pattern)
    {
        if (empty($pattern)) {
            return null;
        }

        $n = preg_match_all($pattern, $html, $matches);

        if ($n > 0) {
            return $matches;
        }
        else {
            return '';
        }
    }

    public static function getRootUrl() {
        return 'http://' . $_SERVER['HTTP_HOST'];
    }

    public static function escapeFilename($url) {
        return str_replace(
            array(':', '/', '?', '=', '+', '%'),
            array('_', '_', '_', '_', '_', '_'),
            $url
        );
    }

    public static function getHostFromUrl($originalurl)
    {

        return parse_url($originalurl, PHP_URL_HOST);
    }

}

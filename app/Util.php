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

    public static function debug_log($arr) {
        $backtrace = debug_backtrace();
        $trace = $backtrace[0];
        /*
        ob_start();
        var_dump($arr);
        $output = ob_get_clean();
*/
        error_log($trace['file'] . '::' . $trace['line'] . '::' . print_r($arr, true));
    }

    public static function convertToUtf8($html) {
        //https://groups.google.com/forum/#!topic/fr.comp.lang.php/nTSqTMmCFUc
        //return utf8_encode($html);

        $encoding_from = 'Windows-1252';    // 'ISO-8859-15'
        $encoding_to = 'UTF-8';
        return mb_convert_encoding($html, $encoding_to, $encoding_from);
    }

    public static function getHostFromUrl($url) {
        $phost = self::getElement($url, '/\:\/\/(.*?)\//is');
        if (empty($phost)) {
            return $url;
        }
        return $phost;
    }

    public static function getRedirect($url) {
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

    public static function getRootUrl() {
        return 'http://' . $_SERVER['HTTP_HOST'];
    }
}

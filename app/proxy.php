<?php

/*
 * Author: jan
 * Date: 15-feb-2014
 */

/**
 * Proxy
 *
 * @author jan
 */
class Proxy {

    protected function getLocalCopy($zip_path, $cacheFilename) {
        $z = new ZipArchive();
        $result = $z->open($zip_path);
        $content = null;

        if ($result) {
            $content = $z->getFromName($cacheFilename);
            $z->close();
        }
        return $content;
    }

    protected function putLocalCopy($content, $zip_path, $proxy_url) {
        $z = new ZipArchive();
        $result = $z->open($zip_path, ZIPARCHIVE::CREATE);

        if ($result) {
            $z->addFromString($proxy_url, $content);
            $z->close();
        }
        else {
            Util::debug_log('error creating zip archive');
        }
    }

    protected function getredir($h) {
        $lines = explode("\n", $h);
        for ($i=0; $i<count($lines); $i++) {
            if (strpos($lines[$i], 'Location:') !== false) {
                return trim(str_replace('Location:', '', $lines[$i]));
            }
        }
        return null;
    }

    protected function getRemoteFile($url) {
        //Util::info_log('get remote:' . $url);
        if (empty($url)) {
            echo 'Empty url!';
            return '';
        }
        //header('Content-Type: text/html;charset=utf-8', false);

        @$content = file_get_contents($url); // surpress warnings about SSL
        //Util::info_log(strlen($content));

        if (empty($content))
        {
            Util::info_log('Trying curl with useragent');

            // Use user agent cloacking.
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $content = curl_exec($ch);
            //$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            //$redir = $this->getredir($content);
            curl_close($ch);
            //Util::debug_log($httpcode); // 200 OK
            //Util::debug_log($redir);    // null
        }


        if (empty($content)) {
            if (empty($http_response_header)) {
                $msg = 'No content (no response header) for url=' . $url;
                Util::info_log($msg);
                echo $msg;
            }
            else {
                $msg = $http_response_header[0];
                Util::info_log($msg);
                echo '<pre>'. $msg . '</pre>';
            }
        }
        return $content;
    }

    /**
     * Probeer de webpagina uit de cache te halen.
     * Als hij er nog niet in zit, haal hem op (en plaats hem er in).
     *
     * @param {string} $dir
     * @param {string} $url
     * @return mixed|null|string
     */
    protected function cacheGet($dir, $url) {
        $cacheFilename = Util::escapeFilename($url);
        $zip_path = $dir . '/'. $cacheFilename . '.zip';

        if (file_exists($zip_path)) {
            return $this->getLocalCopy($zip_path, $cacheFilename);
        } else {
            $content = $this->getRemoteFile($url);

            // Voorkome lege pagina in cache (meer validatie hier mogelijk?)
            if (strlen($content) > 0) {
                $this->putLocalCopy($content, $zip_path, $cacheFilename);
            }
            return $content;
        }
    }

    /**
     * Haal webpagina op, zo mogelijk uit de cache.
     *
     * @param {string} $url
     * @param {bool} $refresh
     * @return mixed|null|string
     */
    public function get($url, $refresh) {
        global $config;
        //Util::info_log($refresh, 'refresh');

        //Util::debug_log($config->settings);

        if (!$config->settings['cache'] || $refresh) {
            //Util::info_log('not cached:' . $url);
            return $this->getRemoteFile($url);
        }

        $dir = $config->settings['cache_dir'];

        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                Util::info_log('error: ' . $dir);
                return $this->getRemoteFile($url);
            }
        }
        return $this->cacheGet($dir, $url);
    }
}

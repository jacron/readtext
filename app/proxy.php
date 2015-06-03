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
        if (empty($url)) {
            echo 'Empty url!';
            return '';
        }
        header('Content-Type: text/html;charset=utf-8', false);

        @$content = file_get_contents($url);
        if (!$content)
        {

            // Use user agent cloacking.
            $ch = curl_init();
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


        if (!$content) {
            if (empty($http_response_header)) {
                echo 'No content (no response header) for url=' . $url;
            }
            else {
                echo '<pre>'. $http_response_header[0] . '</pre>';
            }
        }
        return $content;
    }

    protected function escapeFilename($url) {
        return str_replace(
            array(':', '/', '?', '=', '+', '%'),
            array('_', '_', '_', '_', '_', '_'),
            $url
        );
    }

    protected function cacheGet($dir, $url) {
        $cacheFilename = $this->escapeFilename($url);
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

    public function get($url) {
        global $config;

        if (!$config->settings['cache']) {
            return $this->getRemoteFile($url);
        }

        $dir = $config->settings['cache_dir'];

        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                Util::debug_log('error: ' . $dir);
                return $this->getRemoteFile($url);
            }
        }
        return $this->cacheGet($dir, $url);
    }
}

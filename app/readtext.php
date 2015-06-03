<?php
/*
 * readtext
 * Author: jan
 * Date: 15-mei-2014
 */

class Reader {
    protected function getLees($url, $utf8) {
        $src = new Source();
        $src->get($url, $utf8);
        return $src->getData();
    }

    protected function errorPage($responsecode, $responsetext, $url, $msg=null) {
        $phost = Util::getHostFromUrl($url);
        $error = null;
        if ($responsecode == 404) {
            $error = 'Pagina niet gevonden';
        }
        else if ($msg) {
            $error = $msg;
        }
        return array(
            'title' => $responsetext,
            'header' => '',
            'content' => $responsetext,
            'error' => $error,
            'originalUrl' => $url,
            'hostname' => $phost,
            'unknown' => 'unknown',
            'clshost' => str_replace('.', '-', $phost),
        );
    }

    public function readRedirect($redirect, $utf8) {
        //  The New York Times (Manohla Dargis)  review
        //$html = file_get_contents('http://www.mrqe.com/external_review?review=363318794%27)');
        //file_put_contents('c:\temp\redirected.html', $html);
        //$url = urldecode($redirect);
        list($responsecode, $responsetext, $url) = Util::getRedirect($redirect);
        if ($responsecode != 200 && $responsecode != 302 && $responsecode != 301) {
            return $this->errorPage($responsecode, $responsetext, $url);
        }
        else if (!$url) {
            $msg = 'Invalid redirect: <a href="' . $redirect . '">' . $redirect . '</a><br>';
            return $this->errorPage($responsecode, $responsetext, $url, $msg);
        }
        else {
            return $this->getLees($url, $utf8);
        }
        /*
        else if ($responsecode != 200) {
            list($responsecode, $responsetext, $url2) = Util::getRedirect($url);
            if ($responsecode != 200 && $responsecode != 302 && $responsecode != 301) {
                return $this->errorPage($responsecode, $responsetext, $url);
            }
            else {
                return $this->getLees($url);
            }
        }*/
    }

    public function read($url, $utf8) {
        //Util::debug_log($url);
        $url = urldecode($url);
        //Util::debug_log($url);
        return $this->getLees($url, $utf8);
    }
}

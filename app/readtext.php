<?php
/*
 * readtext
 * Author: jan
 * Date: 15-mei-2014
 */

class Reader {
    protected function getLees($url, $utf8, $refresh) {
        $src = new Source();
        $url = urldecode($url);
        return $src->get($url, $utf8, $refresh);
    }

    protected function errorPage($r, $msg=null) {
        $phost = Util::getHostFromUrl($r['url']);
        $error = null;
        if ($r['code'] == 404) {
            $error = 'Pagina niet gevonden';
        }
        else if ($msg) {
            $error = $msg;
        }
        return array(
            'title' => $r['text'],
            'header' => '',
            'content' => $r['text'],
            'error' => $error,
            'originalUrl' => $r['url'],
            'hostname' => $phost,
            'unknown' => 'unknown',
            'clshost' => str_replace('.', '-', $phost),
        );
    }

    public function readRedirect($redirect, $utf8, $refresh) {
        $r = Util::getRedirect($redirect);
        //Util::debug_log($r);
        //if ($r['code'] == 301)
        {    // Moved permanently
            $r = Util::getRedirect($redirect);
        }
        if ($r['code'] != 200 && $r['code'] != 302 && $r['code'] != 301) {
            return $this->errorPage($r);
        }
        else if (!$r['url']) {
            $msg = 'Invalid redirect: <a href="' . $redirect . '">' . $redirect . '</a><br>';
            return $this->errorPage($r, $msg);
        }
        else {
            return $this->getLees($r['url'], $utf8, $refresh);
        }
    }

    public function read($url, $utf8, $refresh) {
        return $this->getLees($url, $utf8, $refresh);
    }
}

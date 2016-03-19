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
        //$phost = Util::getHostFromUrl($r['url']);
        $elems = parse_url($r['url']);
        $phost = $elems['host']; //Util::getHostFromUrl($this->originalurl);
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
        if (!isset($r['code'])) {
            Util::debug_log($r);
            return $r;
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

    public function makeHtmlPage($target)
    {
        $content = file_get_contents($target);
        $pos = strpos($content, ';');
        $url = substr($content, 0, $pos);
        $content = substr($content, $pos + 1);
        $pos = strpos($content, ';');
        $title = substr($content, 0, $pos);
        $content = substr($content, $pos + 1);

        $patterns = array(
            '/<div class="reader-actions".*?<\/div>/is',
            '/<span class="access-message".*?<\/span>/is',
            '/<div class="reader-actions-mouseover-text".*?<\/div>/is',
            '/<div class="clearfix".*?<\/div>/is',
            '/<div class="fb-like.*?<\/div>/is',
            '/<script src=".*?<\/script>/is',
            '/<li class="share-button-.*?<\/li>/is',
            '/<li class="share-icon-.*?<\/li>/is',
            '/<ul class="related-content".*?<\/ul>/is',
        );

        foreach($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        return array(
            'content' => $content,
            'hostname' => Util::getHostFromUrl($url),
            'unknown' => 'unknown',
            'title' => $title,
            'originalUrl' => $url,
            'clshost' => '',
            'scheme' => 'http'
        );
    }
}

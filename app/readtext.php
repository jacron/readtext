<?php
/*
 * readtext
 * Author: jan
 * Date: 15-mei-2014
 */

class Reader {
    protected function getLees($url, $utf8) {
        $src = new Source();
        $url = urldecode($url);
        return $src->get($url, $utf8);
    }

    protected function errorPage($r, $msg) {
        //$phost = Util::getHostFromUrl($r['url']);
        //Util::debug_log($r);
        $phost = '';
        $error = $msg;
        $text = '';
        $url = '';
        if (isset($r['code'])) {
            $text = $r['text'];
            $url = $r['url'];
            if ($r['code'] == 400) {
                $error = $r['text']; // 400 Bad request
                $error .= '<br>' . $msg;
            }
            else {
                $elems = parse_url($r['url']);
                $phost = $elems['host']; //Util::getHostFromUrl($this->originalurl);
                $error = null;
                if ($r['code'] == 404) {
                    $error = 'Pagina niet gevonden';
                } else if ($msg) {
                    $error = $msg;
                }
            }
        }
        return array(
            'title' => $text,
            'header' => '',
            'content' => $text,
            'error' => $error,
            'originalUrl' => $url,
            'hostname' => $phost,
            'unknown' => 'unknown',
            'clshost' => str_replace('.', '-', $phost),
        );
    }

    public function readRedirect($redirect, $utf8) {
        //return $this->getLees($redirect, $utf8, $refresh);
        // htmlentities, json_encode, html_entity_decode, htmlspecialchars, urlencode...
        Util::debug_log($redirect);
        $r = Util::getRedirect(($redirect));
        //Util::debug_log($r);
        if (!isset($r['code'])) {
            if ($r[0] == 'no code') {
                $msg = $r[1];
                return $this->errorPage($r, $msg);
            }
            Util::debug_log($r);
            return $r;
        }
        if ($r['code'] != 200 && $r['code'] != 302 && $r['code'] != 301) {
            $msg = 'Invalid redirect: <a href="' . $redirect . '">' . $redirect . '</a><br>';
            return $this->errorPage($r, $msg);
        }
        else if (!$r['url']) {
            $msg = 'Invalid redirect: <a href="' . $redirect . '">' . $redirect . '</a><br>';
            return $this->errorPage($r, $msg);
        }
        else {
            return $this->getLees($r['url'], $utf8);
        }
    }

    public function read($url, $utf8, $refresh) {
        return $this->getLees($url, $utf8);
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
            /** @lang text */
            '/<div class="reader-actions".*?<\/div>/is',
            /** @lang text */
            '/<span class="access-message".*?<\/span>/is',
            /** @lang text */
            '/<div class="reader-actions-mouseover-text".*?<\/div>/is',
            /** @lang text */
            '/<div class="clearfix".*?<\/div>/is',
            /** @lang text */
            '/<div class="fb-like.*?<\/div>/is',
            /** @lang text */
            '/<script src=".*?<\/script>/is',
            /** @lang text */
            '/<li class="share-button-.*?<\/li>/is',
            /** @lang text */
            '/<li class="share-icon-.*?<\/li>/is',
            /** @lang text */
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

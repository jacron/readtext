<?php

/*
 * Author: jan
 * Date: 1-jun-2014
 */

/**
 * Source
 *
 * @author jan
 */
class Source {
    private $hosts;
    private $host;
    private $phost;
    private $scheme;
    private $originalurl;
    private $html;
    private $content;
    private $title;
    private $header;

    public function __construct() {
        $this->hosts = new Hosts(); // de lijst met hosts, de lijst met regex, etc.
    }

    protected function hostProp($prop) {
        return isset($this->host[$prop]) ? $this->host[$prop] : '';
    }

    protected function clsHost() {
        return str_replace('.', '-', $this->phost);
    }

    /*
     * add local path to non external resources
     */
    protected function addLocalResourcePath($p, $prefix) {
        if (is_array($p)) {
            for ($i = 0; $i < count($p); $i++) {
                if (strpos($p[$i], 'http') === false) {
                    $p[$i] = $prefix . $p[$i];
                }
            }
        }
        else {
            if (strpos($p, 'http') === false) {
                $p = $prefix . $p;
            }
        }
        return $p;
    }
    
    protected function getProp($prop) {
        $p = $this->hostProp($prop);
        switch($prop) {
            case 'css':
                $p = $this->addLocalResourcePath($p, 'css/');
                break;
            case 'js':
                $p = $this->addLocalResourcePath($p, 'js/');
                break;
        }
        return $p;
    }
    
    protected function getData() {
        // merge data
        $data = array();
        $props = array('logo', 'style', 'css', 'script', 'js');
        foreach($props as $prop) {
            $data[$prop] = $this->getProp($prop);
        }
        return array_merge($data, array(
            'title' => $this->title,
            'header' => $this->header,
            'content' => $this->content,
            'hostname' => $this->phost,
            'scheme' => $this->scheme,
            'clshost' => $this->clsHost(),
            'unknown' => $this->isUnknown(),
            'originalUrl' => $this->originalurl,
        ));
    }

    protected function removeHtmlElementFromContent($pattern) {
        //Util::debug_log($pattern, 'remove pattern');
        $this->content = preg_replace($pattern, '', $this->content);
    }

    protected function cleanContent() {
        //Util::debug_log($this->hosts->removables, 'removables');
        foreach ($this->hosts->removables as $removable) {
            $this->removeHtmlElementFromContent($removable);
        }
    }

    protected function removeVintageAttributesFromContent() {
        $attrs = array('/width="?\d+"?/is');
        foreach($attrs as $attr) {
            $this->content = preg_replace($attr, '', $this->content);
        }
    }

    protected function remove() {
        if (is_array($this->host['remove'])) {
            foreach ($this->host['remove'] as $remove) {
                $this->removeHtmlElementFromContent($remove);
            }
        }
        else {
            $this->removeHtmlElementFromContent($this->host['remove']);
        }
    }

    protected function removeElements() {
        if (isset($this->host['remove']) && !empty($this->host['remove'])) {
            $this->remove();
        }
    }

    protected function parseElement($elm) {
        if (!array_key_exists($elm, $this->hosts->regex)) {
            Util::debug_log('key does not exist:' . $elm);
            return null;
        }
        return Util::getElement($this->html, $this->hosts->regex[$elm]);
    }

    protected function ebertImage() {
        return $this->parseElement('ebertImage');
    }

    protected function ebertPoster() {
        return $this->parseElement('ebertPoster');
    }

    protected function augmentContent() {
        switch($this->host['name']){
           case 'www.rogerebert.com':
              $img = $this->ebertImage();
              $poster = $this->ebertPoster();
              if ($img) {
                 $poster = '';
              }
               return  $img . $poster . $this->content;
           case 'variety.com':
               return $this->parseElement('varietyImage') .
                $this->parseElement('varietyAuthor') .
                $this->content;
           case 'readwrite.com':
              return $this->parseElement('ReadwriteAbstract') .
              $this->parseElement('ReadwriteAuthor') . $this->content;
           case 'cinemagazine.nl':
               return $this->parseElement('cinemagazineThumb') . $this->content;
           default:
              return $this->content;
        }
    }

    protected function adjustContent($content) {
        $this->content = $content;
        $this->cleanContent();
        $this->removeElements();
        $this->removeVintageAttributesFromContent();
        $this->content = $this->augmentContent();
    }

    protected function warnEmpty($url) {
        return
            "<div class=\"warning\">Sorry, I could not find the content of the "
            . "<a href=\"$url\">original site</a></div>";
    }

    protected function writeContentToDisk($content) {
        global $config;
        $dir = $config->settings['contentdir'];

        if (!file_exists($dir)) {
            mkdir(dirname($dir));
        }
        file_put_contents($dir, $content);
    }

    protected function getMultiElement($pattern) {
        if (substr($pattern, 0, 1) == '@') {
            return Util::getClassDiv($this->html, substr($pattern, 1));
        }
        if (substr($pattern, 0, 1) == '*') {
            return Util::getMulti($this->html, substr($pattern, 1));
        }
        else {
            return Util::getElement($this->html, $pattern);
        }
    }
    
    protected function getContents() {
        $content = '';
        $i = 0;
        while ($i < count($this->host['body'])) {
            $content .= $this->getMultiElement($this->host['body'][$i]);
            $i++;
        }
        return $content;
    }

    protected function getContent() {
        if (is_array($this->host['body'])) {
            $content = $this->getContents();
        }
        else {
            $content = $this->getMultiElement($this->host['body']);
        }

        if (!$content) {
           $this->content = $this->warnEmpty($this->originalurl);
        }
        else {
            $this->adjustContent($content);
        }
        //$this->writeContentToDisk($this->content);
    }

    protected function findHost($hostname) {
        if (is_array($hostname)) {
            foreach ($hostname as $name) {
                if ($this->phost === $name) {
                    return true;
                }
            }
        }
        else if ($this->phost === $hostname) {
            return true;
        }
        return false;
    }

    protected function getMyHost() {
        foreach ($this->hosts->hosts as $host) {
            if ($this->findHost($host['name'])) {
                return $host;
            }
        }
        return $this->hosts->defaultHost;
    }

    protected function getHost() {
        $this->host = $this->getMyHost();
    }

    protected function getHeaders() {
        $content = '';
        $i = 0;
        while ($i < count($this->host['header'])) {
            $content .= Util::getElement($this->html, $this->host['header'][$i]);
            $i++;
        }
        return $content;
    }

    protected function getHeader() {
        // Als er geen header gedefinieerd is, neem dan de titel als header.
        if (isset($this->host['header'])) {
            if (is_array($this->host['header'])) {
                $content = $this->getHeaders();
            }
            else {
                $content = Util::getElement($this->html, $this->host['header']);
            }
            $this->header = $content;
        }
        else
        {
            $this->header = $this->title;
        }
    }

    protected function getTitle() {
        $this->title = html_entity_decode($this->parseElement('title'), ENT_QUOTES);
    }

    protected function getPhost() {
        $elems = parse_url($this->originalurl);
        $this->phost = $elems['host']; //Util::getHostFromUrl($this->originalurl);
        $this->scheme = $elems['scheme'];
    }

    protected function getHtml($refresh) {
        $proxy = new Proxy();
        $this->html = $proxy->get($this->originalurl, $refresh);
    }

    protected function convert() {
        $this->content = Util::convertToUtf8($this->content);
        $this->header = Util::convertToUtf8($this->header);
        $this->title = Util::convertToUtf8($this->title);
    }

    protected function isUnknown() {
        return $this->host['name'] === 'unknown';
    }

    /**
     * Get the cleaned text from known and unknown sites.
     *
     * @param string $url
     * @param bool $forcedUtf8 true, if client sent parameter utf8 in querystring
     * @param bool $refresh
     * @return mixed data
     */
    public function get($url, $forcedUtf8, $refresh) {
        $this->originalurl = $url;
        $this->getHtml($refresh);
        $this->writeContentToDisk($this->html); // check the original source
        $this->getTitle();
        $this->getPhost();
        $this->getHost();
        $this->getContent();
        $this->getHeader();

        $hostUtf8 = isset($this->host['utf8']) && $this->host['utf8'];
        if ($hostUtf8 || $forcedUtf8) {
            $this->convert();
        }
        return $this->getData();
    }
}


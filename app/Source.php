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
    private $html;
    private $content;
    private $title;
    private $phost;
    private $host;
    private $header;
    private $hosts;
    private $originalurl;

    public function __construct() {
        $this->hosts = new Hosts();
    }

    public function getData() {
        $data = array();
        $props = array('logo', 'style', 'css', 'script', 'js');
        foreach($props as $prop) {
            $data[$prop] = $this->hostProp($prop);
        }
        return array_merge($data, array(
            'title' => $this->title,
            'header' => $this->header,
            'content' => $this->content,
            'hostname' => $this->phost,
            'clshost' => $this->clsHost(),
            'unknown' => $this->isUnknown(),
            'originalUrl' => $this->originalurl,
        ));

    }

    protected function removeHtmlElementFromContent($pattern) {
        $this->content = preg_replace($pattern, '', $this->content);
    }

    protected function cleanContent() {
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

    protected function getHtmlElement($pattern) {
        return Util::getElement($this->html, $pattern);
    }

     protected function ebertImage() {
        return $this->getHtmlElement(
                '/(<div class="primary-image">.*?<\/div>)/is');
     }

     protected function ebertPoster() {
         return $this->getHtmlElement(
                '/(<div class="movie-poster">.*?<\/div>)/is');
     }

    protected function getVarietyAuthor() {
         return $this->getHtmlElement(
                '/(<section class="byline">.*?<\/section>)/is');
    }

     protected function varietyImage() {
         return $this->getHtmlElement(
                '/(<figure.*?<\/figure>)/is');
     }

     protected function cinemagazineThumb() {
         return $this->getHtmlElement(
                '/<div class="single-thumb">(.*?)<span/is');
     }

    protected function getReadwriteAbstract() {
         return $this->getHtmlElement(
               '/(<div class="abstract".*?<\/div>)/is');
    }

    protected function getReadwriteAuthor() {
         return $this->getHtmlElement(
               '/(<span class="avatar".*?)\s*?<span class="section"/is');
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
               return $this->varietyImage() . $this->getVarietyAuthor() . $this->content;
           case 'readwrite.com':
              return $this->getReadwriteAbstract() .
                   $this->getReadwriteAuthor() . $this->content;
           case 'cinemagazine.nl':
               return $this->cinemagazineThumb() . $this->content;
           default:
              return $this->content;
        }
    }

    protected function getContent() {
        global $config;

        if (is_array($this->host['body'])) {
            $content = '';
            $i = 0;
            while ($i < count($this->host['body'])) {
                $content .= Util::getElement($this->html, $this->host['body'][$i]);
                $i++;
            }
            //Util::debug_log($i);
        }
        else {
            $content = Util::getElement($this->html, $this->host['body']);
            Util::debug_log('one body');
        }

        //file_put_contents('c:\temp\content.html', $this->html);
        if (!$content) {
            $nocontentdir = $config->settings['nocontentdir'];
           file_put_contents($nocontentdir, $this->html);
           $this->content = '<div class="warning">Sorry, I could not find the content of'
                   . ' the <a href="' . $this->originalurl . '">original site</a>';
        }
        else {
            $this->content = $content;
            $this->cleanContent();
            $this->removeElements();
            $this->removeVintageAttributesFromContent();
            $this->content = $this->augmentContent();
        }
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

    protected function getHeader() {
        if (isset($this->host['header'])) {
            if (is_array($this->host['header'])) {
                $content = '';
                $i = 0;
                while ($i < count($this->host['header'])) {
                    $content .= Util::getElement($this->html, $this->host['header'][$i]);
                    $i++;
                }
            }
            else {
                $content = Util::getElement($this->html, $this->host['header']);
            }
            //$this->header = Util::getElement($this->content, $this->host['header']);
            $this->header = $content;
        }
        else
        {
            $this->header = $this->title;
        }
    }

    protected function getTitle() {
        $this->title = html_entity_decode(
            Util::getElement($this->html,
            '/<title>(.*?)<\/title>/is'),
            ENT_QUOTES
        );
    }

    protected function getPhost($url) {
        $this->phost = Util::getHostFromUrl($url);
    }

    protected function getHtml($url) {
        $proxy = new Proxy();
        $this->html = $proxy->get($url);
        //file_put_contents('C:\\temp\\unknown.html', $this->html);
    }

    protected function convert() {
        $this->content = Util::convertToUtf8($this->content);
        $this->header = Util::convertToUtf8($this->header);
        $this->title = Util::convertToUtf8($this->title);
    }

    public function hostProp($prop) {
        return isset($this->host[$prop]) ? $this->host[$prop] : '';
    }

    public function clsHost() {
        return str_replace('.', '-', $this->phost);
    }

    public function isUnknown() {
        return $this->host['name'] == 'unknown';
    }

    public function get($url, $forcedUtf8) {
        $this->originalurl = $url;
        $this->getHtml($url);
        $this->getTitle();
        $this->getPhost($url);
        $this->getHost();
        $this->getContent();
        $this->getHeader();

        $hostUtf8 = isset($this->host['utf8']) && $this->host['utf8'];
        if ($hostUtf8 || $forcedUtf8) {
            $this->convert();
        }
    }
}


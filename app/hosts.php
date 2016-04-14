<?php

/*
 * Author: jan
 * Created: 15-mei-2014
 */

/*
 * style mag geen mintekens bevatten; dit werkt dus niet:
 * 'style' => '.review-author.headline{font-family:chaparral-bold;}'
 *
 * header niet ingevuld betekent: neem titel over in header
 * let op: soms leidt dat tot verdubbeling
 *
 * de volgende properties kunnen arrays zijn:
 * name, body, header, remove
 *
 * overige velden: css, js
 */
/*
 * hosts: a list of hosts
 * regex:
 * - if starting with *, then use preg_match_all insteadof preg_match,
 * - if starting with @, then after that a class name is provided and the whole
 * div with that classname is extracted
 *
 * name
 * body
 * css
 * js
 * header regex or empty; if empty, then use title of page as header(?)
 * utf8
 * style
 *
 * name, body, css, js may be a string or an array
 */

/**
 * Class Hosts
 *
 */
class Hosts {
    public $removables = array(
        '/(<script.*?<\/script>)/is',
        '/(<font.*?>)/is',
        '/(style=".*?")/is',
        '/(style=\'.*?\')/is',
        '/(bgcolor=".*?")/is',
        '/(bgcolor=\'.*?\')/is',
        '/(<b>)/is',
        '/( class="MsoNormal")/is'
    );

    public $defaultHost = array(
        'name' => 'unknown',
        'body' => array(
            '/(<article .*?<\/article>)/is',
            '/(<article .*?<\/article>)/is',
            //'/(<div class="content">.*?)/is',
            '/<body.*?>(.*?)<\/body>/is',
        ),
        //'utf8' => true,
        //'header' => '',
        'remove' => '/(<aside.*?<\/aside>)/is',
    );

    public $regex = array(
        'title' => '/<title>(.*?)<\/title>/is',
        'ebertImage' => '/(<div class="primary-image">.*?<\/div>)/is',
        'ebertPoster' => '/(<div class="movie-poster">.*?<\/div>)/is',
        'varietyAuthor' => '/(<section class="byline">.*?<\/section>)/is',
        'varietyImage' => '/(<figure.*?<\/figure>)/is',
        'cinemagazineThumb' => '/<div class="single-thumb">(.*?)<span/is',
        'ReadwriteAbstract' => <<<'REGEXP'
/(<div class="abstract".*?</div>)/is
REGEXP
    ,
        'ReadwriteAuthor' => '/(<span class="avatar".*?)\s*?<span class="section"/is',
    );

    /**********************
     * @var array hosts
     **********************/
    public $hosts = array(
/*
        array(
            'name' => 'thequietus.com',
            'body' => '#content', // no result here
            'header' => '',
        ),*/
        array(
            'name' => 'www.kqek.com',
            'body' => '/Comment<\/font>.*?(<tr>.*?<\/tr>)/is',
            'remove' => '/(<img.*? src=".*?blackgap_gif.gif".*?)<img/is',
        ),

        array(
            'name' => array(
                'avxhome.in',
                'avxhome.se',
            ),
            'body' => array(
                '..*?article',
            ),
            'header' => '',
            'css' => array(
                'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
                'avxhome.css',
            ),
            'js' => 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',
        ),
        array(
            'name' => array(
                'www.avclub.com',
                'avclub.com'
            ),
            'body' => array(
                '*/(<img src="http:\/\/i.onionstatic.*?>)/is',
                '/(<article id="article-detail.*?<\/article>)/is',
            ),
            'css' => 'avclub.css',
            'js' => 'avclub.js',
            'header' => '',
        ),
        array(
            'name' => 'www.allmovie.com',
            'body' => array(
                '/(<div class="poster".*?<\/div>)/is',
                '/(<section class="review.*?<\/section>)/is'
            ),
            'header' => '/(<hgroup.*?<\/hgroup>)/is',
            'utf8' => false,
        ),
        array(
            'name' => 'www.philognosie.net',
            'body' => array(
                '/(<div id="contentMain.*?)<div id="leftMain">/is',
            ),
            'header' => '',
            'utf8' => true,
        ),
        array(
            'name' => 'www.alternet.org',
            'body' => array(
                '/(<div class="region region-content.*?)<!-- \/\.region -->/is',
                '/<!-- start: body -->(.*?)<!-- start: footer -->/is',
            ),
            'header' => '',
            'css' => 'alternet.css',
        ),
        array(
            'name' => 'www.dvdtalk.com',
            'body' => '/(<table border="0" cellspacing="0" cellpadding="0".*?<\/table>)\s*?<table border="0" width="100%">/is',
        ),
        array(
            'name' => 'efilmcritic.com',
            'body' => '/(<table class=\'reviewtable\'.*?<\/table>)\s*?<BR\/>\s*?<TABLE class=\'infobox\'>/is',
            'utf8' => true,
        ),
        array(
            'name' => '10kbullets.com',
            'body' => '/<table class="tborder".*?(<table class="tborder".*?<\/table>)/is',
        ),
        array(
            'name' => 'www.timeout.com',
            'body' => array(
                '/(<article class="review__article".*?<\/article>)/is',
                //                 review__article
                //'/(<div class="review__body">.*?<\/div>)/is',
               '/(<div id="mainLeft".*?<\/div>)\s*?<div id="mainRight/is',
            ) ,
            //'remove' => '/(<div id="ratingsWrapper.*?<\/div>)\s*?<div class="details" id="filmDetail">/is',
        ),
        array(
            'name' => 'www.filmabides.nl',
            'body' => '/(<div class="content-panel.*?<\/div>)\s*?<\/div>\s*?<div id="disqus_/is',
            'utf8' => false,
            'style' => '#itro_opaco{display: none;}',
            'header' => '',
            //'utf8' => true,
            //'script' => '$(function(){$("details-box-wrapper").hide();});',
            'js' => 'filmabides.js',
        ),
        array(
            'name' => 'www.filmtotaal.nl',
            'body' => '/(<div id="article".*?<\/div>)\s*?<div id="article_sidebar/is',
            'utf8' => true,
        ),
        array(
            'name' => 'www.spiritualityandpractice.com',
            'body' => '/(<div id="middleContainer".*?<\/div>)\s*?<\/td>/is',
            'utf8' => true,
        ),
        array(
            'name' => 'webwereld.nl',
            'body' => '/(<div class="articleWrapper">.*?<\/div>)\s*?<section/is',
            'header' => '',
        ),
        array(
            'name' => 'www.cinepassion.org',
            'body' => '/<body.*?>(.*?)<\/body>/is',
            'utf8' => true,
            'style' => 'table {background-color: transparent;} hr{display:none;}',
        ),
        array(
            'name' => 'mashable.com',
            'body' => '/(<article class=\'full post\'.*?<\/article>)/is',
            'header' => '',
            'remove' => '/(<aside class=\'shares.*?<\/aside>)/is',
        ),
        array(
            'name' => 'www.smashingmagazine.com',
            'body' => '/(<div class="col main.*?<\/div>)\s*?<!-- .col.main/is',
            'header' => '',
            'js' => 'smashing.js',
        ),
        array(
            'name' => 'www.wired.com',
            //'body' => '/(<div class="entry">.*?)\s*?<div id=\'social-bottom/is',
            'body' => '/(<article.*?>.*?<\/article>)/is',
            'header' => array('/<h1 id="post-title.*?>(.*?)<\/h1>/is',
                '/<h1.*?class="post-title.*?>(.*?)<\/h1>/is'),
        ),

        array(
            'name' => 'www.dvdbeaver.com',
            'body' => '/<table.*?(<table.*?<\/table>)/is',
            'utf8' => true,
            'style' => 'b{font-weight:normal;font-family:chaparral-regular}',
        ),
        array(
            'name' => array(
                'www.rogerebert.com',
                'rogerebert.suntimes.com',
            ),
            'body' => '/(<article class="pad.*?<\/article>)/is',
            'header' => array(
                '/<figcaption.*?<h1 itemprop="name">(.*?)<\/h1>/is',
                '/(<div class="primary-image">.*?<\/div>)/is',
            ),
            'logo' => 'rogerebert.png',
            'css' => 'rogerebert.css',
            'remove' => array(
                '/(<div class="whats-.*?)<\/article>/is',
                '/(<section class="share.*?<\/section)>/is',
            ),
        ),
        array(
            'name' => array(
                'variety.com',
                'www.variety.com'
            ),
            'header' => '/<!-- Article Headline -->(.*?)<div/is',
            'body' => array(
                '/(<figure.*?<\/figure>)/is',
                '/(<section class="byline">.*?<\/section>)/is',
                '/<!-- Start Article Post Content -->(.*?)<\/div/is',
            ),
            'logo' => 'variety.png',
            'css' => 'variety.css',
        ),
        array(
            'name' => 'readwrite.com',
            'body' => '/(<section itemprop=\'articleBody.*?<\/section>)/is',
            'css' => 'readwrite.css',
            'js' => 'readwrite.js',
        ),
        array(
            'name' => 'www.classicfilmguide.com',
            'body' => array(
                '/(<div class="post-thumbnail">.*?<\/div>)/is',
                //'/(<div class="pagecontent">.*?<\/div>)/is',
                '@pagecontent',
            ),
         ),
        array(
            'name' => 'filmfanatic.org',
            'body' => '/(<div class="narrowcolumn">.*?<\/div>)\s*?<div class="sidebar">/is',
            'js' => 'filmfanatic.js',
            'style' => 'img.alignleft{float:left;margin: 5px 10px 0 0}',
         ),
        array(
            'name' => 'www.efilmcritic.com',
            'body' => '/(<TABLE\s*?class=\'reviewtable\'>.*?<\/TABLE>)\s*?<BR\/>/is',
            //'utf8' => true,
        ),
        array(
            'name' => 'www.flickfilosopher.com',
            'body' => '/(<div class="entry post.*?)<!-- end \.entry/is',
            'logo' => 'flickfilosopher.png',
            'header' => '',
         ),
        array(
            'name' => array(
                'www.nytimes.com',
                'movies.nytimes.com',
                'movies2.nytimes.com',
            ),
            'body' => array(
                '/(<div id="abColumn".*?)<!--\s*?close abColumn -->/is',
                '/(<div class="columnGroup first.*?<\/div>)\s*?<!--cur: /is',
                '/(<div id="articleBody">.*?<\/div>)\s*?<!--  end #articleBody -->/is',
                '/(<div id="area-main-center-w-left">.*?<\/div>)\s*?<!-- close/is',
                '/(<span class="moviestext">.*?<\/span>)/is',
            ),
            'header' => '',
            'js' => 'nytimes.js',
            'css' => 'nytimes.css',
            'remove' => array(
                '/(<div id="articleToolsTop".*?<\/div>)\s*?<div class="articleBody">/is',
                '/(<div id="articleInline">.*?<\/div>)/is'
            ),
         ),
        array(
            'name' => 'www.epinions.com',
            'body' => '/(<div id="single_review_area">.*?)<\/td>/is',
            'utf8' => true,
         ),
        array(
            'name' => 'www.dvdverdict.com',
            'body' => '/<td style="vertical-align:.*?>(.*?)<\/td>\s*?<td style="width:/is',
            'header' => '',
            'logo' => 'dvdverdict.png',
            'css' => 'dvdverdict.css',
            'remove' => array(
                '/<\/h2>\s*?<p>(.*?)<\/p>/is',
                '/(<td width="40%.*?<\/td>)/is',
            ),
         ),
        array(
            'name' => 'www.rinkworks.com',
            'body' => '/(<div class=\'content\'>.*<\/div>)\W*?<div class=\'footer\'>/is'
         ),
        array(
            'name' => 'www.filmofiel.nl',
            'body' => '/(<div id="content">.*?)<!--/is',
            'logo' => 'filmofiel.png',
            'header' => '',
            'js' => 'filmofiel.js',
         ),
        array(
            'name' => 'www.tcm.com',
            'body' => '/(<div id="contentbody.*)<div id="gigyasharetwo/is'
         ),
        array(
            'name' => 'www.reelviews.net',
            'body' => array(
                '/<!-- Alt Left Column -->\s*?(<div id="right_col.*<\/div>)\s*?<!-- Alt Left Column -->\s*?<div id="left_col/is',
                '/(<div class="container.*?<\/div>)\w?<footer>/is',
            ),
            'remove' => '/<div id="movie-inset">.*?<\/div>/is',
            'utf8' => true,
            'style' => 'hr{display:none;}h1:not([id=review-title]){display:none;}',
            'logo' => 'reelviews.gif',
         ),
        array(
            'name' => array('cinema.nl','www.cinema.nl'),
            'body' => '/(<div class="column col-larger darkblue">.*?<\/div>)\s*?<div class="column col-medium/is',
            'remove' => '/<table class="cols3".*?<\/table>/is',
        ),
        array(
            'name' => 'www.reelviews.net',
            'body' => '/<!-- Alt Left Column -->(.*?)<!-- Alt Left Column/is'
         ),
        array(
            'name' => 'www.abusdecine.com',
            'body' => '/<div id="liensfiche">.*?<\/div>.*?(<p class="gras">.*?<\/p>)/is',
            'utf8' => true,
         ),
        array(
            'name' => 'cadependdesjours.com',
            'body' => '/(<div class="content-panel">.*?)<div id="comments"/is'
         ),
        array(
            'name' => array(
                'homepages.sover.net',
                'www.sover.net',
            ),
            'body' => '/<center>\W*?(<table.*?<\/table>)/is',
            'css' => 'sovernet.css',
            'logo' => 'sovernet.JPG',
            'remove' => '/(<b>|<\/b>)/is',
         ),
        array(
            'name' => 'classicfilmguide.com',
            'body' => '/<div class="pagecontent">(.*?)<\/div>/is'
         ),
        array(
            'name' => 'www.washingtonpost.com',
            'body' => '/<!--plsfield:description-->(.*?)<\/td>/is',
            'header' => array(
                '/<!--THE HEADLINE GOES HERE-->\s*?<h2>(.*?)<\/h2>\s*?<!--THE BYLINE GOES HERE-->/is',
                '/(<table.*?<\/table>).*?<!-- End of ARTICLE head table. -->/is'
            ),
         ),
        array(
            'name' => 'cinemagazine.nl',
            'body' => '/(<div class="post-text".*?<!-- .post-text -->)/is',
            'header' => '',
        ),
    );

}
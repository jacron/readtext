<?php

/*
 * Author: jan
 * Created: 2-jun-2014
 */
include 'app/Util.php';
include 'app/hosts.php';

$root = Util::getRootUrl();
//echo php_uname('n');
switch(php_uname('n')) {
    case 'utrecht.denit.net':
        $root .= '/readtext';
        break;
}
$hosts = new Hosts();
$scripthref = "javascript: var href=document.location.href, "
        . "link=encodeURIComponent(href); document.location.href='"
        . $root . "/?link=' + link;"

?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/style.css?v=1">
        <title>Readtext - Usage</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="container readtext">
            <div class="main">
                <h1>Readtext</h1>
                <p class="intro">
                    Breng websites terug tot de teksten die je wilt lezen.

                </p>
                <p>
                    Eenvoud loont. Gebruikt u <a href="http://help.getpocket.com/">Pocket</a> ook graag gewoon voor op uw PC? Dan is deze tool iets voor u!
                </p>
                <p>
                    Sleep onderstaande link naar uw bladwijzerbalk. Klik voortaan op
                    <strong>read</strong> om een website
                    onmiddellijk om te zetten naar eenvoudige en leesbare tekstweergave.
                    Bijv. deze
                    <a href="http://variety.com/1994/film/reviews/interview-with-the-vampire-1200439504/">recensie</a>
                    op Variety.com, of deze
                    <a href="http://cinemagazine.nl/la-voce-della-luna-1990-recensie/">recensie</a>
                    op cinemagazine.nl.
                </p>
                <fieldset>
                    <legend>Bookmarklet - sleep mij naar de bladwijzerbalk!</legend>
                        <a href="<?=$scripthref?>">read</a>
                </fieldset>

                <p>
                    Speciaal voor filmrecensies ontwikkeld, werkt <strong>Readtext</strong>
                    op de eerste plaats goed met de bekende sites waar filmrecensies op staan.
                    Sites die niet in het lijstje staan worden soms nog verrassend goed aangepast.
                </p>
                <p>
                    <em>Technische beschrijving</em>: een lijst wordt bijgehouden met gegevens voor bekende
                    sites. Onbekende sites krijgen een standaardbehandeling. Met reguliere expressies
                    wordt een vorm van screenscraping toegepast, zodat alleen de tekst van het artikel
                    verschijnt. Soms worden speciale elementen buiten deze inhoud toegevoegd, bijv. een logo. Soms worden gedeelten
                    uit het artikel verwijderd, bijv. een blokje voor sociale media. Om de verwijzingen in links en afbeeldingen
                    te corrigeren wordt javascript ingezet. Verder wordt styling aangepast via css.
                    Hard gecodeerde styling (font, bg-color, style attributen) wordt verwijderd.
                </p>
                <p>
                    <em>Suggesties voor de toekomst</em>: zorgen dat de gebruiker haar eigen fijnregeling
                    voor de weergave in kan stellen, zoals achtergrondkleur, regelafstand, lettergrootte.
                </p>
                <p>
                    Voor en na:
                </p>
                <img src="images/cinemagazine.png">
                <br><br>
                <img src="images/cinemagazine readtext.png">
                <p>
                    Ondersteunde sites:
                </p>
                <ul>
                    <?php foreach ($hosts->hosts as $host) {
                        $name = is_array($host['name'])? $host['name'][0] : $host['name'];
                    ?>
                    <li><?=$name?></li>
                    <?php } ?>
                </ul>

            </div>
        </div>
    </body>
</html>



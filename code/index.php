<?php
function count_border($osm_filename) {
    $dom = new DOMDocument;
    $dom->load($osm_filename);
    return $dom->getElementsByTagName('node')->length;
}

$baseurl = 'http://passportcontrol.net';
$sitename = 'PassportControl.net';
$description = 'Complete border crossing guide for All travellers. '. count_border('border.osm') .' border informations available!';
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo $baseurl ?>/" rel="canonical" />
        <link rel="stylesheet" href="index.css" type="text/css">
        <link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css" type="text/css">
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList"></script>
        <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script>
        <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
        <meta name="description" content="<?php echo $description ?>" />
        <meta property="og:site_name" content="<?php echo $sitename ?>" />
        <meta property="og:title" content="<?php echo $sitename ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="<?php echo $baseurl ?>/Aiga_immigration_inv.png" />
        <meta property="og:url" content="<?php echo $baseurl ?>/" />
        <meta property="og:description" content="<?php echo $description ?>" />

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        
        <title><?php echo $sitename ?> [BETA]</title>
    </head>
    <body>
        <header>
            <h1 class="title"><img class="logo" src="Aiga_immigration.svg">&nbsp;&nbsp;<?php echo $sitename ?> <span style="font-size: 0.4em;">BETA</span></h2>
            <h2 class="subtitle"><?php echo $description ?></h3>
            <nav>
                <ul>
                    <li><a href="https://planet.passportcontrol.net/pbf/">Planet.osm Mirror (Tokyo)</a></li>
                </ul>
            </nav>
        </header>
        <div id="mainmap"><div class="editor">editor</div><div id="popup"></div></div>
        
        <script src="index.js" type="text/javascript"></script>
    </body>
</html>

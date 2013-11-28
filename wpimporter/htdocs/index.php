<?php require_once('..'.DIRECTORY_SEPARATOR.'config/config.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Wordpress Exporter</title>
        <!--link href="http://fonts.googleapis.com/css?family=Source+Code+Pro:200,400,500,700,900" rel="stylesheet" type="text/css"-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="css/global.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <div id='#body'>
            <h1>Bienvenue dans l'exporteur Typo3 -> Wordpress</h1>
            <h2>Choisissez la section à importer : </h2>
            <div>
                <a href='/?section=<?php echo JEUX_STRATEGIE_COM ?>' class='btn btn-primary'>Jeux-stratégie.com</a>
                <a href='/#section=<?php echo STARCRAFT_2_FRANCE ?>' class='btn btn-primary disabled'>Starcraft 2</a>
                <a href='/#section=<?php echo STRATEGIUM_ALLIANCE ?>' class='btn btn-primary disabled'>Stratégium</a>
                <a href='/#section=<?php echo STRATEGIE_39_45 ?>' class='btn btn-primary disabled'>39-45</a>
                <a href='/#section=<?php echo AGES_STRATEGIES ?>' class='btn btn-primary disabled'>Ages stratégies</a>
                <a href='/#section=<?php echo AOE_ALLIANCE ?>' class='btn btn-primary disabled'>Age of empire alliance</a>
                <a href='/#section=<?php echo SCA ?>' class='btn btn-primary disabled'>SCA</a>
                <a href='/#section=<?php echo W3 ?>' class='btn btn-primary disabled'>W3</a>
                <a href='/#section=<?php echo WOW ?>' class='btn btn-primary disabled'>WOW</a>
            </div>
            <br/>

            <?php
                try{

                    if (isset($_GET['action'] )) {
                        $action = $_GET['action'];
                        WPImporterController::$action();

                    } else if (isset($_GET['migration'])) {
                        $action = $_GET['migration'];
                        MigrationToolsController::$action();

                    } else if (isset($_GET['section'])) {
                        WPImporterController($_GET['section']);
                    }

                }catch(Exception $e){
                    echo $e->getMessage();
                }
            ?>
        </div>

        <script src="//code.jquery.com/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>




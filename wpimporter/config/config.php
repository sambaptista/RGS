<?php

/****************************************************************
	GLOBAL
*****************************************************************/
set_time_limit(200000000);





/****************************************************************
	CHEMINS
*****************************************************************/

$vendor_path = '..'.DIRECTORY_SEPARATOR.'vendor';
$htmlpurifier_path = '..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'htmlpurifier'.DIRECTORY_SEPARATOR.'library';
$module_path = '..'.DIRECTORY_SEPARATOR.'module';
$wp_path = $module_path.DIRECTORY_SEPARATOR.'wp';
$controllers_path = $module_path.DIRECTORY_SEPARATOR.'controllers';

ini_set('include_path',     ini_get('include_path').
                            PATH_SEPARATOR.$vendor_path.
                            PATH_SEPARATOR.$htmlpurifier_path.
                            PATH_SEPARATOR.$controllers_path.
                            PATH_SEPARATOR.$module_path.
                            PATH_SEPARATOR.$wp_path.
                            PATH_SEPARATOR.$controllers_path
                           );

// path to wordpress
define('ABS_PATH', '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'wordpress'.DIRECTORY_SEPARATOR.'htdocs');


define('LOG_PATH', $_SERVER['DOCUMENT_ROOT'].'/data/logs/');


/****************************************************************
    Required files that need explicit include (those that aren't loaded by call to a class)
*****************************************************************/

require_once('settings.php');
require_once('HTMLPurifier.auto.php');


// wordpress classes
require_once(ABS_PATH.'/wp-load.php');
require_once(ABS_PATH.'/wp-includes/post.php');
require_once(ABS_PATH.'/wp-includes/meta.php');
require_once(ABS_PATH.'/wp-admin/includes/image.php');





/****************************************************************
	SECTEURS RGS
*****************************************************************/

define("JEUX_STRATEGIE_COM", 1504);
define("STARCRAFT_2_FRANCE", 6631);
define("STRATEGIUM_ALLIANCE", 148);
define("STRATEGIE_39_45", 57);
define("AGES_STRATEGIES", 216);
define("AOE_ALLIANCE", 2222);
define("SCA", 3892);
define("W3", 1090);
define("WOW", 1);


// ID, domaine and news category of the website to import
DEFINE('SECTION_RGS', JEUX_STRATEGIE_COM);
DEFINE('NOM_DE_DOMAINE', 'http://www.jeux-strategie.com/');
DEFINE('NEWS_CATEGORY', 12);






/****************************************************************
	CHAMPS PERSONNALISES DANS WORDPRESS
*****************************************************************/

// games
define('ACF_FJ_DATE_DE_SORTIE', 'field_51c716e645bcf');
define('ACF_FJ_SITES', 'field_51c7160b1f47c');

// tests
define('ACF_FT_LES_PLUS', 'field_51ba2e266d141');
define('ACF_FT_LES_MOINS', 'field_51ba2e6f6d143');
define('ACF_FT_NOTES', 'field_51ba3a24d6110');
define('ACF_FT_CONFIG', 'field_51ba3b75c000d');

// linked games
define('ACF_JEUX_LIES', 'field_51b480bafa00f'); // define('ACF_JEUX_LIES_FIELD', 'field_51b48267d3bf6'); 

// redactional content
define('ACF_CONTENU_REDACTION', 'field_51bb867b1aed0');






/****************************************************************
	Constraints for import in wordpress
*****************************************************************/

define('NB_NEWS', 300000);
define('NB_GALERIES', 0);
define('NB_IMG_PAR_GALERIE', 3);
define('NB_PAGES', 30000);

//define('NB_NEWS', 9999999999);
//define('NB_GALERIES', 999999999999999999999);
//define('NB_IMG_PAR_GALERIE', 3);
//define('NB_PAGES', 9999999999);












/****************************************************************
    Autoload
*****************************************************************/

function autoload($className){
    $include_paths = explode(':', ini_get('include_path') );
    $extensions = array('','.inc', '.class');

    if (!class_exists($className, false) ){
        foreach($include_paths as $include_path){
            foreach($extensions as $extension){
                if(file_exists( $include_path."/".$className.$extension.'.php')){
                    require_once($className.$extension.'.php');
                }
            }
        }
    }
}
spl_autoload_register('autoload');
$log = Log::getInstance();


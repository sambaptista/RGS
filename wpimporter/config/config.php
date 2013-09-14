<?php

/****************************************************************
	GLOBAL
*****************************************************************/
set_time_limit(2000000000000000000);





/****************************************************************
	CHEMINS
*****************************************************************/

// chemins d'inclusion, : pour séparateur mac, ; pour séparateur windows
ini_set('include_path',ini_get(	'include_path').PATH_SEPARATOR.
								'classes'.PATH_SEPARATOR.
								'htmlpurifier'.DIRECTORY_SEPARATOR.'library'.PATH_SEPARATOR.
								'classes'.DIRECTORY_SEPARATOR.'wp'.PATH_SEPARATOR.
								'classes'.DIRECTORY_SEPARATOR.'typo'
							   );

// chemin pour wordpress
define('ABS_PATH', '..'.DIRECTORY_SEPARATOR'.wordpress');







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


// Id, domaine et catégorie de news du site à importer
DEFINE('SECTION_RGS', JEUX_STRATEGIE_COM);
DEFINE('NOM_DE_DOMAINE', 'http://www.jeux-strategie.com/');
DEFINE('NEWS_CATEGORY', 12); 






/****************************************************************
	CHAMPS PERSONNALISES DANS WORDPRESS
*****************************************************************/

// fiches de jeu
define('ACF_FJ_DATE_DE_SORTIE', 'field_51c716e645bcf'); 
define('ACF_FJ_SITES', 'field_51c7160b1f47c');

// fiches de test
define('ACF_FT_LES_PLUS', 'field_51ba2e266d141'); 
define('ACF_FT_LES_MOINS', 'field_51ba2e6f6d143');
define('ACF_FT_NOTES', 'field_51ba3a24d6110');
define('ACF_FT_CONFIG', 'field_51ba3b75c000d');

// Jeux liés
define('ACF_JEUX_LIES', 'field_51b480bafa00f'); // define('ACF_JEUX_LIES_FIELD', 'field_51b48267d3bf6'); 

// contenu rédactionnel en général
define('ACF_CONTENU_REDACTION', 'field_51bb867b1aed0'); 






/****************************************************************
	CONTRAINTES D'IMPORTATION DANS WORDPRESS
*****************************************************************/

define('NB_NEWS', 300000);
define('NB_GALERIES', 0);
define('NB_IMG_PAR_GALERIE', 3);
define('NB_PAGES', 30000);

//define('NB_NEWS', 9999999999);
//define('NB_GALERIES', 999999999999999999999);
//define('NB_IMG_PAR_GALERIE', 3);
//define('NB_PAGES', 9999999999);






























?>
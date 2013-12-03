<?php

class WPImporterController
{
    public $teo; // typo exporter object
    private static $urls = array();

    public function __construct($section)
    {

        switch ($section) {
        case JEUX_STRATEGIE_COM :
            new JeuxStrategieController();
            break;
        case STARCRAFT_2_FRANCE :
            new Starcraft2Controller();
            break;
        case STRATEGIUM_ALLIANCE :
            new StrategiumController();
            break;
        case STRATEGIE_39_45 :
            new Strategie3945Controller();
            break;
        case AGES_STRATEGIES :
            new AgesStrategiesController();
            break;
        case AOE_ALLIANCE :
            new AoeAllianceController();
            break;
        case SCA :
            new ScaController();
            break;
        case W3 :
            new W3Controller();
            break;
        case WOW :
            new WowController();
            break;
        }

//        fr(Page::$link_replacement);
//        echo '*************';
//        fr(Page::$link_domain);
//
//        self::replaceLinks();

        f('Taille de la BD : ' . RGSBD::getInstance()->displayBDSize(), ' weight2');
        f('*** Importation terminée ****', 'weight4 success');
    }




    /****************************************************************
            Tests
    *****************************************************************/

    public static function testimport()
    {
        fr('test import');
        //$fiche = Game::create('jeu test');
        $fiche = Game::findById(2306);


        // date de sortie
        $field_key = ACF_FJ_DATE_DE_SORTIE;
        update_field( $field_key, '123132123', $fiche->ID );

        // contenu
        $field_key = "field_51bb867b1aed0";
        $value = get_field($field_key, $fiche->ID);

        $value[] = array(
            "texte" => "import text",
            "acf_fc_layout" => "contenu_sur_une_colonne"
        );
        $value[] = array(
            "colonne_de_gauche" => "import gauche",
            "colonne_de_droite" => "import droite",
            "acf_fc_layout" => "contenu_sur_deux_colonnes"
        );


        $res = update_field($field_key, $value, $fiche->ID);

        fr($res);

    }

}

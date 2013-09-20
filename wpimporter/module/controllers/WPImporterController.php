<?php

class WPImporterController
{
    public $log;
    public $teo; // typo exporter object


    public function __construct()
    {
        $this->log = $log = Log::getInstance();
        $this->teo = new TypoExporterController($log);

        f('Début Importation', 'weight4');

        exit();

        $bdd = RGSBD::getInstance();
        $bdd->resetWP();



        $this->importeFichesJeu();
        $this->importeTests();
        $this->importeGalleries();
        $this->importeNews();
        $this->importePages();


        echo '<pre>';
        print_r(Page::$link_replacement);
        echo '*************';
        print_r(Page::$link_domain);
        echo '</pre>';

        Page::replaceLinks();


        f('Taille de la BD : '.$bdd->displayBDSize(), ' weight2');
        $this->log->f('*** Importation terminée ****', 'weight4 success');

    }





    /****************************************************************
    Importation
     *****************************************************************/


    public function importeFichesJeu()
    {
        $ficheJeu = new FichesJeu($this->teo->fiches_de_jeu, $this->teo->genres, $this->log);
    }

    public function importeGalleries()
    {
        $galeries = new Gallery($this->teo->galeries, $this->log);
    }

    public function importeNews()
    {
        $news = new News($this->teo->news, $this->log);
    }

    public function importePages()
    {
        $pages = new Page($this->teo->arbre, $this->teo->html_externe, $this->log);
    }

    public function importeTests()
    {
        $tests = new FicheTest($this->teo->fiches_test, $this->log);
    }


    public static function testimport()
    {

        $field_key = "field_51bb867b1aed0";
        $postid = 174778;

        $value = get_field($field_key, $postid);

        echo '<pre>';
        print_r($value);
        echo '</pre>';

        $value[] = array("texte"         => "import text",
                         "acf_fc_layout" => "contenu_sur_une_colonne");

        $value[] = array("colonne_de_gauche" => "import gauche",
                         "colonne_de_droite" => "import droite",
                         "acf_fc_layout"     => "contenu_sur_deux_colonnes");

        update_field($field_key, $value, $postid);


    }


}

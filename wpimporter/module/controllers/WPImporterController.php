<?php

class WPImporterController
{
    public $teo; // typo exporter object
    private static $urls = array();
    protected $errors = array();

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

//        fr(Post::$link_replacement);
//        echo '*************';
//        fr(Post::$link_domain);
//
//        self::replaceLinks();

        f('Taille de la BD : ' . RGSBD::getInstance()->displayBDSize(), ' weight2');

        fr(count(Gallery::$urls));
        fr(Gallery::$urls);

        if (count($this->errors) > 0 ){
            f('*** Importation terminée avec des erreurs ****', 'weight4 error');
            fr($this->errors, 'weight4 error');
        } else {
            f('*** Importation terminée ****', 'weight4 success');
        }
    }




    /****************************************************************
            Tests
    *****************************************************************/

    public static function testimport()
    {
       //fr(Gallery::fetch_media('uploads/media/STR.png', 'turlututu.png'));

        //$fiche = Game::create('jeu test');
        $news = News::findById(34039);

        $content = '
            <pre class=""><span style="display:inline-block;width:100px">9 s 	 -&gt;</span> <p>Dépêche publiée sur le(s) site(s) suivant(s):<br><a href="http://www.jeux-strategie.com/">Jeux-Strategie.com</a><br>Postée par: scherlock</p>
            <p><br>Détenir le pouvoir absolu est votre but dans ce jeu dont nous vous avions déjà parlé il y a quelques temps. Super Power 2 se situ dans un environnement contemporain ou il vous faudra gérer une crise mondiale pour vous imposer par des moyens militaires et diplomatiques. <br>
            Aujourd’hui, Golem Labs, nous propose d’en découvrir plus grâce à une jolie vidéo de présentation accès sur la découverte de l’interface du jeu et sur la stratégie autour de la carte du globe terrestre.<br><a href="http://www.gamershell.com/download_5497.shtml">Télécharger la vidéo sur GamerShell.com (2,3 Mo)</a> <br></p><br></pre>';


        $values = array("texte" => $content, "acf_fc_layout" => "contenu_sur_une_colonne");
        update_field(ACF_CONTENU_REDACTION, array($values), $news->ID);

        exit();

        $query = new WP_Query(
            array(
                'post_type' => 'page',
                'meta_key' => 'typo_id',
                'meta_value' => 1523)
        );


        fr(count($query->posts));
        foreach($query->posts as $post){
            fr($post);
            $meta_values = get_post_meta( $post->ID, 'typo_id');
            fr($meta_values);
        }

        exit();

        // typo id
        $field_key = ACF_TYPO_ID;
        update_field( $field_key, '123132123', $fiche->ID );


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
        $value[] = array(
            "colonne_de_gauche" => "import gauche 2",
            "colonne_de_droite" => "import droite 2",
            "acf_fc_layout" => "contenu_sur_deux_colonnes"
        );

        $res = update_field($field_key, $value, $fiche->ID);
    }

    //	public function testPurifier()
    //	{
    //		$dirty_html = '<span></span><p style="background:black" class="align-center"><font size="5" color="#0066ff"><strong>Dawn of War 2 </strong></font></p><p class="align-center"><font size="5" color="#0066ff"><strong><em>- E-Sport -</em></strong></font></p><p class="align-center"><strong><em><font size="5" color="#0066ff"></font></em></strong></p><p class="align-left"><font color="#000000">Dans ce dossier, seront regroupées toutes les activités E-Sport spécifiques à Dawn of War 2. Notamment nos tournois francophones "Les Cups of Coffee", la sélection de léquipe de France, le ladder français et le championnat international par équipe.</font></p><p class="align-left">Les tableaux seront mis à jour réguièrement.</p><p class="align-left">Notre section E-sport</p><p class="align-left">Notre section internationale</p><p class="align-left"></p>';
    //		$config = HTMLPurifier_Config::createDefault();
    //		$config->set('AutoFormat.RemoveEmpty', true);
    //		$config->set('HTML.ForbiddenAttributes', array('class', 'style', 'align', 'dir') );
    //		$config->set('HTML.ForbiddenElements', array('font', 'strong', 'em', 'script', 'b', 'u') );
    //
    //		$purifier = new HTMLPurifier($config);
    //		$clean_txt = $purifier->purify($dirty_html);
    //		echo htmlentities($clean_txt);
    //	}
}

<?php

class JeuxStrategieController extends WPImporterController
{
    public static $fiches_orphelines = array(
        '100000' => 'Age Of Wonders : Shadow Magic', '100001' => 'Battle Mages', '100002' => 'Battle Realms', '100003' => 'Chaos League',
        '100004' => 'Dominions 2 ', '100005' => 'Etherlords II', '100006' => 'KOHAN : Immortal Sovereigns', '100007' => 'La Bataille pour la Terre du Milieu',
        '100008' => 'La Bataille pour la Terre du Milieu II', '100009' => 'La Bataille pour la Terre du Milieu II : l AvËnement du Roi-Sorcier ',
        '100010' => 'Spellforce', '100011' => 'The Battle for Wesnoth', '100012' => 'Warlords Battlecry II', '100013' => 'Warlords Battlecry III',
        '100014' => 'Command and Conquer 3: La fureur de Kane', '100015' => 'Conquest: Frontier Wars', '100016' => 'Empereur: la Bataille pour Dune',
        '100017' => 'Forged Alliance', '100018' => 'Ground Control 2 : OpÈration Exodus ', '100019' => 'HomeWorld 2', '100020' => 'Massive Assault',
        '100021' => 'Project Visitor', '100022' => 'Sins of a Solar Empire : Entrenchment', '100023' => 'Star Warsô Galactic Battlegroundsô',
        '100024' => 'UFO : Aftermath', '100025' => 'Warhammer 40 000 : Dawn of War', '100026' => 'Warhammer 40 000 : Dawn of War 2 - Chaos Rising',
        '100027' => 'Age of Kings : The Conquerors', '100028' => 'Alerte Rouge 2', '100029' => 'American Conquest : Fight Back',
        '100030' => 'Anno 1503 : le nouveau monde', '100031' => 'Chariots of War', '100032' => 'Civilization 3', '100033' => 'Command & Conquer Generals',
        '100034' => 'C&C Generals : Heure H', '100035' => 'Cossacks : European Wars', '100036' => 'Crown of the north', '100037' => 'Europa 1400',
        '100038' => 'Europa Universalis', '100039' => 'Hearts of Iron', '100040' => 'Medieval Lords', '100041' => 'Medieval Total War', '100042' => 'Montjoie',
        '100043' => 'Rise of Nations', '100044' => 'StrongHold', '100045' => 'Stronghold Crusader ', '100046' => 'The Gladiators', '100047' => 'Victoria',
        '100048' => 'War and Peace', '100049' => 'Warrior Kings', '100050' => 'Advance Wars', '100051' => 'Advance Wars 2 : black hole rising',
        '100052' => 'Advance Wars DS', '100053' => 'Airborne Assault', '100054' => 'Darwinia', '100055' => 'Final Fantasy Tactics Advance',
        '100056' => 'Football Manager 2009', '100057' => 'Impossible Creatures', '100058' => 'No Man s Land', '100059' => 'Galactic Civilisation II',
        '100060' => 'Republic', '100061' => 'Hearthstone', '100062' => 'Dark End', '100063' => 'Birth of America ', '100064' => 'Jagged Alliance 3D ',
        '100065' => 'Defenders of Ardania', '100066' => 'Majesty 2', '100067' => 'Age of Empires 3', '100068' => 'Deus Ex 3', '100069' => 'Dragon Commander',
        '100070' => 'Galactic Assault', '100071' => 'Hearts of Iron 2', '100072' => 'Jumpgate Evolution ', '100073' => 'Kingdom Under Fire II',
        '100074' => 'Rise of Immortals', '100075' => 'Supreme Ruler 1936', '100076' => 'The Settlers VI', '100077' => 'Victoria 2 : A House Divided',
        '100078' => 'Wargame Airland Battle'
    );

    public function __construct()
    {
        $resetTaxonomies = false;
        $this->teo = new TypoExporterController();
        //frx(Tools::getStructure($this->teo->arbre));
        Rgsbd::getInstance()->resetWP($resetTaxonomies, true, true);
        //Rgsbd::getInstance()->resetNews();

        exit();
        $this->insertSections();
        if ($resetTaxonomies) {
            $this->insertGenres();
        }
        $this->insertFiches();
        $this->insertPages();
        $this->insertGalleries();
        $this->insertNews();
    }

    private function insertGenres()
    {
        f('Genres', 'weight3');

        foreach ($this->teo->genres as $typoId => $genreName) {
            f($genreName, 'weight2');

            Genre::create($genreName, $typoId);
        }
    }

    private function insertSections()
    {
        Section::create('jeux-stragegie.com', 'js');
    }

    /*
    *
    *  [13903] => Array
    *  (
    *      [nomJeu] => Age of Empires Online
    *      [illustration] =>
    *      [dateSortie] => Aout 2011
    *      [developpeur] => Gas Powered Games
    *      [editeur] => Microsoft Games
    *      [site] => Age of Empires Online
    *      [note] => 10
    *      [newsType] => aoeo
    *      [illustrationGalerie] =>
    *      [galerie] =>
    *      [genre] => Array
    *          (
    *              [0] => 12
    *          )
    *  )
    *
    */
    private function insertFiches()
    {
        f('Fiches de jeu', 'weight3');

        foreach ($this->teo->fiches_de_jeu as $typoId => $data) {
            try{

                if ($data['newsType']) {
                    fr($data['newsType'], 'success');
                }
                f($data['nomJeu'], 'weight2');

                $fiche = Game::create($data['nomJeu']);
                $fiche->tagTypoId($typoId);

                $genres = array();
                foreach ($data['genre'] as $genre) {
                    $genre = Genre::findByTypoId($genre);
                    array_push($genres, $genre->ID);
                }
                $genres = array_map('intval', $genres);
                $genres = array_unique($genres);
                wp_set_object_terms($fiche->ID, $genres, Genre::$term_type);

                // sections
                $section = Section::findByName('jeux-stragegie.com');
                wp_set_object_terms($fiche->ID, (int) $section->ID, Section::$term_type);

                //$fiche->addACFFields($data);

            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
                Log::logError($e->getMessage(), __LINE__, __FILE__);
            }
        }

        // Insère les fiches orphelines (fiches qui ont du contenu associé, mais qui n'existent pas elles même)
        foreach (self::$fiches_orphelines as $virtualTypId => $data) {
            try{
                $game = Game::create($data, null, array('post_status' => 'draft'));
                $game->tagTypoId($virtualTypId);
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
                Log::logError($e->getMessage(), __LINE__, __FILE__);
            }
        }
    }

    /**
     * Va créer une page qui comporte autant de documents qu'il y a de types de contenus dans la dite page.
     * @todo : reste à exclure (à réfléchir) les types suivants lors de l'ajout :  galeries, tests et fiches de jeu (déjà fait puisque je ne les ajoute pas -> cible uniquement les types à insérer)
     * @todo : ajouter les galeries et pièces jointes, vidéo, multimedia
     */
    public function insertPages()
    {
        f('Pages', 'weight3');

        // create reference page for structure in Page menu in WP
        $homePage = Page::create('JS');

        // insertion des pages enfant
        foreach ($this->teo->arbre as $i => $data) {
            try {
                f('Page : ' . $data['id'] . ' - ' . $data['name'], 'weight2');

                if (isset($data['id_parent']) && $data['id_parent'] != JEUX_STRATEGIE_COM) {
                    $parent = Page::findByTypoId($data['id_parent']);
                } else {
                    $parent = $homePage;
                }

                $page = Page::create($data['name'], null, array('post_parent' => $parent->ID, 'post_date' => date('Y-m-d H:i:s', $data['date_creation'])));
                $page->tagTypoId($data['id']);
                $page->tagTypoURL($data['url']);
                $page->setAttachedGames($data['liaison_jeu']);
                $page->addACFFields($data['content'], $this->teo->html_externe);
                Post::addLinkReplacement($data['url'], get_permalink($page->ID));

                if (defined(NB_PAGES) && $i == NB_PAGES) {
                    break;
                }
            } catch (Exception $e) {
                array_push($this->errors, $e->getMessage());
                Log::logError($e->getMessage(), __LINE__, __FILE__);
            }
        }
    }



    public function insertGalleries()
    {

        foreach ($this->teo->galleries as $typoId => $data) {

            if (is_array($data['image']) && sizeof($data['image']) > 0) {

                $gallery = Gallery::create($data['nom'], array('post_autor' => 1));

                /****************************************************************
                Linked games
                 *****************************************************************/

                if (sizeof(self::$data['liaison_jeu']) > 0) {
                    $values = array();
                    foreach (self::$data['liaison_jeu'] as $jeu_lie) {
                        if (is_numeric($jeu_lie)) {
                            $values[] = FichesJeu::$fiches[$jeu_lie]['wp_post_id'];
                        }
                    }
                    update_field(ACF_JEUX_LIES, $values, self::$wpid);
                }

                /****************************************************************
                Save in wordpress
                 *****************************************************************/
                foreach (self::$data['image'] as $pict_id => $pict) {
                    $cheminArray = explode('/', $pict);
                    $name = array_pop($cheminArray);
                    $chemin = implode('/', $cheminArray);

                    //self::$log->f('download img :   http://www.jeux-strategie.com/'.$chemin.'/'.str_replace(' ', '%20', $name), 'weight1');
                    self::fetch_media('http://www.jeux-strategie.com/' . $chemin . '/' . str_replace(' ', '%20', $name), Tools::cleanFilename($name, time()), $data_id);

                    if ($pict_id >= NB_IMG_PAR_GALERIE) {
                        break;
                    }
                }
            }
        }
    }





    /*
    *  add a repeater row on a taxonomy!!!
    */

    //$field_key = "repeater_field";
    //$post_id = "event_123";
    //$value = get_field($field_key, $post_id);
    //$value[] = array("sub_field_1" => "Foo", "sub_field_2" => "Bar");
    //update_field( $field_key, $value, $post_id );


    /*
    *  add a flexible content row
    *  - each row needs an extra key "acf_fc_layout" holding the name of the layout (string)
    */

    //$field_key = "flexible_field";
    //$value = get_field($field_key);
    //$value[] = array("sub_field_1" => "Foo1", "sub_field_2" => "Bar1", "acf_fc_layout" => "layout_1_name");
    //$value[] = array("sub_field_x" => "Foo2", "sub_field_y" => "Bar2", "acf_fc_layout" => "layout_2_name");
    //update_field( $field_key, $value, $post_id );


    /*
    'news'	=> array(
        0 => array(
            'id' =>  => '...',
            'date_creation' =>  '...',
            'name' => '...',
            'author' => '...',
            'liaison_news' => '...',
            'content' => '...'
         )
    */
    private function insertNews()
    {
        f('Indexation genres' ,'weight3');
        News::buildGenresIndex($this->teo->fiches_de_jeu);

        f('News' ,'weight3');
        foreach($this->teo->news as $i => $data) {

            try {
                f('News n° : '.$i.' - '.$data['name'], 'weight2');

                $content = News::getCleanText($data['content']);
                $content = Page::replaceImages($content);

                $news = News::create($data['name'], null, array('post_date' => date('Y-m-d H:i:s', $data['date_creation']) ));
                $news->tagTypoId($data['id']);

                $values = array("texte" => $content, "acf_fc_layout" => "contenu_sur_une_colonne");
                update_field(ACF_CONTENU_REDACTION, array($values), $news->ID);

                $news->setAttachGames($data);

                if ($i > NB_NEWS*50) {
                    break;
                }
            } catch (Exception $e) {
                array_push($this->errors, $e->getMessage());
                Log::logError($e->getMessage(), __LINE__, __FILE__);
            }
        }
    }
}
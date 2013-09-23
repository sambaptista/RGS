<?php

class WPImporterController
{
    public $teo; // typo exporter object


    public function __construct($section)
    {

        switch ($section) {
            case JEUX_STRATEGIE_COM :
                new JeuxStrategieControler();
                break;
            case STARCRAFT_2_FRANCE :
                new Starcraft2Controler();
                break;
            case STRATEGIUM_ALLIANCE :
                new StrategiumControler();
                break;
            case STRATEGIE_39_45 :
                new Strategie3945Controler();
                break;
            case AGES_STRATEGIES :
                new AgesStrategiesControler();
                break;
            case AOE_ALLIANCE :
                new AoeAllianceControler();
                break;
            case SCA :
                new ScaControler();
                break;
            case W3 :
                new W3Controler();
                break;
            case WOW :
                new WowControler();
                break;
        }


        echo '<pre>';
        print_r(Page::$link_replacement);
        echo '*************';
        print_r(Page::$link_domain);
        echo '</pre>';

        Page::replaceLinks();

        f('Taille de la BD : '.$bdd->displayBDSize(), ' weight2');
        f('*** Importation terminée ****', 'weight4 success');
    }

















    /****************************************************************
    Importation
     *****************************************************************/


    public function buildGenresIndex()
    {
        foreach( self::$fiches as $key => $fiche)
        {
            $genres = $fiche['newsType'];
            if( isset($genres) && sizeof($genres) >0 )
            {
                foreach($genres as $genre)
                {
                    if( !empty($genre) )
                    {
                        if( !isset( self::$newsCats[$genre] ) )
                            self::$newsCats[$genre] = array( $fiche['wp_post_id'] );
                        else
                            array_push( self::$newsCats[$genre], $fiche['wp_post_id']);
                    }
                }
            }
        }
    }



    public function getPageByTypoID( $id )
    {
        foreach( self::$pages as $page)
        {
            if($page['id'] == $id)
            {
                unset($page['children']);
                return $page;
            }
        }
    }



    public static function getJeuByID($id)
    {
        return self::$fiches[$id];
    }




    public static function getJeuByWPID($id)
    {
        foreach(self::$fiches as $typoid => $fiche)
        {
            if($fiche['post_id']==$id ) return $fiche;
        }
    }



    public static function getJeuxByGameLink( $newsCategories )
    {
        $log = Log::getInstance();

        if( empty( $newsCategories ) ) return;

        $newsCategories = explode(',', $newsCategories);
        if( sizeof($newsCategories)==0 ) return;

        $games = array();
        foreach($newsCategories as $nC)
        {

            if( isset(self::$newsCats[$nC]) && sizeof(self::$newsCats[$nC])>0 )
            {
                foreach( self::$newsCats[$nC] as $newsCat)
                {
                    array_push($games, $newsCat);
                }
            }
        }

        return $games;

    }






    /** Import media from url
     *
     * @param string $file_url URL of the existing file from the original site
     * @param int $post_id The post ID of the post to which the imported media is to be attached
     *
     * @return boolean True on success, false on failure
     */

    public static function fetch_media($file_url, $new_filename, $post_id =0)
    {
        // si le fichier a déjà été importé, on retourne directement l'id
        if( isset( self::$urls[$file_url] )) {
            $message = 'Fichier utilisé à double : '.$id_fichier_local;
            error_log($message.chr(10).__LINE__.", ".__FILE__.chr(10).chr(10), 3, LOG_PATH.'/debug_error_log.txt');
            return self::$urls[$id_fichier_local];
        }

        //directory to import to
        $artDir = ABS_PATH.'/wp-content/uploads/2013/05/';

        //if the directory doesn't exist, create it
        if(!file_exists(ABSPATH.$artDir)) {
            mkdir(ABSPATH.$artDir);
        }

        //rename the file... alternatively, you could explode on "/" and keep the original file name
        $ext = array_pop(explode(".", $file_url));

        if (@fclose(@fopen($file_url, "r"))) { //make sure the file actually exists
            copy($file_url, ABSPATH.$artDir.$new_filename);

            $siteurl = get_option('siteurl');
            $file_info = getimagesize(ABSPATH.$artDir.$new_filename);

            //create an array of attachment data to insert into wp_posts table
            $artdata = array();
            $artdata = array
            (
                'post_author' => 1,
                'post_date' => current_time('mysql'),
                'post_date_gmt' => current_time('mysql'),
                'post_title' => $new_filename,
                'post_status' => 'inherit',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)),
                'post_modified' => current_time('mysql'),
                'post_modified_gmt' => current_time('mysql'),
                'post_parent' => $post_id,
                'post_type' => 'attachment',
                'guid' => $siteurl.'/'.$artDir.$new_filename,
                'post_mime_type' => $file_info['mime'],
                'post_excerpt' => '',
                'post_content' => ''
            );

            $uploads = wp_upload_dir();
            $save_path = $uploads['basedir'].'/2013/05/'.$new_filename;

            //insert the database record
            $attach_id = wp_insert_attachment( $artdata, $save_path, $post_id );

            // mise en mémoire pour éviter de réimporter le fichier ultérieurement
            self::$urls[$id_fichier_local] = $attach_id;


            //generate metadata and thumbnails
            if ($attach_data = wp_generate_attachment_metadata( $attach_id, $save_path)) {
                wp_update_attachment_metadata($attach_id, $attach_data);
            }

            //optional make it the featured image of the post it's attached to
            $rows_affected = $wpdb->insert($wpdb->prefix.'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));

            return $attach_id;

        }
        else {
            return false;
        }

        return true;
    }




















    /**
     * Nettoie le code des artifices html
     * @param $content l'html sale qu'il faut nettoyer
     */

    public function createCleanPage( $element )
    {
        if( isset($element['content']['contenu_page']) && is_array($element['content']['contenu_page']))
        {
            $dirty_html = '';
            foreach($element['content']['contenu_page'] as $contenu)
            {
                if( isset($contenu['contenu'])){
                    if(is_array($contenu['contenu'])){
                    }else
                        $dirty_html .= $contenu['contenu'];
                }
            }

            if($dirty_html != '')
            {
                $clean_html = Page::cleanHtml($dirty_html);

                $filename_dirty = 'output/html_sale/'.$element['id'].'-'.Tools::cleanFilename($element['name']).'.html';
                $filename = 'output/html_propre/'.$element['id'].'-'.Tools::cleanFilename($element['name']).'.html';

                $this->log->p('created '. $filename, 'weight1');

                file_put_contents($filename_dirty, $dirty_html);
                file_put_contents($filename, $clean_txt);
            }
        }
    }







    /**
     * Nettoie l'html des ses artifices
     * @param $content l'html sale qu'il faut nettoyer
     */

    public static function cleanHtml($dirty_html)
    {
        if($dirty_html != '')
        {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('HTML.ForbiddenAttributes', array('class', 'style', 'align', 'target'));
            $config->set('HTML.ForbiddenElements', array('font', 'strong', 'em', 'script', 'b'));
            $config->set('AutoFormat.AutoParagraph', true);
            $config->set('AutoFormat.AutoParagraph', true);
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
            $config->set('AutoFormat.RemoveSpansWithoutAttributes', true);

            $purifier = new HTMLPurifier($config);
            $clean_txt = $purifier->purify($dirty_html);
            $clean_txt = preg_replace('#(<br(( +)|)(\/|)(( +)|)>)+#i', '<br />', $clean_txt);
            $clean_txt = preg_replace('!^<p>(.*?)</p>$!i', '$1', $clean_txt);
            $clean_txt = str_replace('<div', '<p', $clean_txt);
            $clean_txt = str_replace('</div', '</p', $clean_txt);
            $clean_txt = str_replace('<h1', '<h2', $clean_txt);
            $clean_txt = str_replace('</h1', '</h2', $clean_txt);

            $patterns = array ('/<p>([^(<\/p>)]*)<p>/ms','/<\/p>([^(<p>)]*)<\/p>/ms');
            $replace = array ('<p>\1</p><p>','</p><p>\1</p>');
            $clean_txt = preg_replace($patterns,$replace,$clean_txt);

            $patterns = array ('/<p>([^(<\/p>)]*)<p>/ms','/<\/p>([^(<p>)]*)<\/p>/ms');
            $replace = array ('<p>\1</p><p>','</p><p>\1</p>');
            $clean_txt = preg_replace($patterns,$replace,$clean_txt);


            // indexe tous les urls -> peu être supprimé en prod
            $xmlDoc = new DOMDocument();
            $xmlDoc->loadHTML( $clean_txt );
            $a = $xmlDoc->getElementsByTagName('a');

            for( $i=0; $i< $a->length; $i++)
            {
                $link = $a->item($i)->getAttribute('href');
                if( strpos( $link, NOM_DE_DOMAINE ) !== false )
                    array_push( self::$link_domain, $link );
            }

            return $clean_txt;
        }
        return '';
    }






    public static function addLinkReplacement($url1, $url2)
    {
        array_push( Page::$link_replacement,
            array(
                'url_typo' => $url1,
                'url_wp' => $url2
            )
        );
    }







    /**
     * Va chercher toutes les images contenues dans l'html et les remplace par des images importées dans wp avec l'url de wp
     * @param $content l'html comportant les images
     * @return le contenu dont les urls des images ont été remplacées
     */

    public static function replaceImages($content)
    {
        if(strlen($content) < 5 && !strpos($content, '<img') ) return $content;

        // on récupère toutes les images
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadHTML( $content );
        $images = $xmlDoc->getElementsByTagName('img');

        // pareil mais avec xpath // ajouter <root> autour de $content sinon le parsage marche pas en xml.
        //		$path = new DOMXPath($xmlDoc);
        //		$chemin = "//img[@src]";
        //		$images = $path->query($chemin);

        $imagesUrls = array();
        for( $i=0; $i< $images->length; $i++)
        {
            $cheminArray = explode('/', $images->item($i)->getAttribute('src') ); // séparre l'url dans un tableau selon les /
            $name = array_pop($cheminArray); // récupère le nom du fichier et l'enlève du tableau. On va s'en servir pour le nettoyer et qu'il soit propre dans wp
            $chemin = implode('/', $cheminArray); // reconstitue l'url sans le nom du fichier au bout

            $attach_id = Gallery::fetch_media( 	$chemin.'/'.str_replace(' ', '%20', $name), Tools::cleanFilename($name, $time) ); // ajoute l'image via wp
            $new_img_url = wp_get_attachment_url($attach_id); // récupère l'url de l'image
            $content = str_replace( $images->item($i)->getAttribute('src'), $new_img_url, $content); // remplace la veille url pointant sur typo par celle pointant vers le nouveau fichier
        }

        return $content;
    }





    public static function replaceLinks()
    {
        $bdd = new PDO('mysql:host='.HOST.';dbname='.BD_NAME_WP.'', ''.LOGIN.'', ''.PASSWORD.'', array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
        foreach( self::$link_replacement as $link)
        {
            $sql = "update wp_posts set post_content = replace(post_content, ?, ?)";
            $req = $bdd->prepare($sql);
            $req->execute( array($link['url_typo'], $link['url_wp']));
        }
    }














    /****************************************************************
            Tests
    *****************************************************************/




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

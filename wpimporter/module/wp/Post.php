<?php

class Post
{
    public static $post_type = 'post';
    public $ID;
    public $post_object;

    /**
     *  Référence tous les liens différents du domaine, pour voir combien de liens pointent vers des pages du réseau. si le tableau est plus grand, c'est que certaines urls pointent nul part
     */
    public static $link_domain = array();

    /**
     *  A chaque produit inséré, on ajoute l'url typo et la nouvelle url page afin de remplacer dans l'html les liens et éviter un maximum de redirections
     *	Pré-rempli avec les liens vers l'accueil
     */
    public static $link_replacement = array(
        //		array(
        //			'url_typo' => NOM_DE_DOMAINE,
        //			'url_wp ' => '/'
        //		)
        //		,
        //		array(
        //			'url_typo' => str_replace('http://', '', NOM_DE_DOMAINE),
        //			'url_wp ' => '/'
        //		)
    );

    protected function __construct($post_object)
    {
        $this->post_object = $post_object;
        $this->ID = $post_object->ID;
    }

    public static function findById($id)
    {
        if (!isset($id)) {
            return null;
        }
        $post_object = WP_Post::get_instance($id);

        return self::newClass($post_object);
    }

    protected static function newClass($post_object)
    {
        $class = get_called_class();

        return new $class($post_object);
    }

    protected static function getPostType()
    {
        $class = get_called_class();

        return $class::$post_type;
    }

    public static function create($name, $post_meta = null, $params = null)
    {
        if (empty($name)) {
            $message = self::getPostType() . " " . $name . ", Not created, no name given";
            throw new Exception($message);
        }

        $localParams = array('post_title' => $name, 'post_type' => self::getPostType());

        if (is_array($params)) {
            $localParams = array_merge($localParams, $params);
        }
        if (!isset($localParams['post_status'])) {
            $localParams['post_status'] = 'publish';
        }
        if (!isset($localParams['post_date'])) {
            $date = new DateTime();
            $localParams['post_date'] = $date->format('Y-m-d H:i:s');
        }

        $ID = wp_insert_post($localParams);

        if ($ID == 0) {
            $message = self::getPostType() . " : " . $name . ", Not inserted";
            Log::logError($message, __LINE__, __FILE__);
            throw new Exception($message);
        }

        if (isset($post_meta) && is_array($post_meta) && count($post_meta) > 0) {
            foreach ($post_meta as $meta => $value) {
                update_post_meta($ID, $meta, $value);
            }
        }

        return self::findById($ID);
    }

    public static function findByTypoId($id)
    {
        if (!is_numeric($id) && $id <= 0) {
            return null;
        }

        $query = new WP_Query(array(
                'post_type' => self::getPostType(), 'meta_key' => 'typo_id', 'meta_value' => $id
            ));

        if (is_array($query->posts) && count($query->posts) == 1) {
            return self::newClass($query->posts[0]);
        } else {
            return null;
        }
    }

    public function tagTypoId($id)
    {
        update_field(ACF_TYPO_ID, $id, $this->ID);
    }

    public function tagTypoUrl($url)
    {
        update_field(ACF_TYPO_URL, $url, $this->ID);
    }

    public function setAttachedGames($elements, $wpid = false)
    {
        if (sizeof($elements) > 0) {
            $games = array();
            foreach ($elements as $id) {
                if (!empty($id) && is_numeric($id) && $id > 0) {
                    if (!$wpid) {
                        $game = Game::findByTypoId($id);
                        $games[] = $game->ID;
                    } else {
                        $games[] = $id;
                    }
                }
            }
            update_field(ACF_JEUX_LIES, $games, $this->ID);
        }
    }

    public function addAttachedGame($game)
    {
        update_field(ACF_JEUX_LIES, array($game->ID), $this->ID);
    }



    /**
     * Nettoie le code des artifices html
     *
     * @param $element Contient l'html à nettoyer
     */
    public function createCleanPage($element)
    {
        if (isset($element['content']['contenu_page']) && is_array($element['content']['contenu_page'])) {
            $dirty_html = '';
            foreach ($element['content']['contenu_page'] as $contenu) {
                if (isset($contenu['contenu'])) {
                    if (is_array($contenu['contenu'])) {
                    } else {
                        $dirty_html .= $contenu['contenu'];
                    }
                }
            }

            if ($dirty_html != '') {
                $clean_html = Page::cleanHtml($dirty_html);

                $filename_dirty = 'output/html_sale/' . $element['id'] . '-' . Tools::cleanFilename($element['name']) . '.html';
                $filename = 'output/html_propre/' . $element['id'] . '-' . Tools::cleanFilename($element['name']) . '.html';

                file_put_contents($filename_dirty, $dirty_html);
                file_put_contents($filename, $clean_html);
            }
        }
    }

    /**
     * Nettoie l'html des ses artifices
     *
     * @param $dirty_html content l'html sale qu'il faut nettoyer
     */

    public static function cleanHtml($dirty_html)
    {
        if ($dirty_html != '') {
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

            $patterns = array('/<p>([^(<\/p>)]*)<p>/ms', '/<\/p>([^(<p>)]*)<\/p>/ms');
            $replace = array('<p>\1</p><p>', '</p><p>\1</p>');
            $clean_txt = preg_replace($patterns, $replace, $clean_txt);

            $patterns = array('/<p>([^(<\/p>)]*)<p>/ms', '/<\/p>([^(<p>)]*)<\/p>/ms');
            $replace = array('<p>\1</p><p>', '</p><p>\1</p>');
            $clean_txt = preg_replace($patterns, $replace, $clean_txt);

            // indexe tous les urls -> peut être supprimé en prod
            if (strpos($clean_txt, 'href') > 0) {
                $xmlDoc = new DOMDocument();
                $xmlDoc->loadHTML($clean_txt);
                $a = $xmlDoc->getElementsByTagName('a');

                for ($i = 0; $i < $a->length; $i++) {
                    $link = $a->item($i)->getAttribute('href');
                    if (strpos($link, NOM_DE_DOMAINE) !== false) {
                        array_push(Post::$link_domain, $link);
                    }
                }
            }
            return $clean_txt;
        }

        return '';
    }

    public static function addLinkReplacement($url1, $url2)
    {
        array_push(Post::$link_replacement, array(
            'url_typo' => $url1, 'url_wp' => $url2
        ));
    }

    /**
     * Va chercher toutes les images contenues dans l'html et les remplace par des images importées dans wp avec l'url de wp
     *
     * @param $content l'html comportant les images
     *
     * @return le contenu dont les urls des images ont été remplacées
     */

    public static function replaceImages($content)
    {
        if (strlen($content) < 5 && !strpos($content, '<img')) {
            return $content;
        }

        // on récupère toutes les images
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadHTML($content);
        $images = $xmlDoc->getElementsByTagName('img');

        // pareil mais avec xpath // ajouter <root> autour de $content sinon le parsage marche pas en xml.
        //		$path = new DOMXPath($xmlDoc);
        //		$chemin = "//img[@src]";
        //		$images = $path->query($chemin);

        for ($i = 0; $i < $images->length; $i++) {
            $cheminArray = explode('/', $images->item($i)->getAttribute('src'));        // sépare l'url dans un tableau selon les /
            $name = array_pop($cheminArray);                                            // récupère le nom du fichier et l'enlève du tableau. On va s'en servir pour le nettoyer et qu'il soit propre dans wp
            $chemin = implode('/', $cheminArray);                                       // reconstitue l'url sans le nom du fichier au bout

            $attach_id = Gallery::fetch_media($chemin . '/' . str_replace(' ', '%20', $name), Tools::cleanFilename($name, time())); // ajoute l'image via wp
            $new_img_url = wp_get_attachment_url($attach_id); // récupère l'url de l'image
            $content = str_replace($images->item($i)->getAttribute('src'), $new_img_url, $content); // remplace la veille url pointant sur typo par celle pointant vers le nouveau fichier
        }

        return $content;
    }

    public static function replaceLinks()
    {
        $bdd = new PDO('mysql:host=' . HOST . ';dbname=' . BD_NAME_WP . '', '' . LOGIN . '', '' . PASSWORD . '', array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
        ));
        foreach (Post::$link_replacement as $link) {
            $sql = "update wp_posts set post_content = replace(post_content, ?, ?)";
            $req = $bdd->prepare($sql);
            $req->execute(array($link['url_typo'], $link['url_wp']));
        }
    }

}
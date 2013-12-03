<?php

class News extends Post
{
    public static $post_type = 'post';

    public function __construct($array, $log)
    {
        $this->log = $log;
        $this->news = $array;

        $this->log->f('News', 'weight3');

        $this->insertNews();
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
        $log = Log::getInstance();

        //		foreach($this->news as $i => $news)
        // $i= 1775;
        for ($i = 0; $i < sizeof($this->news) - 1; $i++) {
            $news = $this->news[$i];
            $this->log->f('News n° : '.$i.' - '.$news['name'], 'weight2');

            $content = $this->getCleanText($news['content']);
            $content = Page::replaceImages($content);

            $post_id = wp_insert_post(array('post_title'   => $news['name'],
                                            'post_type'    => 'post',
                                            'post_status'  => 'publish',
                                            'post_content' => $content,
                                            'post_date'    => date('Y-m-d H:i:s', $news['date_creation'])));

            if ($post_id == 0) {
                $log->f('La news : '.$i.' - '.$news['name'].' -  n\'a pas été inseré', 'error');
            }

            $this->news[$key]['wp_post_id'] = $post_id; // on enregistre l'id wp du post fraichement créé.

            // on va chercher les jeux liés pour les rattacher
            $jeux_lies = FichesJeu::getAttachedGames($news['liaison_news']);

            if (sizeof($jeux_lies) > 0) {
                $values = array();
                foreach ($jeux_lies as $jeu_lie) {
                    $values[] = $jeu_lie;
                }
                update_field(ACF_JEUX_LIES, $values, $post_id);
            }


            if ($i > NB_NEWS) {
                break;
            } // $i > 1800

        }

    }


    private function getCleanText($html)
    {
        $content = Page::cleanHtml($html);


        // supprimer l'entête qui dit sur quel site la news a été postée et par qui.

        //		$patterns = array ('/<p>.*<\/p>(.*)/msU');
        //		$replace = array ('\1');
        //		$content = preg_replace($patterns,$replace,$content);

        //echo $content."\n\n\n**********************\n\n\n";


        //		$chaine_indesirable = '<p>';
        //		$content = preg_replace($chaine_indesirable, '', $content);
        //		$content = '<p>'.$content.'</p>';

        //		$content = preg_replace('/<p.*<\/p>(.*)/', '<p>$1</p>', $content);
        //		$content = '<p>'.$content.'</p>';

        return $content;

    }


}

?>
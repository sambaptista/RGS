<?php

class News extends AbstractRedactionalPost
{
    public static $post_type = 'post';
    public static $newsCats = array();

    public static function getCleanText($html)
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


    public static function buildGenresIndex($fiches)
    {
        foreach ($fiches as $key => $fiche) {
            $genres = $fiche['newsType'];
            if (isset($genres) && sizeof($genres) > 0) {
                foreach ($genres as $genre) {
                    if (!empty($genre)) {
                        $game = Game::findByTypoId($key);
                        if (!isset(self::$newsCats[$genre])) {
                            self::$newsCats[$genre] = array($game->ID);
                        } else {
                            array_push(self::$newsCats[$genre], $game->ID);
                        }
                    }
                }
            }
        }
    }


    public static function getAttachedGames($newsCategories)
    {
        if (empty($newsCategories)) {
            return;
        }

        $newsCategories = explode(',', $newsCategories);
        if (sizeof($newsCategories) == 0) {
            return;
        }

        $games = array();
        foreach ($newsCategories as $nC) {
            if (isset(self::$newsCats[$nC]) && sizeof(self::$newsCats[$nC]) > 0) {
                $games = array_merge($games, self::$newsCats[$nC]);
            }
        }

        return $games;
    }

    public function setAttachGames($data)
    {
        // on va chercher les jeux liés pour les rattacher
        $games = News::getAttachedGames($data['liaison_news']);
        $this->setAttachedGames($games, true);
//
//        if (sizeof($jeux_lies) > 0) {
//            $values = array();
//            foreach ($jeux_lies as $jeu_lie) {
//                $values[] = $jeu_lie;
//            }
//
//            update_field(ACF_JEUX_LIES, $values, $this->ID);
//        }
    }



}
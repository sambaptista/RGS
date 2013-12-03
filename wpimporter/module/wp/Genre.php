<?php

class Genre extends Term
{
    public static $term_type = 'genre';
    public static $genres_typo_id_table_name = 'rgs_genre_correspond';


    public static function create($name, $typo_id)
    {
        $term = parent::create($name);
        $result = Rgsbd::getInstance()->query('SELECT * FROM '.self::$genres_typo_id_table_name.' WHERE genre_id = '.$term->ID);

        if (is_null($result) || is_array($result) && count($result) === 0) {
            Rgsbd::getInstance()->query('INSERT INTO '.self::$genres_typo_id_table_name.' (genre_id, typo_id) VALUES ('.$term->ID.', '.$typo_id.')');
        }

        if (get_class($term) != 'WP_ERROR') {
            return $term;
        } else {
            $message = self::getTermType() . " ". $name . ", Not inserted";
            Log::logError($message, __LINE__, __FILE__);
            throw new Exception($message);
        }
    }

}
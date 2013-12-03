<?php

class Term
{
    public static $term_type = 'genre';
    public $ID;
    public $term_obj;

    protected function __construct($term)
    {
        $this->ID = $term->term_id;
        $this->term_obj = $term;
    }


    protected static function newClass($term_object)
    {
        $class = get_called_class();

        return new $class($term_object);
    }

    protected static function getTermType()
    {
        $class = get_called_class();

        return $class::$term_type;
    }

    public static function create($name)
    {
        fr($name . ' - ' .is_numeric($name));

        if(is_numeric($name)) {
            $message = self::getTermType() . " ". $name . ", Not inserted, numeric given. String expected";
            Log::logError($message, __LINE__, __FILE__);
            throw new Exception($message);
        }
        $term = self::findByName($name);

        fr($term);
        if ($term) {
            return $term;
        } else {
            $term = wp_insert_term($name, self::getTermType()) ;
            if (is_array($term)) {
                return self::findbyId($term['term_id']);
            } else {
                $message = self::getTermType() . " ". $name . ", Not inserted";
                Log::logError($message, __LINE__, __FILE__);
                throw new Exception($message);
            }
        }
    }

    public static function findById($id)
    {
        return self::findBy('id', $id);
    }

    public static function findByTypoId($id)
    {
        if(!is_numeric($id) || $id<=0) return false;

        $result = Rgsbd::getInstance()->query('select genre_id from '.Genre::$genres_typo_id_table_name.' where typo_id='.$id);
        if(is_array($result) && count($result) == 1){
            return self::findBy('id', $result[0]['genre_id']);
        } else {
            return false;
        }
    }

    public static function findByName($name)
    {
        return self::findBy('name', $name);
    }

    protected static function findBy($criteria, $value)
    {
        if(!isset($value) || empty($value)) return false;

        $term = get_term_by($criteria, $value, self::getTermType());

        if ($term) {
            return self::newClass($term);
        } else {
            return false;
        }
    }

}
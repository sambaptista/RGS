<?php

class RGSDB
{

    private $bdd;


    public static function getInstance()
    {
        if (true === is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    private function __construct()
    {
        $this->bdd = new PDO('mysql:host='.HOST.';dbname='.BD_NAME_WP.'', ''.LOGIN.'', ''.PASSWORD.'', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                                                                                             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
    }


    /**
     * Supprime les enregistrements, excepté les champs personnalisés, reset les autoincrement et supprime les fichiers média
     */
    public function resetWP()
    {
        f('Reset de la BD', 'weight3');

        // récupère l'id des custom field pour éviter de les supprimer
        $result = $this->query("select ID as id from wp_posts where post_type='acf'");


        // supprime les post autre que les custom field
        $this->query("delete from wp_posts where post_type <> 'acf'");


        // supprime les postmeta sauf ceux des custom fields
        if (isset($result) && sizeof($result) > 0) {
            $where = '';
            for ($i = 0; $i < sizeof($result); $i++) {
                $where .= 'post_id <> '.$result[$i]['id'];
                if ($i < sizeof($result) - 1) {
                    $where .= ' and ';
                }
            }
            $sql = "delete from wp_postmeta where ".$where;
            $req = $this->prepare($sql);
            $req->execute();
        }

        // supprime les relations posts -> taxonomies
        $this->query("delete from wp_term_relationships");

        // supprime les taxonomies
        $this->query("delete from wp_term_taxonomy where term_taxonomy_id <> 1");

        // supprime les termes
        $this->query("delete from wp_terms where term_id <> 1");

        // remplace les identifiants de postmeta et posts pour qu'il repartent à zéro (ou presque)
        $postmetas = $this->query("select meta_id as id from wp_postmeta");
        $posts = $this->query("select ID as id from wp_posts");


        $req1 = $this->prepare("update wp_posts set ID=? where ID=?");
        $req2 = $this->prepare("update wp_postmeta set post_id=? where post_id=?");
        $i_posts = 1;
        foreach ($posts as $post) {
            $req1->execute(array($i_posts,
                $post['id']));
            $req2->execute(array($i_posts,
                $post['id']));
            $i_posts++;
        }

        $req = $this->prepare("update wp_postmeta set meta_id=? where meta_id=?");
        $i_postmetas = 1;
        foreach ($postmetas as $post) {
            $req->execute(array($i_postmetas,
                $post['id']));
            $i_postmetas++;
        }


        // reset les auto incrément
        $this->query("
			ALTER TABLE wp_term_relationships AUTO_INCREMENT=2;
			ALTER TABLE wp_term_taxonomy AUTO_INCREMENT=2;
			ALTER TABLE wp_terms AUTO_INCREMENT=2;
			ALTER TABLE wp_posts AUTO_INCREMENT=".($i_posts).";
			ALTER TABLE wp_postmeta AUTO_INCREMENT=".($i_postmetas).";
		");


        // supprime tous les uploads
        $path = ABS_PATH.'/wp-content/uploads/';
        $this->recursiveDelete($path);

        // ajoute le répertoire des archives d'images
        @mkdir(ABS_PATH.'/wp-content/uploads', 0777);
        @mkdir(ABS_PATH.'/wp-content/uploads/2013', 0777);
        @mkdir(ABS_PATH.'/wp-content/uploads/2013/05', 0777);
    }


    /* Suprime les fichiers du disque dur récursivement */
    private function recursiveDelete($str)
    {
        if (is_file($str)) {
            return @unlink($str);
        } elseif (is_dir($str)) {
            $scan = glob(rtrim($str, '/').'/*');
            foreach ($scan as $index => $path) {
                $this->recursiveDelete($path);
            }

            return @rmdir($str);
        }
    }


    public function displayBDSize()
    {
        $result = $this->query("
			SELECT sum( data_length + index_length ) / 1024 / 1024 'MB'
			FROM information_schema.TABLES
			where table_schema like '".BD_NAME_WP."'
		");

        return $result[0]['MB'];

    }


    public function query($sql, $params = array())
    {
        $req = $this->prepare($sql);
        $req->execute($params);
        $req->setFetchMode(PDO::FETCH_ASSOC);

        return $req->fetchAll();
    }


    public function prepare($sql)
    {
        return $this->bdd->prepare($sql);
    }


    public function execute($req, $params = array())
    {
        $req->execute($params);
        $req->setFetchMode(PDO::FETCH_ASSOC);
        return $req->fetchAll();
    }



    public function getBdd()
    {
        return $this->bdd;
    }


}
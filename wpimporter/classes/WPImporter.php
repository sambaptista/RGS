<?php





class WPImporter
{
	public $log;
	public $bdd;
	public $teo; // typo exporter object
	
	
	public function __construct()
	{
		$this->log = $log = Log::getInstance();
		$this->teo = new TypoExporter($log);
		$this->bdd = new PDO('mysql:host='.HOST.';dbname='.BD_NAME_WP.'', ''.LOGIN.'', ''.PASSWORD.'', array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));

		//$this->log->fr( $this->teo->arbre );
		Tools::printStructure( $this->teo->arbre);
//		Tools::printStructure( $this->teo->fiches_de_jeu);
echo '***** fiches de test *****';
		Tools::printStructure( $this->teo->fiches_test);
		
		exit();
		
		$this->log->f('Début Importation', 'weight4');

		$this->resetWP();
		$this->displayBDSize();
	
		
		$this->importeFichesJeu();
		$this->displayBDSize();
		
		$this->importeTests();
		$this->displayBDSize();
		
		$this->importeGalleries();
		$this->displayBDSize();
		
		$this->importeNews();
		$this->displayBDSize();
		
		$this->importePages();
		$this->displayBDSize();
		

		
		echo '<pre>';
		print_r( Page::$link_replacement );
		echo '*************';
		print_r( Page::$link_domain );
		echo '</pre>';
		
		Page::replaceLinks();
		
		$this->log->f('*** Importation terminée ****', 'weight4 success');
		
	}
	
	
	
	
	
	
	
	
	/**
	* Supprime les enregistrements, excepté les champs personnalisés, reset les autoincrement et supprime les fichiers média
	*/
	public function resetWP( )
	{
		$this->log->f('Reset de la BD', 'weight3');
		
		// récupère l'id des custom field pour éviter de les supprimer
		$sql = "select ID as id from wp_posts where post_type='acf'";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		$req->setFetchMode(PDO::FETCH_ASSOC);
		$result = $req->fetchAll();
		
																			
		// supprime les post autre que les custom field
		$sql = "delete from wp_posts where post_type <> 'acf'";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		
		// supprime les postmeta sauf ceux des custom fields
		if( isset($result) && sizeof($result)>0  )
		{
			$where = '';
			for($i=0 ; $i<sizeof($result); $i++)
			{
				$where .= 'post_id <> '.$result[$i]['id'];
				if( $i < sizeof($result)-1 ) $where.=' and ';
			}
			$sql = "delete from wp_postmeta where ".$where;
			$req = $this->bdd->prepare($sql);
			$req->execute();
		}
		
		// supprime les relations posts -> taxonomies
		$sql = "delete from wp_term_relationships";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		
		// supprime les taxonomies
		$sql = "delete from wp_term_taxonomy where term_taxonomy_id <> 1";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		

		// supprime les termes
		$sql = "delete from wp_terms where term_id <> 1";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		
		
		// remplace les identifiants de postmeta et posts pour qu'il repartent à zéro (ou presque)
		$sql = "select meta_id as id from wp_postmeta";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		$req->setFetchMode(PDO::FETCH_ASSOC);
		$postmetas = $req->fetchAll();
		
		$sql = "select ID as id from wp_posts";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		$req->setFetchMode(PDO::FETCH_ASSOC);
		$posts = $req->fetchAll();
		
		$sql1 = "update wp_posts set ID=? where ID=?";
		$req1 = $this->bdd->prepare($sql1);
		$sql2 = "update wp_postmeta set post_id=? where post_id=?";
		$req2 = $this->bdd->prepare($sql2);
		$i_posts = 1;
		foreach($posts as $post)
		{
			$req1->execute(array($i_posts, $post['id']));
			$req2->execute(array($i_posts, $post['id']));
			$i_posts++;
		}
		
		$sql = "update wp_postmeta set meta_id=? where meta_id=?";
		$req = $this->bdd->prepare($sql);
		$i_postmetas = 1;
		foreach($postmetas as $post)
		{
			$req->execute(array($i_postmetas, $post['id']));
			$i_postmetas++;
		}
				
		
		
		// reset les auto incrément
		$sql = "
			ALTER TABLE wp_term_relationships AUTO_INCREMENT=2;
			ALTER TABLE wp_term_taxonomy AUTO_INCREMENT=2;
			ALTER TABLE wp_terms AUTO_INCREMENT=2;
			ALTER TABLE wp_posts AUTO_INCREMENT=".($i_posts).";
			ALTER TABLE wp_postmeta AUTO_INCREMENT=".($i_postmetas).";
		";
		$req = $this->bdd->prepare($sql);
		$req->execute();
		
		
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
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
	
	
	
	public function displayBDSize()
	{
		$sql = '
			SELECT sum( data_length + index_length ) / 1024 / 1024 "MB" 
			FROM information_schema.TABLES 
			where table_schema like "sam_rgs_wordpress"
		';
		$req = $this->bdd->prepare($sql);
		$req->execute();
		$req->setFetchMode(PDO::FETCH_ASSOC);
		$result = $req->fetchAll();
				
		$this->log->f('Taille de la BD : '.$result[0]['MB'], 'weight2');
	
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
		print_r( $value );
		echo '</pre>';
		
		$value[] = array(	"texte" => "import text", 
							"acf_fc_layout" => "contenu_sur_une_colonne");
							
		$value[] = array(	"colonne_de_gauche" => "import gauche", 
							"colonne_de_droite" => "import droite", 
							"acf_fc_layout" => "contenu_sur_deux_colonnes");
		
		update_field( $field_key, $value , $postid );


	}
	
























	
	
}

?>
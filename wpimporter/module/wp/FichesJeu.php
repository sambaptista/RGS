<?php

class FichesJeu extends Post
{

	public static $fiches;
	public static $genres;
	public static $newsCats = array();
	public static $sections;
	public $log;
	

	
	
	public function __construct($fiches, $genres,  $log)
	{
		$this->log = $log;
		self::$fiches = $fiches;
		self::$genres = $genres;
			
		$this->log->f('Fiches de jeu', 'weight3');

		//$this->insertSections();
		$this->insertGenres();
		$this->insertFiches();
		$this->buildGenresIndex();
	}
	
	
	
	
	
	private function insertSections()
	{
		$term = wp_insert_term( 'jeux-strategie.com' , 'section');
	 	self::$sections = array( $term['term_id'] );
		
	}


	
	
	private function insertGenres()
	{
		foreach( self::$genres as $key => $genre)
		{
		 	$term = wp_insert_term( $genre, 'genre');
		 	
		 	self::$genres[$key] = array(
		 		'genre' => $genre,
		 		'term_id'=>$term['term_id'],
		 		'term_taxonomy_id'=>$term['term_taxonomy_id']
		 	);
		}
		
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
		
		// insère les fiches complètes
		foreach(self::$fiches as $key => $fiche)
		{			
			
			$post_id = wp_insert_post( array(
				'post_title' => $fiche['nomJeu'],
				'post_type' => 'games',
				'post_status' => 'publish'	
				) 
			);
			
			if($post_id == 0) exit('post '.$fiche['nomJeu'].' n\'a pas été inseré');

			self::$fiches[$key]['wp_post_id'] = $post_id;
			
			$genre_ids = array();
			foreach( $fiche['genre'] as $genre)
			{
				array_push($genre_ids, self::$genres[$genre]['term_id']);
			}
			$link = wp_set_object_terms($post_id, $genre_ids,'genre'); // genres
			//$link = wp_set_object_terms($post_id, self::$sections[0],'section'); // sections
				
				
				
			// définition des champs personnalisés acf
			// add_post_meta( $post_id, 'note', 	$fiche['note'] ); // équivalent en natif wordpress
			
			$values = array();
			update_field( ACF_FJ_DATE_DE_SORTIE, utf8_decode( $fiche['dateSortie']), $post_id );
			$values[] = array(	"adresse" => utf8_decode( Tools::getUrl($fiche['site'])),
								"acf_fc_layout" => "site_officiel");		
			$values[] = array(	"nom" => utf8_decode( Tools::getLinkText($fiche['editeur'])),
								"adresse" => utf8_decode( Tools::getUrl($fiche['editeur'])),
								"acf_fc_layout" => "editeur");	
			$values[] = array(	"nom" => utf8_decode( Tools::getLinkText($fiche['developpeur'])),
								"adresse" => utf8_decode( Tools::getUrl($fiche['developpeur'])),
								"acf_fc_layout" => "développeur");

			update_field( ACF_FJ_SITES, $values , $post_id );

		}
		
		// insère les fiches orphelines (fiches qui ont du contenu associé, mais qui n'existent pas elles même)
		foreach( self::$fiches_orphelines as $fiche)
		{
			$post_id = wp_insert_post( array(
				'post_title' => $fiche,
				'post_type' => 'games',
				'post_status' => 'draft'	
				) 
			);
			
			if($post_id == 0) exit('post '.$fiche.' n\'a pas été inseré');		
			self::$fiches[$key] = array(
				'wp_post_id' => $post_id,
				'nom' => $fiche	
			);	
		}
		
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>
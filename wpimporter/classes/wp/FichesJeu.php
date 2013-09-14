<?php

class FichesJeu extends Post
{

	public static $fiches;
	public static $genres;
	public static $newsCats = array();
	public static $sections;
	public $log;
	
	/**
	Deviendra après importation : 
	'100000' => array('nom' => ..., 'wp_post_id' => ... );
	*/
	public static $fiches_orphelines = array( 
		'100000' => 'Age Of Wonders : Shadow Magic',
		'100001' => 'Battle Mages',
		'100002' => 'Battle Realms',
		'100003' => 'Chaos League',
		'100004' => 'Dominions 2 ',
		'100005' => 'Etherlords II',
		'100006' => 'KOHAN : Immortal Sovereigns',
		'100007' => 'La Bataille pour la Terre du Milieu',
		'100008' => 'La Bataille pour la Terre du Milieu II',
		'100009' => 'La Bataille pour la Terre du Milieu II : l AvËnement du Roi-Sorcier ',
		'100010' => 'Spellforce',
		'100011' => 'The Battle for Wesnoth',
		'100012' => 'Warlords Battlecry II',
		'100013' => 'Warlords Battlecry III',
		'100014' => 'Command and Conquer 3: La fureur de Kane',
		'100015' => 'Conquest: Frontier Wars',
		'100016' => 'Empereur: la Bataille pour Dune',
		'100017' => 'Forged Alliance',
		'100018' => 'Ground Control 2 : OpÈration Exodus ',
		'100019' => 'HomeWorld 2',
		'100020' => 'Massive Assault',
		'100021' => 'Project Visitor',
		'100022' => 'Sins of a Solar Empire : Entrenchment',
		'100023' => 'Star Warsô Galactic Battlegroundsô',
		'100024' => 'UFO : Aftermath',
		'100025' => 'Warhammer 40 000 : Dawn of War',
		'100026' => 'Warhammer 40 000 : Dawn of War 2 - Chaos Rising',
		'100027' => 'Age of Kings : The Conquerors',
		'100028' => 'Alerte Rouge 2',
		'100029' => 'American Conquest : Fight Back',
		'100030' => 'Anno 1503 : le nouveau monde',
		'100031' => 'Chariots of War',
		'100032' => 'Civilization 3',
		'100033' => 'Command & Conquer Generals',
		'100034' => 'C&C Generals : Heure H',
		'100035' => 'Cossacks : European Wars',
		'100036' => 'Crown of the north',
		'100037' => 'Europa 1400',
		'100038' => 'Europa Universalis',
		'100039' => 'Hearts of Iron',
		'100040' => 'Medieval Lords',
		'100041' => 'Medieval Total War',
		'100042' => 'Montjoie',
		'100043' => 'Rise of Nations',
		'100044' => 'StrongHold',
		'100045' => 'Stronghold Crusader ',
		'100046' => 'The Gladiators',
		'100047' => 'Victoria',
		'100048' => 'War and Peace',
		'100049' => 'Warrior Kings',
		'100050' => 'Advance Wars',
		'100051' => 'Advance Wars 2 : black hole rising',
		'100052' => 'Advance Wars DS',
		'100053' => 'Airborne Assault',
		'100054' => 'Darwinia',
		'100055' => 'Final Fantasy Tactics Advance',
		'100056' => 'Football Manager 2009',
		'100057' => 'Impossible Creatures',
		'100058' => 'No Man s Land',
		'100059' => 'Galactic Civilisation II',
		'100060' => 'Republic',
		'100061' => 'Hearthstone',
		'100062' => 'Dark End',
		'100063' => 'Birth of America ',
		'100064' => 'Jagged Alliance 3D ',
		'100065' => 'Defenders of Ardania',
		'100066' => 'Majesty 2',
		'100067' => 'Age of Empires 3',
		'100068' => 'Deus Ex 3',
		'100069' => 'Dragon Commander',
		'100070' => 'Galactic Assault',
		'100071' => 'Hearts of Iron 2',
		'100072' => 'Jumpgate Evolution ',
		'100073' => 'Kingdom Under Fire II',
		'100074' => 'Rise of Immortals',
		'100075' => 'Supreme Ruler 1936',
		'100076' => 'The Settlers VI',
		'100077' => 'Victoria 2 : A House Divided',
		'100078' => 'Wargame Airland Battle'
	);
	
	
	
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

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>
<?php


class FicheTest extends Post
{
    public static $post_type = 'test';

	public function __construct($array, $log)
	{
		$this->log = $log;
		self::$fiches = $array;
		$this->log->f('Fiches de test', 'weight3');
		$this->insertFiches();
	}



	/**
	* @TODO : inclure les fiches test qui sont dans $html_externe
	*/
	private function insertFiches()
	{	
	
		foreach(self::$fiches as $key => $fiche)
		{			
			$post_id = wp_insert_post( array(
				'post_title' => $fiche['name'],
				'post_type' => 'test',
				'post_status' => 'publish'	
				) 
			);
			
			if($post_id == 0) exit('La fiche test '.$key.' n\'a pas été inserée');

			self::$fiches[$key]['wp_post_id'] = $post_id;
			
			
			
			/****************************************************************
				Jeux liés à la page
			*****************************************************************/
			
			if( sizeof($fiche['liaison_jeu']) > 0)
			{				
				$values = array();
				foreach($fiche['liaison_jeu'] as $jeu_lie)
					if(is_numeric($jeu_lie))
						$values[] = FichesJeu::$fiches[$jeu_lie]['wp_post_id'];	
				update_field( ACF_JEUX_LIES , $values, $post_id );
			}
						
						
			
			/****************************************************************
					CHAMPS PERSONNALISES : ACF
			*****************************************************************/
			
			// LES PLUS : REPEATER
			if( sizeof($fiche['lesPlus']) > 0)
			{
				$values = array();
				foreach($fiche['lesPlus'] as $unPlus)
					$values[] = array( "description" => utf8_decode($unPlus) );
				update_field( ACF_FT_LES_PLUS, $values , $post_id );
			}
			
			// LES MOINS : REPEATER
			if( sizeof($fiche['lesMoins']) > 0)
			{
				$values = array();
				foreach($fiche['lesMoins'] as $unMoins)
					$values[] = array( "description" => utf8_decode($unMoins) );
				update_field( ACF_FT_LES_MOINS, $values , $post_id );
			}
				
			// CONFIGURATION : REPEATER
			if( !empty($fiche['configuration']) )
			{
				$values = array();
				$values[] = array(	"description" => utf8_decode( $fiche['configuration'] ));
				update_field( ACF_FT_CONFIG, $values , $post_id );
			}
			
			// NOTATION : FLEXIBLE CONTENT
			$values = array();	
					
			$values[] = array(	"note" =>  $fiche['noteGlobale'] ,
								"commentaire" => utf8_decode( $fiche['commentaire'] ),
								"acf_fc_layout" => "note_globale");
								
			$values[] = array(	"note" =>  $fiche['notation']['jouabiliteNote'] ,
								"commentaire" => utf8_decode( $fiche['notation']['jouabiliteComment'] ),
								"acf_fc_layout" => "jouabilite");	
								
			$values[] = array(	"note" =>  $fiche['notation']['graphismeNote'] ,
								"commentaire" => utf8_decode( $fiche['notation']['graphismeComment'] ),
								"acf_fc_layout" => "graphisme");
								
			$values[] = array(	"note" =>  $fiche['notation']['bandeSonNote'] ,
								"commentaire" => utf8_decode( $fiche['notation']['bandeSonComment'] ),
								"acf_fc_layout" => "bande_son");	
								
			$values[] = array(	"note" =>  $fiche['notation']['multiNote'] ,
								"commentaire" => utf8_decode( $fiche['notation']['multiComment'] ),
								"acf_fc_layout" => "multijoueur");
								
			$values[] = array(	"note" =>  $fiche['notation']['soloNote'] ,
								"commentaire" => utf8_decode( $fiche['notation']['soloComment'] ),
								"acf_fc_layout" => "solo");	
			update_field( ACF_FT_NOTES, $values , $post_id );
		}
		

		
	}

}

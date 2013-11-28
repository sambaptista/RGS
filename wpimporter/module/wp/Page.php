<?php

class Page extends Post
{

	public $log;	
	public static $pages;
	public static $html_externe;


	/*
	*	A chaque produit inséré, on ajoute l'url typo et la nouvelle url page afin de remplacer dans l'html les liens et éviter un maximum de redirections
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

	//Référence tous les liens différents du domaine, pour voir combien de liens pointent vers des pages du réseau. si le tableau est plus grand, c'est que certaines urls pointent nul part
	public static $link_domain = array(); 	

	
	public function __construct($array, $html_externe, $log)
	{
		$this->log = $log;
		self::$pages = $array;
		self::$html_externe = $html_externe;
		
		$this->log->f('Pages', 'weight3');
		$this->insertPages();
	}





	/** 
	* Va créer une page qui comporte autant de sections qu'il y a de types de contenus dans la dite page. 
	* @param $content l'html sale qu'il faut nettoyer
	*/

	/*
		@todo : reste à exclure (à réfléchir) les types suivants lors de l'ajout :  galeries, tests et fiches de jeu (déjà fait puisque je ne les ajoute pas -> cible uniquement les types à insérer)
		@todo : ajouter les galeries et pièces jointes, vidéo, multimedia
	*/

	public function insertPages()
	{
				
		// importation de la page d'accueil racine
		$params = array(
			'post_title' => 'JS',
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_date' => date( 'Y-m-d H:i:s', $page['date_creation'])
		);
		$wp_home_page_id = wp_insert_post($params);
		
		
		// insertion des pages enfant
		foreach(self::$pages as $i => $page)
		{
			$this->log->f('Page : '.$page['id'].' - '.$page['name'], 'weight2');
						
			$params = array(
				'post_title' => $page['name'],
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_date' => date( 'Y-m-d H:i:s', $page['date_creation'])
			);
				
				
							
			/****************************************************************
				Parent management
			*****************************************************************/

			if( isset($page['id_parent']) && is_numeric($page['id_parent']) && $page['id_parent'] != 1504 )
			{	
				$parentPage = $this->getPageByTypoID($page['id_parent']);
				$params['post_parent'] = $parentPage['wp_post_id'];
			}
			else
				$params['post_parent'] = $wp_home_page_id;
			
			$post_id = wp_insert_post($params);
			if($post_id == 0) $log->f('La news : '.$i.' - '.$news['name'].' -  n\'a pas été inseré', 'error');
			self::$pages[$i]['wp_post_id'] = $post_id; // on enregistre l'id wp du post fraichement créé.
			
			Page::addLinkReplacement( $page['url'], get_permalink($post_id) );


			
			/****************************************************************
				Jeux liés à la page
			*****************************************************************/
			
			if( sizeof($page['liaison_jeu']) > 0)
			{				
				$values = array();
				foreach($page['liaison_jeu'] as $jeu_lie)
					if(is_numeric($jeu_lie))
						$values[] = FichesJeu::$fiches[$jeu_lie]['wp_post_id'];	
				update_field( ACF_JEUX_LIES , $values, $post_id );
			}
		
		
		
			/****************************************************************
				Champs personnalisés : ACF
			*****************************************************************/
		
			// aide
			$values = array();	
			if($page['content']['aide'])
				$values[] = array(	"texte" => $page['content']['aide'], 
									"acf_fc_layout" => "contenu_sur_une_colonne");
			
			
			
			if( sizeof($page['content']['contenu_page']) >0)
			{
				foreach($page['content']['contenu_page'] as $contenu)
				{
					//echo 'type : '.$contenu['type'].'<br/>';
					if(1==2 && $contenu['type'] == 'page' && strlen($contenu['titre_contenu'].$contenu['contenu']) > 0)
					{
						$text = self::cleanHtml( $contenu['titre_contenu'].$contenu['contenu']);
						$text = Page::replaceImages($text);
						$values[] = array(	"texte" => $contenu['error'].$text, "acf_fc_layout" => "contenu_sur_une_colonne");										
					}
					elseif(1==2 && $contenu['type'] == 'html_externe')
					{
						$url = 'Url externe : http://www.jeux-strategie.com/_getExternalHtml.php?chemin='.self::$html_externe[$contenu['id_document']].'<br/>';

						$curl = curl_init();
						curl_setopt_array($curl, array(
							CURLOPT_URL => $url,
							CURLOPT_HEADER => false,
							CURLOPT_RETURNTRANSFER => true
						));
						
						$text = curl_exec($curl);
						curl_close($curl);
												
						$text = self::cleanHtml( $text);
						$text = Page::replaceImages($text);
						$values[] = array(	"texte" => $text, "acf_fc_layout" => "contenu_sur_une_colonne");		
					}
	//				elseif($contenu['type'] == 'page_pics')
	//				{
	//					$value[] = array(	"media" => Gallery::$galeries[$contenu['id_document']]['wp_post_id'],
	//										"acf_fc_layout" => "media");
	//				
	//				}
	//				elseif($contenu['type'] = fiche_de_jeu, fiche_test, gallery, attachments, video, multimedia, html_externe
	//				{
	//				
	//				
	//				}
				
				
				}
			}
		
			
			update_field( ACF_CONTENU_REDACTION, $values , $post_id );
				
			if($i >= NB_PAGES) break;
			
		}

	}
	
	










	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//	public function testPurifier()
	//	{
	//		$dirty_html = '<span></span><p style="background:black" class="align-center"><font size="5" color="#0066ff"><strong>Dawn of War 2 </strong></font></p><p class="align-center"><font size="5" color="#0066ff"><strong><em>- E-Sport -</em></strong></font></p><p class="align-center"><strong><em><font size="5" color="#0066ff"></font></em></strong></p><p class="align-left"><font color="#000000">Dans ce dossier, seront regroupées toutes les activités E-Sport spécifiques à Dawn of War 2. Notamment nos tournois francophones "Les Cups of Coffee", la sélection de léquipe de France, le ladder français et le championnat international par équipe.</font></p><p class="align-left">Les tableaux seront mis à jour réguièrement.</p><p class="align-left">Notre section E-sport</p><p class="align-left">Notre section internationale</p><p class="align-left"></p>';
	//		$config = HTMLPurifier_Config::createDefault();
	//		$config->set('AutoFormat.RemoveEmpty', true);
	//		$config->set('HTML.ForbiddenAttributes', array('class', 'style', 'align', 'dir') );
	//		$config->set('HTML.ForbiddenElements', array('font', 'strong', 'em', 'script', 'b', 'u') );
	//
	//		$purifier = new HTMLPurifier($config);
	//		$clean_txt = $purifier->purify($dirty_html);
	//		echo htmlentities($clean_txt);
	//	}
	
	
	
	
	
	
}

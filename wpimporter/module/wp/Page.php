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
	
	










	public function getPageByTypoID( $id )
	{
		foreach( self::$pages as $page)
		{
			if($page['id'] == $id)
			{
				unset($page['children']);
				return $page;
			}
		}	
	}














	
	
	
	/** 
	* Nettoie le code des artifices html
	* @param $content l'html sale qu'il faut nettoyer
	*/

	public function createCleanPage( $element )
	{
		if( isset($element['content']['contenu_page']) && is_array($element['content']['contenu_page']))
		{
			$dirty_html = '';
			foreach($element['content']['contenu_page'] as $contenu)
			{
				if( isset($contenu['contenu'])){
					if(is_array($contenu['contenu'])){
					}else
						$dirty_html .= $contenu['contenu'];
				} 	
			}
			
			if($dirty_html != '')
			{
				$clean_html = Page::cleanHtml($dirty_html);
				
				$filename_dirty = 'output/html_sale/'.$element['id'].'-'.Tools::cleanFilename($element['name']).'.html';
				$filename = 'output/html_propre/'.$element['id'].'-'.Tools::cleanFilename($element['name']).'.html';
				
				$this->log->p('created '. $filename, 'weight1');
				
				file_put_contents($filename_dirty, $dirty_html);
				file_put_contents($filename, $clean_txt);
			}			
		}	
	}







	/** 
	* Nettoie l'html des ses artifices
	* @param $content l'html sale qu'il faut nettoyer
	*/

	public static function cleanHtml($dirty_html)
	{
		if($dirty_html != '')
		{
			$config = HTMLPurifier_Config::createDefault();
			$config->set('AutoFormat.RemoveEmpty', true);
			$config->set('HTML.ForbiddenAttributes', array('class', 'style', 'align', 'target'));
			$config->set('HTML.ForbiddenElements', array('font', 'strong', 'em', 'script', 'b'));
			$config->set('AutoFormat.AutoParagraph', true);
			$config->set('AutoFormat.AutoParagraph', true);
			$config->set('AutoFormat.RemoveEmpty', true);
			$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
			$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
			
			$purifier = new HTMLPurifier($config);
			$clean_txt = $purifier->purify($dirty_html);
			$clean_txt = preg_replace('#(<br(( +)|)(\/|)(( +)|)>)+#i', '<br />', $clean_txt);
			$clean_txt = preg_replace('!^<p>(.*?)</p>$!i', '$1', $clean_txt);
			$clean_txt = str_replace('<div', '<p', $clean_txt);
			$clean_txt = str_replace('</div', '</p', $clean_txt);
			$clean_txt = str_replace('<h1', '<h2', $clean_txt);
			$clean_txt = str_replace('</h1', '</h2', $clean_txt);
			
		 	$patterns = array ('/<p>([^(<\/p>)]*)<p>/ms','/<\/p>([^(<p>)]*)<\/p>/ms');
		  	$replace = array ('<p>\1</p><p>','</p><p>\1</p>');
		  	$clean_txt = preg_replace($patterns,$replace,$clean_txt);
		  	
			$patterns = array ('/<p>([^(<\/p>)]*)<p>/ms','/<\/p>([^(<p>)]*)<\/p>/ms');
			$replace = array ('<p>\1</p><p>','</p><p>\1</p>');
			$clean_txt = preg_replace($patterns,$replace,$clean_txt);
			
			
			// indexe tous les urls -> peu être supprimé en prod
			$xmlDoc = new DOMDocument();
			$xmlDoc->loadHTML( $clean_txt );
			$a = $xmlDoc->getElementsByTagName('a'); 
			
			for( $i=0; $i< $a->length; $i++)
			{
				$link = $a->item($i)->getAttribute('href');
				if( strpos( $link, NOM_DE_DOMAINE ) !== false )
					array_push( self::$link_domain, $link );
			}
								
			return $clean_txt;
		}	
		return '';		
	}
	
	
	
	
	
	
	public static function addLinkReplacement($url1, $url2)
	{
		array_push( Page::$link_replacement, 
			array(
				'url_typo' => $url1,
				'url_wp' => $url2
			)
		);		
	}
	
	
	
	
	
	
	
	/** 
	* Va chercher toutes les images contenues dans l'html et les remplace par des images importées dans wp avec l'url de wp
	* @param $content l'html comportant les images
	* @return le contenu dont les urls des images ont été remplacées
	*/
	
	public static function replaceImages($content)
	{
		if(strlen($content) < 5 && !strpos($content, '<img') ) return $content;
		
		$log = Log::getInstance();
		
		// on récupère toutes les images
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadHTML( $content );
		$images = $xmlDoc->getElementsByTagName('img'); 
		
		// pareil mais avec xpath // ajouter <root> autour de $content sinon le parsage marche pas en xml. 
		//		$path = new DOMXPath($xmlDoc);
		//		$chemin = "//img[@src]";
		//		$images = $path->query($chemin);
		
		$imagesUrls = array();
		for( $i=0; $i< $images->length; $i++)
		{			
			$cheminArray = explode('/', $images->item($i)->getAttribute('src') ); // séparre l'url dans un tableau selon les /
			$name = array_pop($cheminArray); // récupère le nom du fichier et l'enlève du tableau. On va s'en servir pour le nettoyer et qu'il soit propre dans wp
			$chemin = implode('/', $cheminArray); // reconstitue l'url sans le nom du fichier au bout
			
			$attach_id = Gallery::fetch_media( 	$chemin.'/'.str_replace(' ', '%20', $name), Tools::cleanFilename($name, $time) ); // ajoute l'image via wp
			$new_img_url = wp_get_attachment_url($attach_id); // récupère l'url de l'image
			$content = str_replace( $images->item($i)->getAttribute('src'), $new_img_url, $content); // remplace la veille url pointant sur typo par celle pointant vers le nouveau fichier
		}
		
		return $content;
	}
	
	
	
	
	
	public static function replaceLinks()
	{
		$bdd = new PDO('mysql:host='.HOST.';dbname='.BD_NAME_WP.'', ''.LOGIN.'', ''.PASSWORD.'', array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
		foreach( self::$link_replacement as $link)
		{
			$sql = "update wp_posts set post_content = replace(post_content, ?, ?)";
			$req = $bdd->prepare($sql);
			$req->execute( array($link['url_typo'], $link['url_wp']));
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

<?php/******************************************************* Parser Typo -> Wordpress                         **** Version : 0.1                                    **** Auteurs: GG_A_SsaSsIns, XIV-V                    **** Date dernière version : 14/05/2013               ********************************************************////////////////////////////////////////////////////////////////    EXPORTATION TYPO + PARSAGE/NETTOYAGE DES DONNEES     ///////////////////////////////////////////////////////////////// Début du script, l'execution commence a partir d'ici, les différentes fonction sont également appelees ici. //class TypoExporter{	public $bdd;	public $log;	// résultat de la documentation -> on ne fait pas un tableau avec tout dedans, mais plusieurs tableau défini dans les propriétés	// laisser en public	public $cartographie;	public $arbre;	public $genres;	public $jeux;	public $galeries;	public $fiches_de_jeu;	public $fiches_test;	public $attachments;	public $videos;	public $html_externe;	public $news;		public function __construct($log)	{		$this->log = $log;					$this->log->f('Début Exportation', 'weight4');				$this->bdd = new PDO('mysql:host='.HOST.';dbname='.BD_NAME.'', ''.LOGIN.'', ''.PASSWORD.'', array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));		$req = $this->bdd->prepare('SELECT uid as id, pid as id_parent, title as name, TSconfig, tstamp as date_creation, perms_userid as author, keywords, description FROM pages WHERE pid=? AND deleted=0 AND hidden=0 AND nav_hide=0 AND NOT doktype=254  AND NOT doktype=199 ORDER BY sorting ASC');		$req->execute(array(SECTION_RGS));		$req->setFetchMode(PDO::FETCH_ASSOC);		$result = $req->fetchAll();					$i=0;		while($i < count($result))		{			$req->execute(array($result[$i]['id']));			$result = array_merge($result, $req->fetchAll());			$i++;		}					$this->log->f('Création $arbre + parsage et ajout des contenus/marqueurs', 'weight3');		foreach ($result as $key => $value)		{			$searchContent = $this->getContenu($value['id'], SECTION_RGS, NOM_DE_DOMAINE, $value['author'], $value['name']);						if(is_numeric($result[$key]['TSconfig'])){				$resultIdLiaison = $result[$key]['TSconfig'];			}else{				$resultIdLiaison = "";			}				$result[$key] = array(				'id' => 			$result[$key]['id'], 				'id_parent' => 		$result[$key]['id_parent'], 				'name' => 			$result[$key]['name'], 				'date_creation' => 	$result[$key]['date_creation'], 				'author' => 		$searchContent[0], 				'keywords' => 		$result[$key]['keywords'], 				'description' => 	$result[$key]['description'], 				'url' => 			$searchContent[1],				'liaison_jeu' => 	explode(',',$resultIdLiaison),				'content' => 		$searchContent[2]			);		}		
		$pagesGaleriesVides = array();		$pagesFicheJeuVides = array();		$pagesFicheTestVides = array();		$pagesAttachementVides = array();		$pagesVideoVides = array();		$pagesHtmlExterneVides = array();
	
	
	
		$this->log->f('Creation des tableaux avec leur contenu : $galeries, $fiches_de_jeu, $fiches_test, $attachments, $videos, $html_externe', 'weight3');		foreach($result as $key => $element)		{			if( isset($element['content']['contenu_page']) && is_array($element['content']['contenu_page']))			{				foreach($element['content']['contenu_page'] as $key2 => $contenu_page)				{					if(isset($contenu_page['type']) && $contenu_page['type'] === 'gallery' || isset($contenu_page['type']) && $contenu_page['type'] === 'page_pics') 					{							if(is_array($contenu_page['contenu']) && sizeof($contenu_page['contenu']) >= 1){							$contenu_page['contenu']['liaison_jeu'] = $element['liaison_jeu']; // ajouté par sam							$this->galeries[$contenu_page['id_document']] = $contenu_page['contenu'];							unset($result[$key]['content']['contenu_page'][$key2]['contenu']);													}else if(array_key_exists('images', $contenu_page) && is_array($contenu_page['images']) && sizeof($contenu_page['images']) >= 1){							$contenu_page['images']['liaison_jeu'] = $element['liaison_jeu']; // ajouté par sam							$this->galeries[$contenu_page['id_document']] = $contenu_page['images'];							unset($result[$key]['content']['contenu_page'][$key2]['images']);												}else{							$pagesGaleriesVides[] = $element['id'];						}					}									if( isset($contenu_page['type']) && $contenu_page['type'] === 'fiche_de_jeu') {																						if( is_array($contenu_page['contenu']) && sizeof($contenu_page['contenu']) >= 1){
							$contenu_page['contenu']['url'] = $element['url'];							$this->fiches_de_jeu[$element['id']] = $contenu_page['contenu']; // ajouté par sam // remplace l'id du document par l'id de la page qui comporte le document
							//$this->fiches_de_jeu[$contenu_page['id_document']] = $contenu_page['contenu']; // commenté par sam : version à alex avec l'id du document qui se trouve dans une page							unset($result[$key]['content']['contenu_page'][$key2]['contenu']);						}else{							$pagesFicheJeuVides[] = $element['id'];						}					}									if( isset($contenu_page['type']) && $contenu_page['type'] === 'fiche_test') {	
						if( is_array($contenu_page['contenu']) && sizeof($contenu_page['contenu']) >= 1){
							$contenu_page['contenu']['url'] = $element['url']; // ajouté par sam
							$contenu_page['contenu']['liaison_jeu'] = $element['liaison_jeu']; // ajouté par sam
							$contenu_page['contenu']['name'] = $element['name']; // ajouté par sam							$this->fiches_test[$contenu_page['id_document']] = $contenu_page['contenu'];							unset($result[$key]['content']['contenu_page'][$key2]['contenu']);						}else{							$pagesFicheTestVides[] = $element['id'];						}					}										if(isset($contenu_page['type']) && $contenu_page['type'] === 'attachments' || isset($contenu_page['type']) && $contenu_page['type'] === 'multimedia') {												if( is_array($contenu_page['contenu']) && sizeof($contenu_page['contenu']) >= 1){							$this->attachments[$contenu_page['id_document']] = $contenu_page['contenu'];							unset($result[$key]['content']['contenu_page'][$key2]['contenu']);						}else{							$pagesAttachementVides[] = $element['id'];						}					}										if(isset($contenu_page['type']) && $contenu_page['type'] === 'video') {												if( is_array($contenu_page['contenu']) && sizeof($contenu_page['contenu']) >= 1){							$this->videos[$contenu_page['id_document']] = $contenu_page['contenu'];							unset($result[$key]['content']['contenu_page'][$key2]['contenu']);						}else{							$pagesVideoVides[] = $element['id'];						}					}					if(isset($contenu_page['type']) && $contenu_page['type'] === 'html_externe') {							if(sizeof($contenu_page['contenu']) >= 1){							$this->html_externe[$contenu_page['id_document']] = $contenu_page['contenu'];							unset($result[$key]['content']['contenu_page'][$key2]['contenu']);						}else{							$pagesHtmlExterneVides[] = $element['id'];						}					}				}			}		}		$this->arbre = $result;		$this->log->f('Creation des tableaux avec leur contenu : $genres, $jeux, $news', 'weight3');		try	{			// recupere un tableau des genres de jeux pour les fiches.			$req = $this->bdd->prepare('SELECT uid, type FROM tx_afgames_type');			$req->execute(array());			$req->setFetchMode(PDO::FETCH_ASSOC);			while ($result2 = $req->fetch())			{				$this->genres[$result2['uid']] = $result2['type'];			}						$req->closeCursor();				// recupere un tableau du listing des jeux.				$req = $this->bdd->prepare('SELECT gamekey, titre, studio FROM tx_afgames_game WHERE deleted=0 AND hidden=0');			$req->execute(array());			$req->setFetchMode(PDO::FETCH_ASSOC);			while ($result3 = $req->fetch())			{				$this->jeux[$result3['gamekey']] = array('titre' => $result3['titre'], 'studio' => $result3['studio']);			}						$req->closeCursor();					// recupere un tableau de news.			$req = $this->bdd->prepare('select n.uid, n.crdate, n.title, n.bodytext, n.author, n.tx_afnews_af_games, n.tx_rgmediaimages_config, n.tx_rgmediaimages_width, n.tx_rgmediaimages_height from tt_news as n inner join tt_news_cat_mm nc on nc.uid_local = n.uid where nc.uid_foreign = ? AND n.deleted=0 AND n.hidden=0 order by n.crdate asc');			$req->execute(array(NEWS_CATEGORY));			$req->setFetchMode(PDO::FETCH_ASSOC);			while ($result4 = $req->fetch())			{						if(isset($result4['tx_rgmediaimages_config']) && !empty($result4['tx_rgmediaimages_config']))				{					$youTube = '<embed type="application/x-shockwave-flash" src="'.$result4['tx_rgmediaimages_config'].'" width="'.$result4['tx_rgmediaimages_width'].'" height="'.$result4['tx_rgmediaimages_height'].'" id="sfwvideo" name="sfwvideo" bgcolor="#FFFFFF" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" flashvars="'.$result4['tx_rgmediaimages_config'].'">';				}				else				{					$youTube = '';				}						$this->news[] = array(										'id' => $result4['uid'],										'date_creation' => $result4['crdate'], 										'name' => $result4['title'], 										'author' => $this->getAuthor($result4['author']),										'liaison_news' => trim($result4['tx_afnews_af_games'], ','),										'content' => $result4['bodytext'] . '<br><br>' . $youTube										);			}						$req->closeCursor();   			}		catch(Exception $e)		{			die('Erreur : '.$e->getMessage());		}		$req->closeCursor();				$this->log->f('Fin Exportation', 'weight4');		return $this;			}							// Fonction de recuperation du contenu //	private function getContenu($pageId, $idPageRacine, $nomDeDomaine, $idAuthor, $nomPage)	{				$author = NULL;		$url = NULL;		$content = array();		$tabCompteur = array();				// Recuperation URL de la page		try		{			$req = $this->bdd->prepare('SELECT pagepath FROM tx_realurl_pathcache WHERE page_id=? AND rootpage_id=?');			$req->execute(array($pageId, $idPageRacine));			$req->setFetchMode(PDO::FETCH_ASSOC);			while ($result = $req->fetch())			{				if(isset($result['pagepath']) && !empty($result['pagepath']))				{					$url = $nomDeDomaine . $result['pagepath'];				}				else				{					$url = "Url non disponible pour cette page";				}			}			$req->closeCursor();						// Recuperation du contenu de la page			$req = $this->bdd->prepare('SELECT uid as id_document, CType, header, bodytext, image, media, list_type, select_key, multimedia, pi_flexform, tx_templavoila_to, tx_templavoila_flex, tx_afAuthor_author FROM tt_content WHERE pid=? AND deleted=0 AND hidden=0 ORDER BY sorting ASC');			$req->execute(array($pageId));			$req->setFetchMode(PDO::FETCH_ASSOC);						while ($result = $req->fetch())			{							if(isset($result['CType']) && !empty($result['CType']))				{					if(isset($result['tx_afAuthor_author']) && !empty($result['tx_afAuthor_author']))					{						$tabCompteur[] = $result['tx_afAuthor_author'];					}										$content['aide'] = '<p style="background-color:orange; padding:10px; border:5px yellow solid; color:black;">ID Page Typo Backend : <strong style="bolder;">'.$pageId.'</strong> , Lien Typo Frontend : <a target="_blank" href="'.$url.'">Lien de la page, cliquez dessus pour visionner</a> : '.$url.'<br>Cadre Orange d aide a supprimer lorsque la page est entierement corrige comme tout les cadres oranges rencontre dans les different contenu.</p><br>';													// Contenu templavoila					if($result['CType'] === "templavoila_pi1")					{ 										if($result['tx_templavoila_to'] === "20")						{							$content['contenu_page'][] = array(	'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>',																'contenu' =>  			$this->parseContenu($result['tx_templavoila_flex'], $result['CType'], $result['tx_templavoila_to']),																'type' 		=>			'fiche_de_jeu',																'id_document' =>		$result['id_document']															);																					}						else if($result['tx_templavoila_to'] === "28")						{							$content['contenu_page'][] = array(	'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>',																'contenu' =>  			$this->parseContenu($result['tx_templavoila_flex'], $result['CType'], $result['tx_templavoila_to']),																'type' 		=>			'fiche_test',																'id_document' =>		$result['id_document']															);															}						else						{							$content['contenu_page'][] = array(	'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>',																'contenu' =>  			$result['tx_templavoila_flex'],																'type' 		=>			'unknow_templavoila_pi1',																'id_document' =>		$result['id_document']															);						}											// Contenu textes/images/html/multimedia					}					else if($result['CType'] === "text" || $result['CType'] === "textpic" || $result['CType'] === "html" || $result['CType'] === "multimedia")					{						if($result['CType'] !== "textpic")						{							if($result['CType'] !== "multimedia")							{								if($result['CType'] === "html" && $result['header'] === "importation" || $result['CType'] === "html" && $result['header'] === "Importation")								{									$video = explode(',',$result['bodytext']);									$content['contenu_page'][]  = array(																		'titre_contenu' => 		'',																		'contenu' => 			$video,																		'type'		=> 			'video',																		'id_document' =>		$result['id_document']																	);								}								else								{									$content['contenu_page'][]  = array(																		'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>',																		'contenu' => 			$result['bodytext'],																		'type'		=> 			'page',																		'id_document' =>		$result['id_document']																	);								}							}							else							{								$content['contenu_page'][]  = array(																	'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>',																	'contenu' =>  			$this->parseContenu($result['multimedia'],'media'),																	'type'		=> 			'multimedia',																	'id_document' =>		$result['id_document']																);							}						}						else						{							$content['contenu_page'][]  = array(																'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>',																'contenu' => 			$result['bodytext'],																'images' =>  			array(																							  'nom' => $nomPage,																							  'image' => $this->parseContenu($result['image'],'image')																						),																'type'		=> 			'page_pics',																'id_document' =>		$result['id_document']															);												}										 // Galerie native					}					else if($result['CType'] === "image" )					{						$content['contenu_page'][]  = array(															'titre_contenu' => 		'<h2>'. $result['header'] . '</h2>',															'contenu' => array (																				'nom' => $nomPage,																				'image' => $this->parseContenu($result['image'], $result['CType'])																			   ),															'type'		=> 			'gallery',															'id_document' =>		$result['id_document']														);																			 //  WT Gallery					}					else if( $result['CType'] === "list" && $result['list_type'] === "wt_gallery_pi1")					{						$content['contenu_page'][]  = array(															'titre_contenu' => 		'<h2>'. $result['header'] . '</h2>',															'contenu' => array (																				'nom' => $nomPage,																				'image' => $this->parseContenu($result['pi_flexform'], $result['CType'])																			   ),															'type'		=> 			'gallery',															'id_document' =>		$result['id_document']														);																// Contenu listes														}					else if($result['CType'] === "list" && $result['list_type'] === "rgmediaimages_pi1" || $result['CType'] === "list" && $result['list_type'] === "external_pageset_pi1")					{						if($result['CType'] === "list" && $result['list_type'] === "rgmediaimages_pi1")						{							$content['contenu_page'][]  = array( 																'titre_contenu' => 		'<h2>'. $result['header'] . '</h2>',																'contenu' => 			$this->parseContenu($result['pi_flexform'], 'video'),																'type'		=> 			'video',																'id_document' =>		$result['id_document']															);													}						else						{													$content['contenu_page'][]  = array(																'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>', 																'contenu' => 			$this->parseContenu($result['select_key'], 'externe'),																'type'		=> 			'html_externe',																'id_document' =>		$result['id_document']															);						}									// Contenu uploads					}					else if($result['CType'] === "uploads")					{ 						$content['contenu_page'][]  = array(															'titre_contenu' => 		'<h2>' . $result['header'] . '</h2>', 															'contenu' => 			$this->parseContenu($result['media'], 'media'), 															'type'		=>			'attachments',															'id_document' =>		$result['id_document']															);																}					else					{						$content['contenu_page'][]  = array('error' => '<p style="background-color:orange; padding:10px; border:5px yellow solid; color:black;">Un element du contenu dans la page est manquant ( voir tout les elements de la page si celle ci est completement vide ), il n est soit pas exportable par le script soit, c est un cas particulier.<br><br>Voici, le titre de cet element manquant : "<strong style="bolder;">'.$result['header'].'</strong>" ( Si les guillemets restent vide, cela veut dire que soit il n y a pas de titre, soit il est cache ).<br>Vous devez donc vous rendre sur typo a la page id : "<strong style="bolder;">'.$pageId.'</strong>", contenu : "<strong style="bolder;">'.$result['id_document'].'</strong>", pour controler la page et son contenu. ( Attention, ce n est parce que il y a ce message d erreur que le contenu doit forcement etre recuperer,<br>sa peut tres bien etre un menu, un script ou autre qu il faut ignorer et dans ce cas, effacer juste ce message orange ).<br><br>Vous avez a votre disposition le lien de la page en question pour comparer le rendu du contenu : <a target="_blank" href="'.$url.'">voir le contenu de cette page</a><br><br>Si vous desirez recuperer le contenu manquant de cette page, il faut le faire manuellement, si la page ne sert a rien meme pour une arborescence, vous pouvez la supprimer.<br>( A verifier tout de meme avant de la supprimer dans le cas ou la page ne sert a rien dans la nouvelle arborescence sur wordpress, l arborescence sur typo, on s en moque puisque pas le meme menu ).<br><br>Si vous gardez cette page, pensez a effacer les messages d avertissement/aide qui sont dans un cadre orange comme celui-ci.</p>');										}									}				else				{					$content['contenu_page'][]  = array('error' => '<p style="background-color:orange; padding:10px; border:5px yellow solid; color:black;">Contenu non parser, verifier la page en utilisant le lien : <a target="_blank" href="'.$url.'">voir le contenu de cette page</a>, si la page doit etre exporte, voici son id sur typo : "<strong style="bolder;">'.$pageId.'</strong>", il faut le faire manuellement sinon, si la page ne sert pas, meme pour une arborescence ( a verifier tout de meme ), vous pouvez la supprimer<br>Si vous gardez cette page, pensez a effacer ce message d avertissement</p>');				}			}									$req->closeCursor();						if(isset($tabCompteur) && !empty($tabCompteur))			{				$tabCompteur = array_count_values($tabCompteur);				foreach($tabCompteur as $key => $value)				{					if($value === max($tabCompteur))					{						$idAuthor = $key;					}				}							}						$author = $this->getAuthor($idAuthor);					}		catch(Exception $e)		{			die('Erreur : '.$e->getMessage());		}				return array($author, $url, $content);			}		// Fonction de parsage des images pour la correction des chemins //	private function parseContenu($contenu, $option, $type = null)	{				if(isset($contenu) && !empty($contenu)){			switch ($option){				case 'templavoila_pi1':					$rendu = $this->getParseFiche($contenu, $type);				break; 				case 'media':					$tabExplode = explode(",", $contenu);					foreach($tabExplode as $value)					{						$rendu[] = "/uploads/media/" . $value;					}				break;				case 'list':					$chemin = $this->getWTPath($contenu);					$rendu = $this->listingImage($chemin);				break;				case 'image':					$tabExplode = explode(",", $contenu);					foreach($tabExplode as $value)					{						$rendu[] = "uploads/pics/" . $value;					}				break;				case 'video':						$rendu = $this->getVideo($contenu);				break;				case 'externe':					$rendu = "fileadmin/articles/" . $contenu . ".html";				break;				default:					$rendu[] = $contenu;			}					return $rendu;				}	}		// Fonction de listing d'un repertoire d'image d'un site complet, retourne un tableau de reference des chemins d'images //	private function listingImage($chemin)	{		$url = 'http://www.jeux-strategie.com/_getImages.php?chemin='.$chemin;		$curl = curl_init();				curl_setopt_array($curl, array(			CURLOPT_URL => $url,			CURLOPT_HEADER => false,			CURLOPT_RETURNTRANSFER => true		));				$json = curl_exec($curl);		curl_close($curl);		$images = json_decode($json) ;				for($i=0 ; $i<sizeof($images); $i++)		{			$images[$i] = $chemin.$images[$i];		}				return $images;	}			// récucupère les images situées à l'emplacement spécifé par le flexform	public function getWTPath($xml)	{		$xmlDoc = new DOMDocument();		$xmlDoc->loadXML( $xml );				//$idformated = date('Ymd_His', $_SESSION['user_id']);		$path = new DOMXPath($xmlDoc);		$chemin = "//field[@index='path']/value";		$client = $path->query($chemin)->item(0);				return $client->nodeValue;		}	// parse le flexform des fiches de jeu/notation 	private function getParseFiche($xml, $typeFiche)	{			$xmlDoc = new DOMDocument();		$xmlDoc->loadXML( $xml );		$path = new DOMXPath($xmlDoc);		 	// parse fiche de jeu	if($typeFiche === "20")	{						$illustration = $path->query("//field[@index='field_image']/value")->length;			$illustrationGalerie = $path->query("//field[@index='field_image_galerie']/value")->length;			$galerie = $path->query("//field[@index='field_url_galerie']/value")->length;					if(!empty($illustration) || $illustration != 0)			{				if(!empty($path->query("//field[@index='field_image']/value")->item(0)->nodeValue))				{					$illustration = '/uploads/tx_templavoila/'.$path->query("//field[@index='field_image']/value")->item(0)->nodeValue;				}				else				{					$illustration = "";				}			}			else			{				$illustration = "";			}						if(!empty($illustrationGalerie) || $illustrationGalerie != 0)			{				if(!empty($path->query("//field[@index='field_image_galerie']/value")->item(0)->nodeValue))				{					$illustrationGalerie = '/uploads/tx_templavoila/'.$path->query("//field[@index='field_image_galerie']/value")->item(0)->nodeValue;				}				else				{					$illustrationGalerie = "";				}			}			else			{				$illustrationGalerie = "";			}						if(!empty($galerie) || $galerie != 0)			{				if(!empty($path->query("//field[@index='field_titre_galerie']/value")->item(0)->nodeValue))				{					$galerie = '<a href="'.$this->getConvertUrl($path->query("//field[@index='field_url_galerie']/value")->item(0)->nodeValue).'">'.$path->query("//field[@index='field_titre_galerie']/value")->item(0)->nodeValue.'</a>';				}				else				{					$galerie = "";				}			}			else			{				$galerie = "";			}			$ficheJeu = array(		'nomJeu' => $path->query("//field[@index='field_titre']/value")->item(0)->nodeValue,									'illustration' => $illustration,									'dateSortie' => $path->query("//field[@index='field_datefr']/value")->item(0)->nodeValue,									'developpeur' => '<a href="'.$this->getConvertUrl($path->query("//field[@index='field_concepteur_url']/value")->item(0)->nodeValue).'">'.$path->query("//field[@index='field_concept']/value")->item(0)->nodeValue.'</a>',									'editeur' => '<a href="'.$this->getConvertUrl($path->query("//field[@index='field_editeur_url']/value")->item(0)->nodeValue).'">'.$path->query("//field[@index='field_editeur']/value")->item(0)->nodeValue.'</a>',									'site' => '<a href="'.$this->getConvertUrl($path->query("//field[@index='field_url']/value")->item(0)->nodeValue).'">'.$path->query("//field[@index='field_titre_url_site']/value")->item(0)->nodeValue.'</a>',									'note' => $path->query("//field[@index='field_note_globale']/value")->item(0)->nodeValue,									'newsType' => explode(',', $path->query("//field[@index='field_newsJS']/value")->item(0)->nodeValue),									'illustrationGalerie' => $illustrationGalerie,									'galerie' => $galerie						);									$nbreLien = $path->query("//field[@index='field_titre_lien']/value")->length;			$nbreGenre = $path->query("//field[@index='field_genre']/value")->length;						$genLien = array();			$genGenre = array();												for($i=0; $i<$nbreLien; $i++)			{					$genLien['lien'][] = '<a href="'.$this->getConvertUrl($path->query("//field[@index='field_url_lien']/value")->item($i)->nodeValue).'">'.$path->query("//field[@index='field_titre_lien']/value")->item($i)->nodeValue.'</a>';			}			for($i=0; $i<$nbreGenre; $i++)			{				$genGenre['genre'] = explode(',', $path->query("//field[@index='field_genre']/value")->item($i)->nodeValue);			}						$ficheJeu = array_merge_recursive($ficheJeu, $genLien);			$ficheJeu = array_merge_recursive($ficheJeu, $genGenre);						return $ficheJeu;					}		else if($typeFiche === "28") // parse fiche test		{ 					$lesPlusLength = $path->query("//field[@index='field_texte_plus']/value")->length;			$lesMoinsLength = $path->query("//field[@index='field_texte_moins']/value")->length;						$lesPlus = array();			$lesMoins = array();						for($i=0; $i<$lesPlusLength; $i++)			{				$lesPlus[] = $path->query("//field[@index='field_texte_plus']/value")->item($i)->nodeValue;			}						for($i=0; $i<$lesMoinsLength; $i++)			{				$lesMoins[] = $path->query("//field[@index='field_texte_moins']/value")->item($i)->nodeValue;			}					$ficheTest = array(		'lesPlus' => $lesPlus,									'lesMoins' => $lesMoins,									'configuration' => $path->query("//field[@index='field_config']/value")->item(0)->nodeValue,									'notation' => array(																 'jouabiliteNote' => $path->query("//field[@index='field_notej']/value")->item(0)->nodeValue,															 				'jouabiliteComment' => $path->query("//field[@index='field_commentj']/value")->item(0)->nodeValue,															 				'graphismeNote' => $path->query("//field[@index='field_noteg']/value")->item(0)->nodeValue,															 				'graphismeComment' => $path->query("//field[@index='field_commentg']/value")->item(0)->nodeValue,															 				'bandeSonNote' => $path->query("//field[@index='field_noteb']/value")->item(0)->nodeValue,															 				'bandeSonComment' => $path->query("//field[@index='field_commentb']/value")->item(0)->nodeValue,															 				'multiNote' => $path->query("//field[@index='field_notem']/value")->item(0)->nodeValue,															 				'multiComment' => $path->query("//field[@index='field_commentm']/value")->item(0)->nodeValue,															 				'soloNote' => $path->query("//field[@index='field_notes']/value")->item(0)->nodeValue,															 				'soloComment' => $path->query("//field[@index='field_comments']/value")->item(0)->nodeValue,														),									'noteGlobale' => $path->query("//field[@index='field_note_globale']/value")->item(0)->nodeValue,									'commentaire' => $path->query("//field[@index='field_comment_note']/value")->item(0)->nodeValue						);									return $ficheTest;				}		else		{					return $xml;					}	}		// retrouve l'url des fiches templavoila quand se sont les id qui sont retournés. 	private function getConvertUrl($id)	{		if(is_numeric($id)){					try{					$req = $this->bdd->prepare('SELECT rootpage_id, pagepath FROM tx_realurl_pathcache WHERE page_id=?');					$req->execute(array($id));					$req->setFetchMode(PDO::FETCH_ASSOC);					while ($result = $req->fetch()){										if(!empty($result))						{							switch ($result['rootpage_id']){								case '1504':								$id = "http://www.jeux-strategie.com/".$result['pagepath'];								break;								case '6631':								$id = "http://www.starcraft2france.com/".$result['pagepath'];								break;								case '148':								$id = "http://www.strategium-alliance.com/".$result['pagepath'];								break;								case '57':								$id = "http://www.39-45strategie.com/".$result['pagepath'];								break;								case '216':								$id = "http://www.ages-strategie.com/".$result['pagepath'];								break;								case '2222':								$id = "http://www.ageofempires3-alliance.com/".$result['pagepath'];								break;								case '3892':								$id = "http://www.supremecommander-alliance.com/".$result['pagepath'];								break;								case '1090':															$id = "http://www.warcraft3france.com/".$result['pagepath'];								break;								case '1':															$id = "http://www.worldofwarcraft-alliance.com/".$result['pagepath'];								break;							}												}						else						{							switch ($id){								case '1504':								$id = "http://www.jeux-strategie.com/";								break;								case '6631':								$id = "http://www.starcraft2france.com/";								break;								case '148':								$id = "http://www.strategium-alliance.com/";								break;								case '57':								$id = "http://www.39-45strategie.com/";								break;								case '216':								$id = "http://www.ages-strategie.com/";								break;								case '2222':								$id = "http://www.ageofempires3-alliance.com/";								break;								case '3892':								$id = "http://www.supremecommander-alliance.com/";								break;								case '1090':								$id = "http://www.warcraft3france.com/";								break;								case '1':								$id = "http://www.worldofwarcraft-alliance.com/";								break;							}												}					}					$req->closeCursor();									}catch(Exception $e){					die('Erreur : '.$e->getMessage());				}				}				return $id;	}		// retourne les liens vidéos rgmediaimages_pi1. 	private function getVideo($xml){				$xmlDoc = new DOMDocument();		$xmlDoc->loadXML( $xml );		$path = new DOMXPath($xmlDoc);				$exist = $path->query("//field[@index='url']/value")->length;				if($exist >= 1){			$tab = preg_split("#\s#", $path->query("//field[@index='url']/value")->item(0)->nodeValue);			return($tab);		}else{			return '';		}		}		// Recuperation nom de l'auteur de la page	private function getAuthor($id){				try{			$req = $this->bdd->prepare('SELECT username FROM be_users WHERE uid=?');			$req->execute(array($id));			$req->setFetchMode(PDO::FETCH_ASSOC);			while ($result = $req->fetch()){				if(isset($result['username']) && !empty($result['username'])){					$author = $result['username'];				}else{					$author = "Le nom de l'auteur n'est pas present en base de donnee";				}			}			return($author);			$req->closeCursor();					}		catch(Exception $e){			die('Erreur : '.$e->getMessage());		}				}}	?>
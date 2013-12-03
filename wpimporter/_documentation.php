<?php
//asdf
// On fait quoi pour le contenu qui est dans du html externe ?


$data = array(

	'arbre' => array( // ARBRE
		0 => array(	'id' => '2024'
					'id_parent' => '5599'
					'name' => 'L’Histoire du jeu de stratégie'
			        'date_creation' => '1260642198'
			        'author' => 'pmh'
			        'keywords' => 
			        'description' => 
			        'url' => 'http://www.jeux-strategie.com/articles/lhistoire-du-jeu-de-strategie'
					'liaison_jeu' => '8384' // id du jeu lié
			        'content' => array(
						'aide' => '...',
						'contenu_page' => array(
							0 => array(  // Etre attentif, ce modèle est fait pour les contenu de type : fiche_de_jeu, fiche_test, gallery, attachments, video, multimedia, html_externe
								'error' => '...',
								'titre_contenu' => '...',
								'type' => '...',
								'id_document' => '...', // référence des galeries et urls
							),
							1 => array( // Etre attentif, ce modèle est fait pour les contenu de type : page_pics
								'error' => '...',
								'titre_contenu' => '...',
								'contenu' => '...',
								'images' => '...',
								'type' => '...',
								'id_document' => '...',
							),
							2 => array(  // Etre attentif, ce modèle est fait pour les contenu de type : page
								'error' => '...',
								'titre_contenu' => '...',
								'contenu' => '...',
								'type' => '...',
								'id_document' => '...',
								
							),
							...
						)
			        ),
			        'children' => array()
		),
		
		...
		
	), 
	
	'fiches_de_jeu' => array(  // FICHE DE JEU
		'13904' => array(
						'nomJeu' => 'Starcraft 2',
						'illustration' => '/uploads/tx_templavoila/ss76-hires_02.jpg',
						'dateSortie' => '27 juillet 2010',
						'developpeur' => 'Blizzard',
						'editeur' => 'Blizzard',
						'site' => 'Starcraft 2 France',
						'note' => '10',
						'newsType' => 'SC2',
						'illustrationGalerie' => '/uploads/tx_templavoila/ss70-hires.jpg',
						'galerie' => 'lien galerie',
						'lien' => 	array(
										0 => 'Notre Forum',
										1 => 'Notre Dossier',
										...
									),
						'genre' =>  array(
										0 => '14',
										1 => '8',
										...
									)
		),

    'fiches_test' => array(  // FICHE DE TEST
        '13729' => array(
                    'nom' = "...",
                    'liaison_jeu' = array(),
                    'lesPlus' => array(
                                    0 => "...",
                                    1 => '...',
                                    ...
                                ),
                    'lesMoins' => array(
                                    0 => "...",
                                    1 => '...',
                                    ...
                                ),
                    'configuration' => '...',
                    'notation' => array(
                                    'jouabiliteNote' => '16',
                                    'jouabiliteComment' => '...',
                                    'graphismeNote' => '15',
                                    'graphismeComment' => '...',
                                    'bandeSonNote' => '16',
                                    'bandeSonComment' => '...',
                                    'multiNote' => '12',
                                    'multiComment' => '...',
                                    'soloNote' => '13',
                                    'soloComment' => 'Campagne plate et monotone'
                                ),
                    'noteGlobale' => '3',
                    'commentaire' => '...'
        ),

        '13730' => array(
            ...
        ),

        ...
    ),
	
	
	'galeries' => array( // GALERIES
		'11355' => 	array(
						'liaison_jeu' => array(),
						'nom' => '...',
						'image' => array(
										0 => 'uploads/.../img2',
										1 => 'uploads/.../img2',
										...
										)
					),	
		'11356' => 	array(
						'liaison_jeu' => array(),
						'nom' => '...',
						'image' => array(
										0 => 'uploads/.../img2',
										1 => 'uploads/.../img2',
										...
										)
					),
		...
	),
	
	'attachments' => array( // PIECE JOINTES
		'10555' => 	array(
						0 => 'uploads/.../mp3',
						1 => 'uploads/.../mp3',
						...
					),	
		'10556' =>  array(
						0 => 'uploads/.../mp3',
						1 => 'uploads/.../mp3',
						...
					),
		...
	),
	
	'video' => array( // VIDEO
		'14462' => 	array(
						0 => 'http://www.youtube.com/watch?v=QqhpJE-IWyc',
						1 => 'http://www.jeux-strategie.com/fileadmin/video/Diablo3/archiviste/oragenda.flv',
						...
					),	
		'14663' =>  array(
						0 => 'http://www.jeux-strategie.com/fileadmin/video/Diablo3/archiviste/biblioquete.flv',
						1 => 'http://www.youtube.com/watch?v=xv9R_9jT0sM&hd=1',
						...
					),
		...
	),
	
	
	'jeux' => array( // TYPE DE JEU / NEWS TYPE
		'SC2' => array(
					'titre' => 'Starcraft 2',
					'studio' => 'Blizzard'
				),
		'ta' => array(
					'titre' => 'total annihilation',
					'studio' => 'Cavedog'
				),
		...
	),
	
	
	'genres' => array( // GENRES DE JEU
		'19' => 'Action',
		'23' => 'Fantaisie',
		...
	)
	
	'html_externe' => array( // ARTICLE HTML EXTERNE
		'1854' => 'fileadmin/articles/JS/2035.html',
		'1856' => 'fileadmin/articles/JS/1399.html',
		...
	)
	
	
	'news'	=> array(
		0 => array(
			'id' =>  => '...',
			'date_creation' =>  '...',
			'name' => '...',
			'author' => '...',
			'liaison_news' => '...', 
			'content' => '...'			
 		)
		...
	), 


)
?>
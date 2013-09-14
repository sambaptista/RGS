<?php

/**
 *  Installation des Add-ons
 *  
 *  Le code suivant incluera les 4 Add-ons premium dans votre thème.
 *  N'essayez pas d'inclure un fichier qui n'existe pas sous peine de rencontrer des erreurs.
 *  
 *  Tous les champs doivent être inclus durant l'action 'acf/register_fields'.
 *  Les autres Add-ons (comme la page Options) peuvent être inclus en dehors de cette action.
 *  
 *  Vous devez placer un dossier add-ons dans votre thème afin que le code suivant fonctionne correctement.
 *
 *  IMPORTANT
 *  Les Add-ons peuvent être inclus dans un thème premium à condition de respecter les termes du contrat de licence ACF.
 *  Cependant, ils ne doivent pas être inclus dans une autre extension gratuite ou premium. 
 *  Pour plus d'informations veuillez consulter cette page http://www.advancedcustomfields.com/terms-conditions/
 */ 

// Champs 
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	//include_once('add-ons/acf-repeater/repeater.php');
	//include_once('add-ons/acf-gallery/gallery.php');
	//include_once('add-ons/acf-flexible-content/flexible-content.php');
}

// Page d‘options 
//include_once( 'add-ons/acf-options-page/acf-options-page.php' );


/**
 * Enregistrez des groupes de champs
 * La fonction register_field_group accepte 1 tableau qui contient les données nécessaire à l‘enregistrement d'un groupe de champs
 * Vous pouvez modifier ce tableau selon vos besoins. Cela peut toutefois provoquer des erreurs dans les cas où le tableau ne serait plus compatible avec ACF
 */

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_biblioth%c3%a8que-de-post-du-forum',
		'title' => 'Bibliothèque de post du forum',
		'fields' => array (
			array (
				'key' => 'field_5193a08fcf5f9',
				'label' => 'ID de la section du forum',
				'name' => 'id_de_la_section_du_forum',
				'type' => 'number',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'jeux',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_biblioth%c3%a8que-de-replays',
		'title' => 'Bibliothèque de replays',
		'fields' => array (
			array (
				'key' => 'field_51939c4dc26a1',
				'label' => 'Replays',
				'name' => 'replays',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_51939c4dc26cc',
						'label' => 'Carte',
						'name' => 'carte',
						'type' => 'image',
						'save_format' => 'object',
						'preview_size' => 'thumbnail',
					),
					array (
						'key' => 'field_51939c4dc26e3',
						'label' => 'Fichier Replay',
						'name' => 'fichier_replay',
						'type' => 'file',
						'save_format' => 'url',
					),
					array (
						'key' => 'field_51939c4dc26f7',
						'label' => 'Description',
						'name' => 'description',
						'type' => 'wysiwyg',
						'toolbar' => 'full',
						'media_upload' => 'yes',
						'the_content' => 'yes',
					),
				),
				'row_min' => 0,
				'row_limit' => '',
				'layout' => 'row',
				'button_label' => 'Ajouter un replay',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'jeux',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_biblioth%c3%a8que-de-t%c3%a9l%c3%a9chargements',
		'title' => 'Bibliothèque de téléchargements',
		'fields' => array (
			array (
				'key' => 'field_51939da97a37c',
				'label' => 'Téléchargement',
				'name' => 'telechargement',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_51939da97a39b',
						'label' => 'Fichier à télécharger',
						'name' => 'fichier_à_telecharger',
						'type' => 'file',
						'save_format' => 'id',
					),
					array (
						'key' => 'field_51939da97a3ae',
						'label' => 'Description',
						'name' => 'description',
						'type' => 'wysiwyg',
						'toolbar' => 'full',
						'media_upload' => 'yes',
						'the_content' => 'yes',
					),
				),
				'row_min' => 0,
				'row_limit' => '',
				'layout' => 'row',
				'button_label' => 'Ajouter un téléchargement',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'jeux',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_biblioth%c3%a8que-de-vid%c3%a9os',
		'title' => 'Bibliothèque de vidéos',
		'fields' => array (
			array (
				'key' => 'field_5193a10091e2d',
				'label' => 'Vidéos',
				'name' => 'videos',
				'type' => 'flexible_content',
				'layouts' => array (
					array (
						'label' => 'Vidéo Youtube',
						'name' => 'video_youtube',
						'display' => 'table',
						'sub_fields' => array (
							array (
								'key' => 'field_5193a21748533',
								'label' => 'Lien vers la vidéo Youtube',
								'name' => 'vidéo_youtube',
								'type' => 'text',
								'order_no' => 0,
								'instructions' => '',
								'required' => 0,
								'id' => 'acf-field-vidéo_youtube',
								'class' => 'text',
								'conditional_logic' => array (
									'status' => 0,
									'allorany' => 'all',
									'rules' => 0,
								),
								'default_value' => '',
								'formatting' => 'none',
							),
							array (
								'key' => 'field_5193a21748543',
								'label' => 'Description',
								'name' => 'description',
								'type' => 'wysiwyg',
								'order_no' => 1,
								'instructions' => '',
								'required' => 0,
								'id' => 'acf-field-description',
								'class' => 'wysiwyg',
								'conditional_logic' => array (
									'status' => 0,
									'allorany' => 'all',
									'rules' => 0,
								),
								'toolbar' => 'full',
								'media_upload' => 'yes',
								'the_content' => 'yes',
							),
						),
					),
					array (
						'label' => 'Vidéo iframe ou embed',
						'name' => 'video_iframe_ou_embed',
						'display' => 'table',
						'sub_fields' => array (
							array (
								'key' => 'field_5193a21748554',
								'label' => 'Code de la vidéo',
								'name' => 'code_de_la_video',
								'type' => 'textarea',
								'order_no' => 0,
								'instructions' => '',
								'required' => 0,
								'id' => 'acf-field-code_de_la_video',
								'class' => 'textarea',
								'conditional_logic' => array (
									'status' => 0,
									'allorany' => 'all',
									'rules' => 0,
								),
								'default_value' => '',
								'formatting' => 'none',
							),
							array (
								'key' => 'field_5193a21748568',
								'label' => 'Description',
								'name' => 'description',
								'type' => 'wysiwyg',
								'order_no' => 1,
								'instructions' => '',
								'required' => 0,
								'id' => 'acf-field-description',
								'class' => 'wysiwyg',
								'conditional_logic' => array (
									'status' => 0,
									'allorany' => 'all',
									'rules' => 0,
								),
								'toolbar' => 'full',
								'media_upload' => 'yes',
								'the_content' => 'yes',
							),
						),
					),
				),
				'sub_fields' => array (
					array (
						'key' => 'field_5193a1e0153da',
					),
					array (
						'key' => 'field_5193a1e0153c7',
					),
				),
				'button_label' => '+ Ajouter une vidéo',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'jeux',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_evaluation-2',
		'title' => 'Evaluation',
		'fields' => array (
			array (
				'key' => 'field_51939cf7880ab',
				'label' => 'Evaluation',
				'name' => 'evaluation',
				'type' => 'number',
				'instructions' => 'Sur 5 points',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'jeux',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

?>
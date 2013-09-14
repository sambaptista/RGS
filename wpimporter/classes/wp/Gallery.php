<?php



class Gallery
{
	public $log;
	public static $galeries;
	
	private static $urls;
	
	
	public function __construct($array, $log)
	{
		$this->log = $log;
		self::$galeries = $array;
		
		$this->log->f('Galeries', 'weight3');
		$this->insertGaleries();
		
	}
	

	
	public function insertGaleries()
	{		
		$i =1;
		$nbGaleries = sizeof(self::$galeries);				
		foreach(self::$galeries as $id => $galerie)
		{
			$this->log->f('Galerie n° '.$id.', '.$i.'e/'.$nbGaleries, 'weight2');
			
			if( is_array($galerie['image']) && sizeof($galerie['image']) > 0 )
			{
				$post_id = wp_insert_post( array(
					'post_author' => 1, 
					'post_title' => $galerie['nom'],
					'post_status' => 'publish',											
					'post_type' => 'gallery'
					)
				);
			
				if($post_id == 0) exit('La galerie '.$id.' n\'a pas été inserée');

				self::$galeries[$id] = array
				(
					'wp_post_id' => $post_id,
					'galerie' => $galerie['image']
				);
					


				/****************************************************************
					Jeux liés à la galerie
				*****************************************************************/

				if( sizeof($galerie['liaison_jeu']) > 0)
				{				
					$values = array();
					foreach($galerie['liaison_jeu'] as $jeu_lie)					
						if(is_numeric($jeu_lie))
							$values[] = FichesJeu::$fiches[$jeu_lie]['wp_post_id'];	
					update_field( ACF_JEUX_LIES , $values, $post_id );
				}
				
				
				
				
				/****************************************************************
					Importe dans wordpress
				*****************************************************************/
				foreach($galerie['image'] as $pict_id => $pict)
				{
					$cheminArray = explode('/', $pict);
					$name = array_pop($cheminArray);
					$chemin = implode('/', $cheminArray);
				
					//$this->log->f('download img :   http://www.jeux-strategie.com/'.$chemin.'/'.str_replace(' ', '%20', $name), 'weight1');
					self::fetch_media(	   		     'http://www.jeux-strategie.com/'.$chemin.'/'.str_replace(' ', '%20', $name), Tools::cleanFilename($name, $time), $galerie_id );
				
					if($pict_id >= NB_IMG_PAR_GALERIE) break;
				}
			}

			if($i >= NB_GALERIES) break;
			$i++;
		}
		
	}
	

	
	
	
	/** Import media from url
	 *
	 * @param string $file_url URL of the existing file from the original site
	 * @param int $post_id The post ID of the post to which the imported media is to be attached
	 *
	 * @return boolean True on success, false on failure
	 */

	public static function fetch_media($file_url, $new_filename, $post_id =0) 
	{
		global $wpdb;
		
		// si le fichier a déjà été importé, on retourne directement l'id
		$id_fichier_local = $file_url;
		if( isset( self::$urls[$id_fichier_local] )) {		
			$message = 'Fichier utilisé à double : '.$id_fichier_local;
			error_log($message.chr(10).__LINE__.", ".__FILE__.chr(10).chr(10), 3, $_SERVER['DOCUMENT_ROOT'].'/_debug_error_log.txt');
			return self::$urls[$id_fichier_local];
		}
		
		$log = Log::getInstance();
		
		//		if(!$post_id) {
		//			return false;
		//		}
				
		//directory to import to	
		$artDir = ABS_PATH.'/wp-content/uploads/2013/05/';

		//if the directory doesn't exist, create it	
		if(!file_exists(ABSPATH.$artDir)) {
			mkdir(ABSPATH.$artDir);
		}

		//rename the file... alternatively, you could explode on "/" and keep the original file name
		$ext = array_pop(explode(".", $file_url));

		if (@fclose(@fopen($file_url, "r"))) { //make sure the file actually exists
			copy($file_url, ABSPATH.$artDir.$new_filename);


			$siteurl = get_option('siteurl');
			$file_info = getimagesize(ABSPATH.$artDir.$new_filename);

			//create an array of attachment data to insert into wp_posts table
			$artdata = array();
			$artdata = array
			(
				'post_author' => 1, 
				'post_date' => current_time('mysql'),
				'post_date_gmt' => current_time('mysql'),
				'post_title' => $new_filename, 
				'post_status' => 'inherit',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)),											
				'post_modified' => current_time('mysql'),
				'post_modified_gmt' => current_time('mysql'),
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'guid' => $siteurl.'/'.$artDir.$new_filename,
				'post_mime_type' => $file_info['mime'],
				'post_excerpt' => '',
				'post_content' => ''
			);

			$uploads = wp_upload_dir();
			$save_path = $uploads['basedir'].'/2013/05/'.$new_filename;
			
			//insert the database record
			$attach_id = wp_insert_attachment( $artdata, $save_path, $post_id );

			// mise en mémoire pour éviter de réimporter le fichier ultérieurement
			self::$urls[$id_fichier_local] = $attach_id;
			
			
			//generate metadata and thumbnails
			if ($attach_data = wp_generate_attachment_metadata( $attach_id, $save_path)) {
				wp_update_attachment_metadata($attach_id, $attach_data);
			}

			//optional make it the featured image of the post it's attached to
			$rows_affected = $wpdb->insert($wpdb->prefix.'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));
			
			return $attach_id;
			
		}
		else {
			return false;
		}

		return true;
	}
	
	
	
	
	
	
}

?>
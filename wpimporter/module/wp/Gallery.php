<?php


class Gallery
{
    public static $post_type = 'gallery';
    public static $urls = array();

    //	public function __construct($gallery, $typoid)
    //	{
    //        f('Galerie n° '.$gallery['id'], 'weight2');
    //        self::$gallery=$gallery;
    //        self::$save();
    //	}

    public function getGallery()
    {
        return $this->gallery;
    }







    /** Import media from url
     *
     * @param string $file_url URL of the existing file from the original site
     * @param $new_filename
     * @param int $post_id     The post ID of the post to which the imported media is to be attached
     *
     * @return boolean True on success, false on failure
     */

    public static function fetch_media($file_url, $new_filename, $post_id = 0)
    {
        //global $wpdb;

        // si le fichier a déjà été importé, on retourne directement l'id
        if (isset(self::$urls[$file_url])) {
            $message = 'Fichier utilisé à double : ' . $file_url;
            error_log($message . chr(10) . __LINE__ . ", " . __FILE__ . chr(10) . chr(10), 3, LOG_PATH . '/error_log.txt');

            return Gallery::$urls[$file_url];
        }

        //directory to import to
        $artDir = ABS_PATH . '/wp-content/uploads/2013/05/';

        //if the directory doesn't exist, create it
        if (!file_exists(ABSPATH . $artDir)) {
            mkdir(ABSPATH . $artDir);
        }

        if (strpos($file_url, 'uploads') === 0 || strpos($file_url, 'fileadmin') === 0) {
            $file_url = 'http://www.jeux-strategie.com/'.$file_url;
        }

        if (@fclose(@fopen($file_url, "r"))) { //make sure the file actually exists
            copy($file_url, ABSPATH . $artDir . $new_filename);

            $siteurl = get_option('siteurl');
            $file_info = getimagesize(ABSPATH . $artDir . $new_filename);

            //create an array of attachment data to insert into wp_posts table
            $artdata = array(
                'post_author'       => 1, 'post_date' => current_time('mysql'), 'post_date_gmt' => current_time('mysql'), 'post_title' => $new_filename,
                'post_status'       => 'inherit', 'comment_status' => 'closed', 'ping_status' => 'closed',
                'post_name'         => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)), 'post_modified' => current_time('mysql'),
                'post_modified_gmt' => current_time('mysql'), 'post_parent' => $post_id, 'post_type' => 'attachment',
                'guid'              => $siteurl . '/' . $artDir . $new_filename, 'post_mime_type' => $file_info['mime'], 'post_excerpt' => '',
                'post_content'      => ''
            );

            $uploads = wp_upload_dir();
            $save_path = $uploads['basedir'] . '/2013/05/' . $new_filename;

            //insert the database record
            $attach_id = wp_insert_attachment($artdata, $save_path, $post_id);

            // mise en mémoire pour éviter de réimporter le fichier ultérieurement
            self::$urls[$file_url] = $attach_id;

            //generate metadata and thumbnails
            if ($attach_data = wp_generate_attachment_metadata($attach_id, $save_path)) {
                wp_update_attachment_metadata($attach_id, $attach_data);
            }

            //optional make it the featured image of the post it's attached to
            //$wpdb->insert($wpdb->prefix . 'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));

            return $attach_id;

        } else {
            return false;
        }

        return true;
    }

}
<?php


class Gallery
{

    private $gallery;
    private $typoid;
    private $wpid;

	public function __construct($gallery, $typoid)
	{
        f('Galerie n° '.$gallery['id'], 'weight2');
        $this->gallery=$gallery;
        $this->save();
	}


    public function getGallery()
    {
        return $this->gallery;
    }


	private function save()
	{
        $gallery = $this->gallery;
        if( is_array($gallery['image']) && sizeof($gallery['image']) > 0 )
        {
            $this->wpid = wp_insert_post(array(
                    'post_author' => 1,
                    'post_title' => $gallery['nom'],
                    'post_status' => 'publish',
                    'post_type' => 'gallery'
                )
            );
            if ($this->wpid==0) exit('La galerie '.$this->typoid.' n\'a pas été inserée');


            /****************************************************************
                 Linked games
            *****************************************************************/

            if( sizeof($this->gallery['liaison_jeu']) > 0)
            {
                $values = array();
                foreach($this->gallery['liaison_jeu'] as $jeu_lie)
                    if(is_numeric($jeu_lie))
                        $values[] = FichesJeu::$fiches[$jeu_lie]['wp_post_id'];
                update_field( ACF_JEUX_LIES , $values, $this->wpid );
            }


            /****************************************************************
                Save in wordpress
            *****************************************************************/
            foreach($this->gallery['image'] as $pict_id => $pict)
            {
                $cheminArray = explode('/', $pict);
                $name = array_pop($cheminArray);
                $chemin = implode('/', $cheminArray);

                //$this->log->f('download img :   http://www.jeux-strategie.com/'.$chemin.'/'.str_replace(' ', '%20', $name), 'weight1');
                self::fetch_media('http://www.jeux-strategie.com/'.$chemin.'/'.str_replace(' ', '%20', $name), Tools::cleanFilename($name, $time), $gallery_id );

                if($pict_id >= NB_IMG_PAR_GALERIE) break;
            }
        }

	}
}
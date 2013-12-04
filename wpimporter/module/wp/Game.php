<?php

class Game extends Post
{
    public static $post_type = 'games';

    public function addACFFields($data)
    {
        // définition des champs personnalisés acf
        // add_post_meta( $post_id, 'note', 	$fiche['note'] ); // équivalent en natif wordpress
        update_field(ACF_FJ_DATE_DE_SORTIE, utf8_decode($data['dateSortie']), $fiche->ID);
        $values = array(
            array(
                "adresse" => utf8_decode(Tools::getUrl($data['site'])),
                "acf_fc_layout" => "site_officiel"
            ), array(
                "nom" => utf8_decode(Tools::getLinkText($data['editeur'])),
                "adresse" => utf8_decode(Tools::getUrl($data['editeur'])),
                "acf_fc_layout" => "editeur"
            ), array(
                "nom" => utf8_decode(Tools::getLinkText($data['developpeur'])),
                "adresse" => utf8_decode(Tools::getUrl($data['developpeur'])),
                "acf_fc_layout" => "développeur"
            )
        );

        update_field(ACF_FJ_SITES, $values, $this->ID);
    }
}
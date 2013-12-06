<?php

class Page extends Post
{

    public static $post_type = 'page';


    public function addACFFields($content, $html_externe)
    {
        // aide
        if ($content['aide']) {
            update_field(ACF_INFO, $content['aide'], $this->ID);
        }

        $values = array();
        if (sizeof($content['contenu_page']) > 0) {

            foreach ($content['contenu_page'] as $contenu) {

                if ($contenu['type'] == 'page' && strlen($contenu['titre_contenu'] . $contenu['contenu']) > 0) {

                    $text = self::cleanHtml($contenu['titre_contenu'] . $contenu['contenu']);
                    $text = Page::replaceImages($text);
                    $values[] = array("texte" => $contenu['error'] . $text, "acf_fc_layout" => "contenu_sur_une_colonne");

                } elseif ($contenu['type'] == 'html_externe') {

                    $url = 'Url externe : http://www.jeux-strategie.com/_getExternalHtml.php?chemin=' . $html_externe[$contenu['id_document']] . '<br/>';
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true
                    ));

                    $text = curl_exec($curl);
                    curl_close($curl);

                    $text = self::cleanHtml($text);
                    $text = Page::replaceImages($text);
                    $values[] = array("texte" => $text, "acf_fc_layout" => "contenu_sur_une_colonne");
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

        update_field(ACF_CONTENU_REDACTION, $values, $this->ID);
    }



}

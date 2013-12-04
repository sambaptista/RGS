<?php

class Page extends Post
{

    public static $post_type = 'page';

    //Référence tous les liens différents du domaine, pour voir combien de liens pointent vers des pages du réseau. si le tableau est plus grand, c'est que certaines urls pointent nul part
    public static $link_domain = array();

    public function addACFFields($content)
    {

        // aide
        $values = array();
        if ($content['aide']) {
            $values[] = array(
                "texte" => $content['aide'], "acf_fc_layout" => "contenu_sur_une_colonne"
            );
        }

        if (sizeof($content['contenu_page']) > 0) {
            foreach ($content['contenu_page'] as $contenu) {
                //echo 'type : '.$contenu['type'].'<br/>';
                if (1 == 2 && $contenu['type'] == 'page' && strlen($contenu['titre_contenu'] . $contenu['contenu']) > 0) {
                    $text = self::cleanHtml($contenu['titre_contenu'] . $contenu['contenu']);
                    $text = Page::replaceImages($text);
                    $values[] = array("texte" => $contenu['error'] . $text, "acf_fc_layout" => "contenu_sur_une_colonne");
                } elseif (1 == 2 && $contenu['type'] == 'html_externe') {
                    $url = 'Url externe : http://www.jeux-strategie.com/_getExternalHtml.php?chemin=' . self::$html_externe[$contenu['id_document']] . '<br/>';

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

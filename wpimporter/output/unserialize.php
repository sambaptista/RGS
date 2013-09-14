<?php 


$link = 'http://www.jeux-strategie.com/dossiers/age-of-empires-online/les-celtes';
echo strpos( $link, 'http://www.jeux-strategie.com/' );
if( strpos( $link, 'http://www.jeux-strategie.com/' ) )
echo 'asdf';
else echo 'qwer';


exit();

  $sourcestring='<p>Dépêche publiée sur le(s) site(s) suivant(s):<br /><a href="http://www.jeux-strategie.com/riseofnations">RiseOfNations-Alliance.com</a><br /><a href="http://www.jeux-strategie.com/warcraft">Warcraft3France.com</a><br /><a href="http://www.jeux-strategie.com/sudden">39-45strategie.com</a><br /><a href="http://www.jeux-strategie.com/">Jeux-Strategie.com</a><br /><a href="http://www.jeux-strategie.com/aom">Age-of-Mythology.com</a><br /><a href="http://www.jeux-strategie.com/earth">EmpireEarth-France.com</a><br /><a href="http://www.jeux-strategie.com/age">AoK-Conquerors.com</a><br />Postée par: Equipe AF</p>
  
  <p><br />Après plusieurs années de bons et loyaux services, nos forums de discussions avaient besoin dune cure de jouvence. <br />
  Cest aujourdhui chose faite avec la mise en place dun nouveau forum, qui, tout en restant dans la tradition de lAlliance - sobriété, convivialité et fonctionnalité - propose de nombreuses nouvelles possibilités comme la <i>personnalisation de votre profil</i> par lajout dun avatar affiché dans les messages ou la <i>configuration de vos forums favoris</i>.<br />
  Qui dit nouveau forum dit nécessité de se <a href="http://forum.alliance-francophone.net/index.php?act=Reg&amp;CODE=00">réinscrire</a> pour pouvoir poster, mais aussi règles inchangées donc profitez en pour lire ou relire notre <a href="http://forum.alliance-francophone.net/index.php?act=boardrules">charte</a>.<br />
  Pour vous aider dans cette phase de transition, vous avez à votre disposition un forum de <a href="http://forum.alliance-francophone.net/index.php?showforum=59">questions/réponses </a>détaillant les principales fonctionnalités de ce nouveau forum et un forum de <a href="http://forum.alliance-francophone.net/index.php?showforum=1">test</a> pour faire vous premiers pas.<br /><i>Lequipe de l Alliance vous souhaite la bienvenue sur son nouveau forum.</i><br /><a href="http://forum.alliance-francophone.net/index.php?act=Reg&amp;CODE=00">Sinscrire sur le nouveau forum</a> <br /></p>';

  $patterns = array ('/<p>.*<\/p>(.*)/ms');
  $replace = array ('\1');

  echo "<pre>";
  echo "From:\n";
  echo htmlspecialchars($sourcestring);
  echo "\n\nTo:\n";
  echo htmlspecialchars(preg_replace($patterns,$replace,utf8_encode($sourcestring)));
  echo "</pre>";
EXIT();




$msg = 'a:10:{s:3:"key";s:19:"field_51bb867b1aed0";s:5:"label";s:21:"Contenu de rédaction";s:4:"name";s:20:"contenu_de_redaction";s:4:"type";s:16:"flexible_content";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:7:"layouts";a:4:{i:0;a:4:{s:5:"label";s:23:"Contenu sur une colonne";s:4:"name";s:23:"contenu_sur_une_colonne";s:7:"display";s:3:"row";s:10:"sub_fields";a:1:{i:0;a:10:{s:3:"key";s:19:"field_51bb868a1aed1";s:5:"label";s:5:"Texte";s:4:"name";s:5:"texte";s:4:"type";s:7:"wysiwyg";s:12:"instructions";s:0:"";s:12:"column_width";s:0:"";s:13:"default_value";s:0:"";s:7:"toolbar";s:5:"basic";s:12:"media_upload";s:3:"yes";s:8:"order_no";i:0;}}}i:1;a:4:{s:5:"label";s:25:"Contenu sur deux colonnes";s:4:"name";s:25:"contenu_sur_deux_colonnes";s:7:"display";s:5:"table";s:10:"sub_fields";a:2:{i:0;a:10:{s:3:"key";s:19:"field_51bb86d81aed4";s:5:"label";s:17:"Colonne de gauche";s:4:"name";s:17:"colonne_de_gauche";s:4:"type";s:7:"wysiwyg";s:12:"instructions";s:0:"";s:12:"column_width";s:0:"";s:13:"default_value";s:0:"";s:7:"toolbar";s:4:"full";s:12:"media_upload";s:3:"yes";s:8:"order_no";i:0;}i:1;a:10:{s:3:"key";s:19:"field_51bb86ef1aed6";s:5:"label";s:17:"Colonne de droite";s:4:"name";s:17:"colonne_de_droite";s:4:"type";s:7:"wysiwyg";s:12:"instructions";s:0:"";s:12:"column_width";s:0:"";s:13:"default_value";s:0:"";s:7:"toolbar";s:4:"full";s:12:"media_upload";s:3:"yes";s:8:"order_no";i:1;}}}i:2;a:4:{s:5:"label";s:6:"Média";s:4:"name";s:5:"media";s:7:"display";s:3:"row";s:10:"sub_fields";a:1:{i:0;a:12:{s:3:"key";s:19:"field_51bb87041aed8";s:5:"label";s:6:"Média";s:4:"name";s:5:"media";s:4:"type";s:12:"relationship";s:12:"instructions";s:37:"Sélectionnez un ou plusieurs médias";s:12:"column_width";s:0:"";s:9:"post_type";a:2:{i:0;s:10:"attachment";i:1;s:7:"gallery";}s:8:"taxonomy";a:1:{i:0;s:3:"all";}s:7:"filters";a:1:{i:0;s:6:"search";}s:15:"result_elements";a:1:{i:0;s:9:"post_type";}s:3:"max";s:0:"";s:8:"order_no";i:0;}}}i:3;a:4:{s:5:"label";s:10:"Voir aussi";s:4:"name";s:10:"voir_aussi";s:7:"display";s:3:"row";s:10:"sub_fields";a:1:{i:0;a:12:{s:3:"key";s:19:"field_51bb886f1aeda";s:5:"label";s:21:"Documents en relation";s:4:"name";s:21:"documents_en_relation";s:4:"type";s:12:"relationship";s:12:"instructions";s:0:"";s:12:"column_width";s:0:"";s:9:"post_type";a:4:{i:0;s:4:"post";i:1;s:4:"page";i:2;s:5:"games";i:3;s:4:"test";}s:8:"taxonomy";a:1:{i:0;s:3:"all";}s:7:"filters";a:2:{i:0;s:6:"search";i:1;s:9:"post_type";}s:15:"result_elements";a:1:{i:0;s:9:"post_type";}s:3:"max";s:0:"";s:8:"order_no";i:0;}}}}s:12:"button_label";s:18:"Ajouter un contenu";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:3:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";s:5:"value";s:0:"";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:0;}';



echo '<pre>';
print_r( unserialize($msg) );
echo '</pre>';








 ?>
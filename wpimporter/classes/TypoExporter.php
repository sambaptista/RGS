<?php

	
	
	

							$contenu_page['contenu']['url'] = $element['url'];
							//$this->fiches_de_jeu[$contenu_page['id_document']] = $contenu_page['contenu']; // commenté par sam : version à alex avec l'id du document qui se trouve dans une page

							$contenu_page['contenu']['url'] = $element['url']; // ajouté par sam
							$contenu_page['contenu']['liaison_jeu'] = $element['liaison_jeu']; // ajouté par sam
							$contenu_page['contenu']['name'] = $element['name']; // ajouté par sam
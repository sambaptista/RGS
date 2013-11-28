<?php


	$imageList = array();

	if ($handle = opendir($chemin)) 
	{
		while (false !== ($entry = readdir($handle))) 
		{
			if($entry != "." && $entry != "..")
			{
				if(!is_dir($chemin.$entry))
				{
					$imageList[$chemin][] = $entry;
				}
				if(is_dir($chemin.$entry))
				{
					listingImage($chemin.$entry.'/');
				}
			}
		}
		closedir($handle);
	}	

	echo '<pre>';
	print_r( $imageList );
	echo '</pre>';
	echo json_encode($imageList);



?>
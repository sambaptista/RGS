<?php

	$filename = $_REQUEST['chemin'];
	if( strpos($filename, '.html') )
	{
		$text = file_get_contents($filename);

		exit($text);
	}

	exit('fail');

?>
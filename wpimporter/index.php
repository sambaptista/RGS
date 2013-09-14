<?php

// modèles 
require_once('config/settings.php');
require_once('config/config.php');
require_once('htmlpurifier/library/HTMLPurifier.auto.php');


// classes php
require_once(ABS_PATH.'/wp-load.php');
require_once(ABS_PATH.'/wp-includes/post.php');
require_once(ABS_PATH.'/wp-includes/meta.php');
require_once(ABS_PATH.'/wp-admin/includes/image.php');




echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Exporteur Typo -> WP</title>
	<link href="http://fonts.googleapis.com/css?family=Source+Code+Pro:200,400,500,700,900" rel="stylesheet" type="text/css">
	<style type="text/css" media="screen">
		body{ 
			font-family: "Source Code Pro", sans-serif;
			font-size:12px;
			font-weight:400;
		}
		
		.weight4{
			font-size:15px;
			font-weight:900;
			margin-top:20px;
			color:#000;
		}
		.weight3{
			margin-top:10px;
			font-size:14px;
			font-weight:700;
			color:#333;

		}
		.weight2{
			margin-top:10px;
			font-size:12px;
			color:#666;
			font-weight:400;
		}
		.weight1{
			font-size:11px;
			color:#999;
			font-weight:200;
		}
		
		.success{color:green;margin-bottom:200px;}
		.error{color:red;}
	</style>
</head>
<body>';




function autoload($className){
	$include_paths = explode(':', ini_get('include_path') );
	$extensions = array('','.inc', '.class');

	if (!class_exists($className, false) ){
		foreach($include_paths as $include_path){
			foreach($extensions as $extension){
				if(file_exists( $include_path."/".$className.$extension.'.php')){

					require_once($className.$extension.'.php');
				}
			}
		}
	}
}
spl_autoload_register('autoload');



//function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
//{
    // error was suppressed with the @-operator
//    if (0 === error_reporting()) {
//        return false;
//    }
//
//    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
//}
//set_error_handler('handleError');




$log = Log::getInstance();
$log->setActivated(true);
$log->setFlushActivated(true);
$log->setPrintActivated(true);


try{
	if(isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];
		WPImporter::$action();	
	}else{
		$wpimporter = new WPImporter();	
	}
}catch(Exception $e){
	echo $e->getMessage();
}




echo '</body></html>';
?>
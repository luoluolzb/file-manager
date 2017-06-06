<?php

include_once('../class/ArgsValidator.class.php');
include_once('../class/Response.class.php');
include_once('../class/SimpleFileManager.class.php');

$args = ArgsValidator::validate(
	array('dir', 'rootDir'),
	array(),
	array(
		'errcode' => 1
	)
);
if(!is_array($args)){
	Response::responseData($args, array());
}
else{
	$args['dir'] = mb_convert_encoding($args['dir'], 'GBK', 'UTF-8');
	$args['rootDir'] = mb_convert_encoding($args['rootDir'], 'GBK', 'UTF-8');

	$sfm = new SimpleFileManager($args['dir'], $args['rootDir']);
	$fileList = $sfm->getFileList();
	Response::responseData(0, $fileList);
}
?>
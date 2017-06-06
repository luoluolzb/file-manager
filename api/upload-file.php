<?php

include_once('../class/ArgsValidator.class.php');
include_once('../class/UploadFileHandler.class.php');
include_once('../class/Response.class.php');

$args = ArgsValidator::validate(
	array('fileFiled', 'saveDir'),
	array(),
	array(
		'errcode' => 1
	)
);
if(!is_array($args)){
	Response::responseData($args, array());
}
else{
	$args['fileFiled'] = mb_convert_encoding($args['fileFiled'], 'GBK', 'UTF-8');
	$args['saveDir'] = mb_convert_encoding($args['saveDir'], 'GBK', 'UTF-8');

	$ufm = new UploadFileHandler();
	if($ufm->load($args['fileFiled']) && $ufm->save($args['saveDir'])){
		Response::responseData(0);
	}
	else{
		Response::responseData(2);
	}
}
?>
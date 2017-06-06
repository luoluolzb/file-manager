<?php

include_once('../class/ArgsValidator.class.php');
include_once('../class/Response.class.php');
include_once('../class/SimpleFileManager.class.php');

$args = ArgsValidator::validate(
	array('dir', 'fileList', 'toName'),
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
	foreach ($args['fileList'] as $i => $fileName) {
		$args['fileName'][$i] = mb_convert_encoding($fileName, 'GBK', 'UTF-8');
	}
	$args['toName'] = mb_convert_encoding($args['toName'], 'GBK', 'UTF-8');

	$sfm = new SimpleFileManager($args['dir']);
	if($sfm->zip($args['fileList'], $args['toName'])){
		Response::responseData(0);
	}
	else{
		Response::responseData(2);
	}
}
?>
<?php

include_once('../class/ArgsValidator.class.php');
include_once('../class/Response.class.php');
include_once('../class/SimpleFileManager.class.php');

$args = ArgsValidator::validate(
	array('dir', 'fileList'),
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

	if(is_array($args['fileList'])){
		foreach ($args['fileList'] as $i => $fileName) {
			$args['fileList'][$i] = mb_convert_encoding($fileName, 'GBK', 'UTF-8');
		}
	}else{
		$args['fileList'] = mb_convert_encoding($args['fileList'], 'GBK', 'UTF-8');
	}
	

	$sfm = new SimpleFileManager($args['dir']);
	if($sfm->delete($args['fileList'])){
		Response::responseData(0);
	}
	else{
		Response::responseData(2);
	}
}
?>
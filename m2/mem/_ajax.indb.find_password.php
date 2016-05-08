<?php
include dirname(__FILE__).'/../lib/library.php';
include $shopRootDir.'/conf/fieldset.php';

$mobilePasswordFinder = Core::loader('mobilePasswordFinder');

function iconvArray($data)
{
	if(is_array($data)){
		return array_map('iconvArray', $data);
	}
	else {
		return iconv("UTF-8", "EUC-KR", $data);
	}
}
if(is_array($_POST)){
	if (get_magic_quotes_gpc()) stripslashes_all($_POST);
	$_POST = array_map('iconvArray', $_POST);
}

try {
	$resultArray = array();
	$result = $mobilePasswordFinder->execute($_POST);
	$resultArray = @explode("|", $result);

	if($resultArray[0] !== '0000'){
		throw new Exception($resultArray[0]);
	}
	else {
		echo $result;
	}
}
catch(Exception $e){
	exit($e->getMessage());
}
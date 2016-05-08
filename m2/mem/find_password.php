<?php
include '../_header.php';
include $shopRootDir . '/conf/fieldset.php';

if($sess) msg("고객님은 로그인 중입니다.", '../index.php');

if(!is_object($config)){
	$config = Core::loader('config');
}
$info_cfg = $config->load('member_info');

$use_choice_mail = $use_choice_hp = '';
if($checked['useField']['email']){
	$use_choice_mail = 'y';
}
if((int)$info_cfg['finder_use_mobile'] == 1){
	if(is_file($shopRootDir . '/lib/Hpauth.class.php')) {
		$hpauth = Core::loader('Hpauth');
		$hpauthRequestData = $hpauth->getAuthRequestData();

		if($hpauthRequestData['useyn'] == 'y'){
			$use_choice_hp = 'y';
		}
	}
}

$tpl->assign('use_choice_mail', $use_choice_mail);
$tpl->assign('use_choice_hp', $use_choice_hp);
$tpl->print_('tpl');
?>
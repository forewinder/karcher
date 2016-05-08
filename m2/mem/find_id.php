<?php
include '../_header.php';
include $shopRootDir . '/conf/fieldset.php';

if($sess) msg("고객님은 로그인 중입니다.", '../index.php');

if(is_file($shopRootDir . '/lib/Hpauth.class.php')) {
	$hpauth = Core::loader('Hpauth');
	$hpauthRequestData = $hpauth->getAuthRequestData();
}
if(is_file($shopRootDir . '/lib/dormant.class.php')) {
	$dormant = Core::loader('dormant');
}

$ipinType = '';
if($ipin['useyn'] === 'y') $ipinType = 'ipin';
if($ipin['nice_useyn'] === 'y') $ipinType = 'niceipin';

if($_POST['act'] === 'Y'){
	if($_POST['rncheck'] == 'ipin' || $_POST['rncheck'] == 'hpauthDream'){
		list($m_id, $name) = $db->fetch("SELECT m_id, name FROM " . GD_MEMBER . " WHERE dupeinfo = '".$_POST['dupeinfo']."'");

		//휴면회원 조회
		if(is_object($dormant) && !$m_id){
			list($m_id, $name) = $dormant->findIdUser('dupeinfo', $_POST);
		}
	}
	else {
		$where[] = "name='" . $_POST['srch_name'] . "'";
		if ($checked['useField']['email']) {
			$where[] = "email='" . $_POST['srch_mail'] . "'";
		}

		list($m_id, $name) = $db->fetch("SELECT m_id, name FROM " . GD_MEMBER . " WHERE " . implode(" and ", $where));

		//휴면회원 조회
		if(is_object($dormant) && !$m_id){
			list($m_id, $name) = $dormant->findIdUser('name', $_POST);
		}
	}
}


if(is_object($hpauth)){
	$tpl->assign('hpauthyn', $hpauthRequestData['useyn']);
	$tpl->assign('hpauthCPID', $hpauthRequestData['cpid']);
}
$tpl->assign('ipinType', $ipinType);
$tpl->assign('act', $_POST['act']);
$tpl->print_('tpl');
?>
<?php
include '../_header.php';

if($sess) msg("고객님은 로그인 중입니다.", '../index.php');

$message = '';
switch($_POST['otpType']){
	case 'mail':
		$message = '회원정보상에 등록되어 있는 고객님의 이메일로 인증번호가 전송되었습니다.';
	break;

	case 'mobile':
		$message = '회원정보상에 등록되어 있는 고객님의 휴대폰으로 인증번호가 전송되었습니다.';
	break;
}

$tpl->assign($_POST);
$tpl->assign('message', $message);
$tpl->print_('tpl');
?>
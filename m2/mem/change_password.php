<?php
include '../_header.php';

if($sess) msg("고객님은 로그인 중입니다.", '../index.php');

$tpl->print_('tpl');
?>
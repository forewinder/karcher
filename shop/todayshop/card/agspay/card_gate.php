<?

### 올더게이트 (AGSPay V4.0 for PHP)

//include '../conf/pg.agspay.php';
@include '../conf/pg.escrow.php';

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

$pg['zerofee'] = ( $pg['zerofee'] == 'yes' ? '9000400002' : '9000400001' );			// 무이자 여부 (Y:9000400002 / N:9000400001)
if ($pg['zerofee'] != '9000400002' || empty($pg['zerofee_period']) === true) {
	$pg['zerofee_period'] = 'NONE';
}

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
$ordnm = strcut(strip_tags($ordnm),90); // 상품명 태그 제거, 길이 제한(계좌이체:100byte 이내)
if($i > 1)$ordnm .= ' 외'.($i-1).'건';
?>
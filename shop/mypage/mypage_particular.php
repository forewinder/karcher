<?
include '../_header.php';

// 직인
include_once dirname(__FILE__) . '/../conf/config.pay.php';
$sealpath = '/shop/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set['tax']['seal'];

$ordno = $_GET['ordno'];

$order = new order();
$order->load($ordno);

// 권한 체크
if ($sess['m_no']){
	if ($order['m_no'] != $sess['m_no']) msg("접근권한이 없습니다",-1);
} else {
	if ($order['nameOrder'] != $_COOKIE['guest_nameOrder'] || $order['m_no']) msg("접근권한이 없습니다",-1);
}

// 접근 제한 체크
if ($order['step'] < $cfg['particularPermit']) msg('잘못된 접근입니다.',-1);

//사업장소재지
if (!$cfg['road_address']) {
	$address = $cfg['address'];
} else {
	$address = $cfg['road_address'];
}

//합계
$totalAmount = $order->getRealPrnSettleAmount();

//전체취소된 주문건
if((int)$order['step2'] == 44){
	$totalAmount = 0;
}

$total = array(
	'etc' => $totalAmount,
	'supply' => 0,
	'tax' => 0,
);

$rowCount = 0;
$refundedFeeAmount = 0; //환불수수료
$item = array();
foreach ($order->getOrderItems() as $v) {
	$tmp = array();
	if ($v->hasCancelCompleted()) {
		$refundedFeeAmount += array_sum($v->getRefundedFeeAmount());
		continue;
	}

	$rowCount++;

	// 금액 계산
	$supply = $v->getSupplyPrice();
	$tax = $v->getTax();

	// 금액 총합
	$total['etc'] -= $v->getAmount();
	$total['supply'] += $supply;
	$total['tax'] += $tax;

	// 상품명
	$tmp['goodsnm'] = $v['goodsnm'];
	if ($v['opt1']) {
		$tmp['goodsnm'] .= " [".$v['opt1'];
		if ($v['opt2']) $tmp['goodsnm'] .= "/".$v['opt2'];
		$tmp['goodsnm'] .= ']';
	}
	if ($v['addopt']) $tmp['addopt'] = '['.str_replace('^','] [',$v['addopt']).']';

	$tmp['ea'] = $v['ea'];
	$tmp['price'] = $v['price'];
	$tmp['supply'] = $supply;
	$tmp['tax'] = $tax;

	$item[] = $tmp;
}

if ($total['etc']) {
	//배송비 + 보증보험수수료 + 환불수수료
	$total['etcAddPrice'] = $order->getTaxAddAmount() + $refundedFeeAmount - $order->getCancelCompletedDeliveryFee();
	if($total['etcAddPrice'] > 0){
		$total['etcAddPriceSupply'] = round($total['etcAddPrice']/1.1);
		$total['etcAddPriceSurtax'] = $total['etcAddPrice'] - $total['etcAddPriceSupply'];
	}

	//할인
	$total['discount'] = $order->getDiscount() - $order->getCancelCompletedMemberDiscount() - $order->getCancelCompletedGoodsDiscount() - $order->getCancelCompletedCouponDiscount() - $order->getCancelCompletedEmoney();
	if($total['discount'] > 0){
		$total['discountSupply'] = round($total['discount']/1.1);
		$total['discountSurtax'] = $total['discount'] - $total['discountSupply'];
	}

	$total['supply'] = $total['supply']+$total['etcAddPriceSupply']-$total['discountSupply'];
	$total['tax'] = $total['tax']+$total['etcAddPriceSurtax']-$total['discountSurtax'];

	$rowCount++;
}

$orddt = $order['orddt'];
$nameOrder = $order['nameOrder'];

$tpl->assign('cfg',$cfg);
$tpl->assign('sealpath',$sealpath);
$tpl->assign('address',$address);
$tpl->assign('orddt',$orddt);
$tpl->assign('nameOrder',$nameOrder);
$tpl->assign('item',$item);
$tpl->assign('total',$total);
$tpl->assign('totalAmount',$totalAmount);
$tpl->assign('rowCount',$rowCount);

$tpl->print_('tpl');

?>
<?php
/*******************************************************************************************
 * 네이버페이에서 주문ID를 발급받고 주문정보를 페이으로 넘긴 후 페이 페이지를 새창으로 띠움
 *******************************************************************************************/

include "../lib/library.php";
include "../lib/load.class.php";
@include "../conf/naverCheckout.cfg.php";
require "../lib/naverCheckout.class.php";
require "../conf/config.php";
include "../lib/cart.class.php";

// ASCII(제어문자) 문자가 있는 경우 네이버 페이 API에서 주문조회가 되지 않아 상품정보 전송시 제거
$not_string = Array('%01','%02','%03','%04','%05','%06','%07','%08','%09','%0A','%0B','%0C','%0D','%0E','%0F','%10','%11','%12','%13','%14','%15','%16','%17','%18','%19','%1A','%1B','%1C','%1D','%1E','%1F');

//상품v2 적용여부 체크
$v2 = false;
if(is_dir($_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/lib/Clib') === true) $v2 = true;

//기본값 설정
$tax_type['1'] = 'TAX';
$tax_type['0'] = 'TAX_FREE';

$shippingType['PREPAYED'] = 'PAYED';//선불
$shippingType['FREE'] = 'FREE';//무료
$shippingType['CASH_ON_DELIVERY'] = 'ONDELIVERY';//후불

if ($_GET[mode]!='cart') {
	// 장바구니 모드가 아니면, 구매 상품을 장바구니에 담아 처리 한다.
	$_COOKIE['gd_isDirect'] = 1;
	$cart = new Cart($_COOKIE['gd_isDirect']);	// isdirect = 1

	// 멀티옵션
	if ($_POST[multi_ea]) {
		$_keys = array_keys($_POST[multi_ea]);
		for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
			$_opt = $_POST[multi_opt][ $_keys[$i] ];
			$_ea = $_POST[multi_ea][ $_keys[$i] ];
			$_addopt = $_POST[multi_addopt][ $_keys[$i] ];

			if($v2 === true) {
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];
				$cart->addCart($_POST[goodsno],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST[goodsCoupon]);
			} else {
				$cart->addCart($_POST[goodsno],$_opt,array_notnull($_addopt),$_ea,$_POST[goodsCoupon]);
			}
		}
	}
	else {
		if($v2 === true) {
			$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],$_POST[_addopt_inputable],$_POST[ea],$_POST[goodsCoupon]);
		} else {
			$cart->addCart($_POST[goodsno],$_POST[opt],array_notnull($_POST[addopt]),$_POST[ea],$_POST[goodsCoupon]);
		}
	}
} else {
	$cart = new Cart;
}

$_GET[idxs] = isset($_GET[idxs]) ? $_GET[idxs] : 'all';

$cart->setOrder($_GET[idxs]);
$item = $cart->item;

$param = array(
'mode' => '0',
'deliPoli' => 0,
'marketingType' => 'naverCheckout'
);
$tmp = getDeliveryMode($param);

$naver_delivery['group_id'] = str_replace('.', '', microtime(true));

if($tmp['type'] == '선불') {
	$naver_delivery['feeType'] = 'CHARGE';
	$naver_delivery['feePayType'] = 'PREPAYED';
	$naver_delivery['delivery_price'] = $tmp['price'];
} else if($tmp['type'] == '후불') {
	$naver_delivery['feeType'] = 'CHARGE';
	$naver_delivery['feePayType'] = 'CASH_ON_DELIVERY';
	$naver_delivery['delivery_price'] = $checkoutCfg['collect'];
} else if($tmp['type'] == '무료') {//조건부 무료
	$naver_delivery['feeType'] = 'FREE';
	$naver_delivery['feePayType'] = 'FREE';
	$naver_delivery['delivery_price'] = 0;
}

$NaverCheckout = &load_class('NaverCheckout','NaverCheckout');
if(!$NaverCheckout->check_use()) msg('네이버페이 사용을 중단 하였습니다.', -1);

if($v2 === true) chkOpenYn($item,"D", -1);

//페이 주문정보 저장
$checkout_query = 'insert into '.GD_NAVERCHECKOUT.' set `shipping_type`="", `shipping_price`="", `total_price`="", `orderId`="", regdt=now()';
$db->query($checkout_query);
$checkSno = $db->lastID();
if($checkSno === false || $checkSno < 1) {
	msg('주문내역 저장이 실패되었습니다.', -1);
}

$arr_data[] = '<?xml version="1.0" encoding="utf-8"?>';
$arr_data[] = '<order>';
$arr_data[] = '<merchantId>' . $checkoutCfg['naverId'] . '</merchantId>';//네이버 공통 인증키
$arr_data[] = '<certiKey>' . $checkoutCfg['connectId'] . '</certiKey>';//연동 인증키

foreach($item as $goods) {

	$arr_option_info = '';
	$tmp_array_option_info = Array();
	$tmp_array_option_info[] = $goods['optno'];//옵션번호
	$tmp_array_option_info[] = $goods['addno'];//추가옵션번호 - 1,2,3,4 or ,2,3,4
	$tmp_array_option_info[] = $goods['ea'];//수량
	$option_info = implode('^', $tmp_array_option_info);


	if($v2 === true) {
		$query = "select a.*, b.sno as optsno, b.price, b.reserve, b.stock from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' where a.goodsno='$goods[goodsno]' and opt1='{$goods[opt][0]}' and opt2='{$goods[opt][1]}' limit 1";

		$basePrice = $db->fetch($db->_query_print('SELECT price FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] AND go_is_deleted=[i] ORDER BY link DESC', $goods[goodsno], 1), true);
	} else {
		$query = "select a.*, b.sno as optsno, b.price, b.reserve, b.stock from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno where a.goodsno='$goods[goodsno]' and opt1='{$goods[opt][0]}' and opt2='{$goods[opt][1]}' limit 1";

		$basePrice = $db->fetch($db->_query_print('SELECT price FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] ORDER BY link DESC', $goods[goodsno], 1), true);
	}

	$data = $db->fetch($query);

	if(!$NaverCheckout->check_banWords($data['goodsnm']))$errmsg=$data['goodsnm']."은 네이버페이 서비스에 사용가능한 상품이 아닙니다.";
	if(!$NaverCheckout->check_exceptions($data['goodsno']))$errmsg=$data['goodsnm']."은 네이버페이 서비스에 사용가능한 상품이 아닙니다.";
	if($data['optnm'])$optnm = explode('|',$data['optnm']);

	if(!$data['goodsno'])$errmsg="올바른 접근이 아닙니다.";
	if ($data['usestock'] && $data['stock']<$goods['ea'])$errmsg = $data['goodsnm']."의 재고가 모자랍니다.";

	if (empty($goods[opt][0])) unset($goods[opt][0]);
	if (empty($goods[opt][1])) unset($goods[opt][1]);

	// 최소,최대구매수량체크
	$min_ea = $data['min_ea'];
	$max_ea = $data['max_ea'];

	if($goods['ea'] < $min_ea) {
		$errmsg=$data['goodsnm']." 상품의 최소구매수량은 {$min_ea}개 입니다.";
	}
	else if($max_ea!='0' && $goods['ea'] > $max_ea) {
		$errmsg=$data['goodsnm']." 상품의 최대구매수량은 {$max_ea}개 입니다.";
	}

	if($errmsg) msg($errmsg, -1);

	//상품 이미지 설정
	$arr_img = explode('|', $goods['img_l']);

	$totalPrice += $data['price'] * $goods['ea'];


	//상품 기본정보 설정
	$arr_data[] = '<product>';
	$arr_data[] = '<id>' .$goods['goodsno']. '</id>';//상품코드
	$arr_data[] = '<merchantProductId>' .$data['goodscd']. '</merchantProductId>';//판매자상품번호
	$arr_data[] = '<ecMallProductId>' .$data['goodsno']. '</ecMallProductId>';//지식쇼핑 ep의 mall_pid
	$arr_data[] = '<name><![CDATA[' .urldecode(str_replace($not_string, '', urlencode($data['goodsnm']))). ']]></name>';//상품명
	$arr_data[] = '<basePrice>' .$basePrice['price']. '</basePrice>';//본상품 1개의 판매가격
	$arr_data[] = '<taxType>' .$tax_type[$data['tax']]. '</taxType>';//과세종류 TAX=과세, TAX_FREE=면세, ZERO_TAX=영세
	$arr_data[] = '<infoUrl><![CDATA[' ."http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$data['goodsno']. ']]></infoUrl>';//상품상세 페이지 URL
	$arr_data[] = '<imageUrl><![CDATA[' .'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$arr_img[0]. ']]></imageUrl>';//상품원본이미지 URL
//	$arr_data[] = '<giftName><![CDATA[]]></giftName>';//사은품명
	if($checkoutCfg['return_price'] != '') {
		$arr_data[] = '<returnShippingFee><![CDATA[' .$checkoutCfg['return_price']. ']]></returnShippingFee>';//편도 반품 배송비
		$arr_data[] = '<exchangeShippingFee><![CDATA[' .($checkoutCfg['return_price'] * 2). ']]></exchangeShippingFee>';//편도 반품 배송비
	}

	$totalMoney += $data['price'];

	//옵션 데이터 설정
	$arr_option_name = $option_text = Array();

	//item 테이블에 저장되는 itemSno를 manageCode로 사용하기 위해 우선 저장한다.
	if(isset($goods['opt'])) {
		$arr_option_name = explode('|', $data['optnm']);
		for($os = 0; $os < count($goods['opt']); $os++) {
			if($goods['opt'][$os] == '') msg('옵션을 선택해주세요', -1);
			$goods['opt'][$os] = urldecode(str_replace($not_string, '', urlencode($goods['opt'][$os])));

			$option_text[] = $arr_option_name[$os].'/'.$goods['opt'][$os];
		}
	}

	$item_query = 'insert into '.GD_NAVERCHECKOUT_ITEM.' set `goodsno`="'.$data['goodsno'].'", `goodsnm`="'.$data['goodsnm'].'", `price`="'.$data['price'].'", `ea`="'.$goods['ea'].'", `option`="'.implode(', ', $option_text).'", `option_info`="'.$option_info.'"';
	$db->query($item_query.", `checkoutSno`='$checkSno'");
	$naver_item_sno = $db->lastID();



	if(isset($goods['opt']) || empty($goods['select_addopt']) === false) {

		$arr_data[] = '<option>';

		$arr_data[] = '<manageCode>S' .$goods['optno']. '</manageCode>';//옵션 sno
		$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';//옵션 주문수량 1이상

		if(isset($goods['opt'])) {
			$arr_data[] = '<selectedItem>';
			$arr_data[] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
			$arr_data[] = '<name><![CDATA[' .$data['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
			$arr_data[] = '<value>';
			$arr_data[] = '<id>S' .$data['optsno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
			$arr_data[] = '<text><![CDATA[' .implode(', ', $option_text). ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
			$arr_data[] = '</value>';
			$arr_data[] = '</selectedItem>';
		}

		//입력옵션
		$text_option_price = 0;
		if(empty($goods['input_addopt']) === false) {
			foreach($goods['input_addopt'] as $input_option) {
				$arr_data[] = '<selectedItem>';
				$arr_data[] = '<type>INPUT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
				$arr_data[] = '<name><![CDATA[' .$input_option['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
				$arr_data[] = '<value>';
				$arr_data[] = '<id>T' .$input_option['sno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
				$arr_data[] = '<text><![CDATA[' .$input_option['opt']. ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
				$arr_data[] = '</value>';
				$arr_data[] = '</selectedItem>';
				$text_option_price += $input_option['price'];
			}
		}

		//추가상품 정보 설정 - 추가상품의 경우 마이너스 금액을 설정할 수 없어 일반 옵션으로 설정한다.
		$add_option_price = 0;
		if($v2 === true) {
			if(empty($goods['select_addopt']) === false) {
				foreach($goods['select_addopt'] as $add_option) {
					$arr_data[] = '<selectedItem>';
					$arr_data[] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
					$arr_data[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
					$arr_data[] = '<value>';
					$arr_data[] = '<id>A' .$add_option['sno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
					$arr_data[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
					$arr_data[] = '</value>';
					$arr_data[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		} else {
			if(empty($goods['addopt']) === false) {
				foreach($goods['addopt'] as $add_option) {
					$arr_data[] = '<selectedItem>';
					$arr_data[] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
					$arr_data[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
					$arr_data[] = '<value>';
					$arr_data[] = '<id>A' .$add_option['sno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
					$arr_data[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
					$arr_data[] = '</value>';
					$arr_data[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		}

		$arr_data[] = '<price>'.($data['price'] - $basePrice['price'] + $text_option_price + $add_option_price).'</price>';//옵션금액 - 기본판매가 + 입력형옵션 추가금액

		$arr_data[] = '</option>';
	} else if(empty($goods['input_addopt']) === false) {
		$arr_data[] = '<option>';

		$text_option_manageCode = Array();
		$text_option_price = 0;
		foreach($goods['input_addopt'] as $input_option) {
			$arr_data[] = '<selectedItem>';
			$arr_data[] = '<type>INPUT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
			$arr_data[] = '<name><![CDATA[' .$input_option['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
			$arr_data[] = '<value>';
			$arr_data[] = '<id>T' .$input_option['sno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
			$arr_data[] = '<text><![CDATA[' .$input_option['opt']. ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
			$arr_data[] = '</value>';
			$arr_data[] = '</selectedItem>';
			$text_option_manageCode[] = 'T'.$input_option['sno'];
			$text_option_price += $input_option['price'];
		}
		$arr_data[] = '<manageCode>' .$naver_item_sno. '</manageCode>';//옵션 sno
		$arr_data[] = '<price>'.$text_option_price.'</price>';
		$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';
		$arr_data[] = '</option>';

	} else {
		$none_option = true;

		//추가옵션만 있는경우 옵션데이터 생성
		$add_option_price = $add_manage_code = 0;
		$only_add = array();

		if($v2 === true) {
			if(empty($goods['select_addopt']) === false) {
				$none_option = false;
				foreach($goods['select_addopt'] as $add_option) {
					if($add_manage_code == 0) $add_manage_code = $add_option['sno'];
					$only_add[] = '<selectedItem>';
					$only_add[] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
					$only_add[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
					$only_add[] = '<value>';
					$only_add[] = '<id>A' .$add_option['sno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
					$only_add[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
					$only_add[] = '</value>';
					$only_add[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		} else {
			if(empty($goods['addopt']) === false) {
				$none_option = false;
				foreach($goods['addopt'] as $add_option) {
					if($add_manage_code == 0) $add_manage_code = $add_option['sno'];
					$only_add[] = '<selectedItem>';
					$only_add[] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
					$only_add[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
					$only_add[] = '<value>';
					$only_add[] = '<id>A' .$add_option['sno']. '</id>';//옵션값 ex)R, XL - 영문,숫자, 특수문자( !+-/=_| ) 공백사용불가 - 최대 50자
					$only_add[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//선택옵션 텍스트값 ex)빨강, XL - 최대 50자
					$only_add[] = '</value>';
					$only_add[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		}

		// 옵션 정보가 없는 본상품 주문일 경우는      <single> 요소가 반드시 들어가야 한다.
		if ($none_option === true) {
			$arr_data[] = '<single>';
			$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';
			$arr_data[] = '</single>';
		} else {//추가상품정보만 있는경우
			$arr_data[] = '<option>';

			$arr_data[] = '<manageCode>A' .$add_manage_code. '</manageCode>';//옵션 sno
			$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';//옵션 주문수량 1이상
			$arr_data[] = '<price>'.($data['price'] - $basePrice['price'] + $text_option_price + $add_option_price).'</price>';//옵션금액 - 기본판매가 + 입력형옵션 추가금액

			$arr_data[] = implode("\n", $only_add);

			$arr_data[] = '</option>';
		}
	}


	//배송정책 정보설정
	$arr_data[] = '<shippingPolicy>';

	$arr_data[] = '<groupId>'.$naver_delivery['group_id'].'</groupId>';//배송비 묶음 그룹
	$arr_data[] = '<feeType>'.$naver_delivery['feeType'].'</feeType>';//배송비유형 FREE=무료, CHARGE=유료, CONDITIONAL_FREE=조건부무료, CHARGE_BY_QUANTITY=수량별부과
	$arr_data[] = '<feePayType>'.$naver_delivery['feePayType'].'</feePayType>';//배송비 결제방법 FREE=무료, PREPAYED=선불, CASH_ON_DELIVERY=착불
	$arr_data[] = '<feePrice>'.$naver_delivery['delivery_price'].'</feePrice>';//배송비 금액

	//지역별 배송비 설정
	if ($checkoutCfg['area_delivery'] != 'n' && $checkoutCfg['area_delivery'] != '') {
		$arr_data[] = '<surchargeByArea>';
		$arr_data[] = '<splitUnit>' .$checkoutCfg['area_delivery']. '</splitUnit>';//사용권역 2 = 2권역, 3 = 3권역
		if($checkoutCfg['area_delivery'] == '2') {
			$arr_data[] = '<area2Price>' .$checkoutCfg['area22Price']. '</area2Price>';//2권역 지역별 배송비
		} else if($checkoutCfg['area_delivery'] == '3') {
			$arr_data[] = '<area2Price>' .$checkoutCfg['area32Price']. '</area2Price>';//2권역 지역별 배송비
			$arr_data[] = '<area3Price>' .$checkoutCfg['area33Price']. '</area3Price>';//3권역 지역별 배송비
		} else {
			msg("지역별 배송비가 잘못설정 되어 있습니다.\n 관리자에게 문의해 주세요.");
			exit;
		}
		$arr_data[] = '</surchargeByArea>';
	}
	
	$arr_data[] = '</shippingPolicy>';

	$arr_data[] = '</product>';

}

$arr_data[] = '<interface>';
$arr_data[] = '<salesCode></salesCode>';
$arr_data[] = '<cpaInflowCode>'. $_COOKIE["CPAValidator"]. '</cpaInflowCode>';//지식쇼핑 가맹점에서 사용.(쿠키에서 CPAValidator 값을 입력)
$arr_data[] = '<mileageInflowCode>'. $_COOKIE['NA_MI']. '</mileageInflowCode>';//네이버페이 포인트 가맹점에서 사용(네이버 페이 포인트 연동 가이드 > 유입 파라미터 참고)
$arr_data[] = '<naverInflowCode>'. $_COOKIE["NA_CO"]. '</naverInflowCode>';//쿠키에서 NA_CO 값을 입력
$arr_data[] = '<saClickId>'. $_COOKIE["NVADID"]. '</saClickId>';//네이버 검색광고에서 사용
$arr_data[] = '<merchantCustomCode1>'. $checkSno. '</merchantCustomCode1>';//네이버 주문등록 sno
$arr_data[] = '</interface>';

$backUrl = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$data['goodsno'];
$arr_data[] = '<backUrl><![CDATA[' . $backUrl.']]></backUrl>';

$arr_data[] = '</order>';

$post_data = iconv('euc-kr', 'utf-8', implode("\n", $arr_data));

//주문 등록호출
if($checkoutCfg['testYn']=='y') {
	$url = "https://test-api.pay.naver.com/o/customer/api/order/v20/register";//테스트
} else {
	$url = "https://api.pay.naver.com/o/customer/api/order/v20/register";//실서버
}

$headers = array('Content-Type: application/xml; charset=utf-8');


$ci = curl_init();
curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ci, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ci, CURLOPT_URL, $url);
curl_setopt($ci, CURLOPT_POST, TRUE);
curl_setopt($ci, CURLOPT_TIMEOUT, 10);
curl_setopt($ci, CURLOPT_POSTFIELDS, $post_data);

$response = curl_exec($ci);
curl_close ($ci);

// 주문 등록 후 결과처리
$param = explode(':', $response);

if ($param[0] == "SUCCESS") {
	// 성공일 경우
	$requestParam = "/".$param[1]."/".$param[2];
}
else {
	msg(iconv('utf-8', 'euc-kr', $response));
	exit;
}


if($naver_delivery['feePayType'] == 'PREPAYED') $totalPrice += (int)$naver_delivery['delivery_price'];

$checkout_query = 'update '.GD_NAVERCHECKOUT.' set `shipping_type`="'.$shippingType[$naver_delivery['feePayType']].'", `shipping_price`="'.$naver_delivery['delivery_price'].'", `total_price`="'.$totalPrice.'", regdt=now()';

if($checkout_query) {
	$db->query($checkout_query.', `orderId`="'.$param[1]."/".$param[2].'" where `checkoutSno`="'.$checkSno.'"');

	if (is_object($cart) && method_exists($cart, 'buy')) {
		$cart->buy();
	}
}

// 주문서 URL 재전송(redirect)
if($checkoutCfg['testYn']=='y') {
	$redirectUrl = "https://test-order.pay.naver.com/customer/buy".$requestParam;//테스트
} else {
	$redirectUrl = "https://order.pay.naver.com/customer/buy".$requestParam;//실서버
}
?>

<html>
<body>
<form name="frm" method="get" action="<?=$redirectUrl?>">
</form>
</body>
<script>
<?php if(isset($_GET['isMobile'])){ ?>
document.frm.target = "_self";
document.frm.submit();
<?php }else{ ?>
document.frm.target = "_blank";
document.frm.submit();
<?php } ?>
</script>
</html>
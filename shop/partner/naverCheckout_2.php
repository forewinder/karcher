<?php
/**************************************************
 * v2.1 네이버 페이에서 요청한 상품정보 전송
 * naverCheckout.php에서 include로 사용한다.
 *************************************************/
parse_str(urldecode($_SERVER['QUERY_STRING']));

if($checkoutCfg['useYn']!='y') exit;

$NaverCheckout = &load_class('NaverCheckout','NaverCheckout');
if(!$NaverCheckout->check_use()) exit;

//기본값 설정
$tax_type['1'] = 'TAX';
$tax_type['0'] = 'TAX_FREE';

$shippingType['PREPAYED'] = 'PAYED';//선불
$shippingType['FREE'] = 'FREE';//무료
$shippingType['CASH_ON_DELIVERY'] = 'ONDELIVERY';//후불

if(empty($product)) exit;

//상품v2 적용여부 체크
$v2 = false;
if(is_dir($_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/lib/Clib') === true) $v2 = true;

//본상품에 대한 상품조회 여부  true = 본상품조회, false 특정상품조회
$total_goods_search = false;

//주문등록시 등록된 DB[gd_navercheckout] sno
if(!$merchantCustomCode1) exit;//네이버페이 v2.1 주문시 필수로 설정되는 값이다.

// Todo gd_navercheckout 데이터가 필요한가?
$query = $db->_query_print('SELECT * FROM '.GD_NAVERCHECKOUT.' WHERE checkoutSno=[i]', $merchantCustomCode1);
$checkout = $db->fetch($query);

$query = $db->_query_print('SELECT * FROM '.GD_NAVERCHECKOUT_ITEM.' WHERE checkoutSno=[i]', $merchantCustomCode1);
$checkout_item = $db->_select($query);
if(empty($checkout_item)) exit;

//바로구매 모드로 장바구니 호출
$_COOKIE['gd_isDirect'] = 1;
$cart = new Cart($_COOKIE['gd_isDirect']);	// isdirect = 1
$cart->get_uid(true);//장바구니 uid 초기화


$db_goods = $db_goods_option = $db_goods_add = array();//DB 상품관련정보
$naver_goods_data = $naver_all_option_item = $naver_option_item = $naver_add_option = array();//네이버 전송용 데이터

$add_cart = true;

foreach($checkout_item as $item) {
	$cart_add_option = $cart_text_option = array();//카트등록시 사용
	$sales = false;//상품판매 가능여부 ( false = 판매가능, true = 판매불가 )

	if(empty($db_goods[$item['goodsno']])) {
		$db_goods[$item['goodsno']] = $db->fetch($db->_query_print("select * from ".GD_GOODS." WHERE goodsno=[s]", $item['goodsno']), true);//상품정보

		if($v2 === true) {
			$tmp_goods_option = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno=[s] AND go_is_deleted=[i] ORDER BY link desc", $item['goodsno'], 1));//선택옵션
			$tmp_goods_add = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_ADD." WHERE goodsno=[s] ORDER BY type desc, step asc", $item['goodsno'], 1));//입력옵션, 추가상품

			foreach($tmp_goods_add as $tga) {
				$db_goods_add[$item['goodsno']][$tga['type']][$tga['sno']] = $tga;
			}

		} else {
			$tmp_goods_option = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno=[s] ORDER BY link desc", $item['goodsno'], 1));//선택옵션
			$tmp_goods_add = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_ADD." WHERE goodsno=[s] ORDER BY step asc", $item['goodsno'], 1));//입력옵션, 추가상품

			//v2이전에는 선택형 추가옵션만 존재함
			foreach($tmp_goods_add as $tga) {
				$db_goods_add[$item['goodsno']]['S'][$tga['sno']] = $tga;
			}
		}

		//상품 기본 판매가
		$base_price[$item['goodsno']] = $tmp_goods_option[0]['price'];

		//옵션유무
		if($tmp_goods_option[0]['opt1'] != '') $option_bool[$item['goodsno']] = true;
		else $option_bool[$item['goodsno']] = false;

		foreach($tmp_goods_option as $tgo) {
			$db_goods_option[$item['goodsno']][$tgo['optno']] = $tgo;
		}

		//장바구니에서 사용될 데이터를 설정한다.
		if(isset($add_goods_data[$item['goodsno']]) === false) {
			$temp_add_data = explode('|', $db_goods[$item['goodsno']]['addoptnm']);
			foreach($temp_add_data as $add) {
				$add_data = explode('^', $add);
				if($add_data[count($add_data)-1] == 'S') {
					$add_goods_data[$item['goodsno']][] = $add_data;
				} else {
					$text_data[$item['goodsno']][] = $add_data;
				}
			}
		}
	}



	//주문옵션 정보
	/*
		0	옵션번호
		1	추가옵션번호
		2	수량
	*/
	$arr_option_info = explode('^', $item['option_info']);

	/*상품데이터 설정*/
	if(!$NaverCheckout->check_banWords($db_goods[$item['goodsno']]['goodsnm'])) $sales = true;
	if(!$NaverCheckout->check_exceptions($item['goodsno'])) $sales = true;

	if($v2 === false && isset($db_goods[$item['goodsno']]['option_name']) === false) {
		$db_goods[$item['goodsno']]['option_name'] = $db_goods[$item['goodsno']]['optnm'];
	}

	if(!$db_goods[$item['goodsno']]['goodsno']) {
		echo "올바른 접근이 아닙니다.[empty goodsno]";
		exit;
	}

	//네이버에 전송될 상품정보 설정 여부
	if(empty($create_goods[$item['goodsno']])) $create_goods[$item['goodsno']] = true;
	else $create_goods[$item['goodsno']] = false;

	//최소,최대구매수량체크
	$max_min[$item['goodsno']] = false;
	$arr_info = explode('^', $item['option_info']);
	if(($arr_info[2] < $db_goods[$item['goodsno']]['min_ea']) || ($db_goods[$item['goodsno']]['max_ea'] != '0' && $arr_info[2] > $db_goods[$item['goodsno']]['max_ea'])) {
		if($max_min[$item['goodsno']] !== true) {
			$max_min[$item['goodsno']] = true;
			$create_goods[$item['goodsno']] = true;
		}
	}

	//상품 진열여부
	if($db_goods[$item['goodsno']]['open'] != '1') $sales = true;

	//상품 품절설정 여부
	if($db_goods[$item['goodsno']]['runout'] == '1') $sales = true;

	if($create_goods[$item['goodsno']] === true) {
		unset($naver_goods_data[$item['goodsno']]);

		if($sales === true) {
			//네이버 페이 설정에서 판매금지 상품으로 설정되어 있는 경우
			$naver_goods_data[$item['goodsno']][] = '<status>NOT_SALE</status>';//본상품 거래상태
			$add_cart = false;//최소/최대구매수량을 초과하는 경우 alert가 출력되어 카드담기 안함
		} else if($max_min[$item['goodsno']] === true) {
			//최소/최대구매 수량을 초과하는 경우
			$naver_goods_data[$item['goodsno']][] = '<status>NOT_SALE</status>';//본상품 거래상태
			$add_cart = false;//최소/최대구매수량을 초과하는 경우 alert가 출력되어 카드담기 안함
		} else {
			if ($db_goods[$item['goodsno']]['usestock'] && $db_goods[$item['goodsno']]['totstock'] < 1) {//재고관리 중 재고가 1보다 작은 경우 품절처리
				$naver_goods_data[$item['goodsno']][] = '<stockQuantity>0</stockQuantity>';//상품재고(0=품절 구매불가, 1이상 구매가능)
				$naver_goods_data[$item['goodsno']][] = '<status>SOLD_OUT</status>';//재고부족 실패처리
			} else {// 재고관리를 안하거나 재고가 있는 경우
				if(!$db_goods[$item['goodsno']]['usestock'] && $db_goods[$item['goodsno']]['totstock'] < 1) $db_goods[$item['goodsno']]['totstock'] = '10000';//재고관리를 안하고 입력된 재고가 0인 경우
				$naver_goods_data[$item['goodsno']][] = '<stockQuantity>'.$db_goods[$item['goodsno']]['totstock'].'</stockQuantity>';//상품재고(0=품절 구매불가, 1이상 구매가능)
				$naver_goods_data[$item['goodsno']][] = '<status>ON_SALE</status>';
			}
		}

		//상품 이미지 설정
		$arr_img = explode('|', $db_goods[$item['goodsno']]['img_l']);

		//상품 기본정보 설정
		$naver_goods_data[$item['goodsno']][] = '<id>' .$db_goods[$item['goodsno']]['goodsno']. '</id>';//상품코드
		$naver_goods_data[$item['goodsno']][] = '<merchantProductId>' .$db_goods[$item['goodsno']]['goodscd']. '</merchantProductId>';//판매자상품번호
		$naver_goods_data[$item['goodsno']][] = '<ecMallProductId>' .$db_goods[$item['goodsno']]['goodsno']. '</ecMallProductId>';//지식쇼핑 ep의 mall_pid
		$naver_goods_data[$item['goodsno']][] = '<name><![CDATA[' .urldecode(str_replace($not_string, '', urlencode($db_goods[$item['goodsno']]['goodsnm']))). ']]></name>';//상품명
		$naver_goods_data[$item['goodsno']][] = '<basePrice>' .$base_price[$item['goodsno']]. '</basePrice>';//본상품 1개의 판매가격
		$naver_goods_data[$item['goodsno']][] = '<taxType>' .$tax_type[$db_goods[$item['goodsno']]['tax']]. '</taxType>';//과세종류 TAX=과세, TAX_FREE=면세, ZERO_TAX=영세
		$naver_goods_data[$item['goodsno']][] = '<infoUrl><![CDATA[' ."http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$db_goods[$item['goodsno']]['goodsno']. ']]></infoUrl>';//상품상세 페이지 URL
		$naver_goods_data[$item['goodsno']][] = '<imageUrl><![CDATA[' .'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$arr_img[0]. ']]></imageUrl>';//상품원본이미지 URL
	//	$naver_goods_data[$item['goodsno']][] = '<giftName><![CDATA[]]></giftName>';//사은품명
		if($checkoutCfg['return_price'] != '') {
			$naver_goods_data[$item['goodsno']][] = '<returnShippingFee><![CDATA[' .$checkoutCfg['return_price']. ']]></returnShippingFee>';//편도 반품 배송비
			$naver_goods_data[$item['goodsno']][] = '<exchangeShippingFee><![CDATA[' .($checkoutCfg['return_price'] * 2). ']]></exchangeShippingFee>';//교환배송비
		}
	}



	//선택옵션 장바구니 데이터 설정
	$_opt = array();
	$_opt[] = $db_goods_option[$item['goodsno']][$arr_option_info[0]]['opt1'];
	$_opt[] = $db_goods_option[$item['goodsno']][$arr_option_info[0]]['opt2'];


	$array_add_name = array();
	if($db_goods[$item['goodsno']]['addoptnm'] != '') {
		$add_name = explode('|', $db_goods[$item['goodsno']]['addoptnm']);
		foreach($add_name as $tmp_add_name) {
			$tmp_add_name = explode('^', $tmp_add_name);
			$array_add_name[$tmp_add_name[count($tmp_add_name)-1]][] = $tmp_add_name[0];
		}
	}


	$selected_add = explode(',', $arr_option_info[1]);

	//선택옵션 네이버 전송 데이터 설정
	if($option_bool[$item['goodsno']] === true) {

		$option_name = explode('|', $db_goods[$item['goodsno']]['option_name']);

		//상품에 등록된 선택옵션 전체 데이터 설정
		if(isset($naver_all_option_item[$item['goodsno']]) === false) {
			$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
			$naver_all_option_item[$item['goodsno']][] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
			$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$db_goods[$item['goodsno']]['option_name']. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자

			foreach($db_goods_option[$item['goodsno']] as $goods_option_data) {
				$option_text = '';
				if($option_name[0] && $goods_option_data['opt1']) $option_text = $option_name[0].'/'.$goods_option_data['opt1'];
				if($option_name[1] && $goods_option_data['opt2']) $option_text .= ', '.$option_name[1].'/'.$goods_option_data['opt2'];

				$naver_all_option_item[$item['goodsno']][] = '<value>';
				$naver_all_option_item[$item['goodsno']][] = '<id>S'.$goods_option_data['sno'].'</id>';
				$naver_all_option_item[$item['goodsno']][] = '<text><![CDATA['.$option_text.']]></text>';
				$naver_all_option_item[$item['goodsno']][] = '</value>';
			}

			$naver_all_option_item[$item['goodsno']][] = '</optionItem>';

			//상품에 등록된 입력옵션 전체 데이터 설정
			$arr_text_option_check = explode('|', $db_goods[$item['goodsno']]['addoptnm']);
			if(empty($arr_text_option_check) === false) {
				for($o = 0; $o < count($arr_text_option_check); $o++) {
					if(substr($arr_text_option_check[$o], -1, 1) == 'I') {
						$arr_text = explode('^', $arr_text_option_check[$o]);
						$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
						$naver_all_option_item[$item['goodsno']][] = '<type>INPUT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
						$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$arr_text[0]. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
						$naver_all_option_item[$item['goodsno']][] = '</optionItem>';
					}
				}
			}

			//선택된 추가옵션만 전체옵션 데이터에 추가설정함
			if(isset($db_goods_add[$item['goodsno']]['S'])) {
				foreach($db_goods_add[$item['goodsno']]['S'] as $add_data) {
					if(is_int(array_search($add_data['sno'], $selected_add))) {
						$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
						$naver_all_option_item[$item['goodsno']][] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
						$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$array_add_name['S'][$add_data['step']]. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자

						$naver_all_option_item[$item['goodsno']][] = '<value>';
						$naver_all_option_item[$item['goodsno']][] = '<id>A' .$add_data['sno']. '</id>';
						$naver_all_option_item[$item['goodsno']][] = '<text><![CDATA[' .$add_data['opt']. ']]></text>';
						$naver_all_option_item[$item['goodsno']][] = '</value>';
						$naver_all_option_item[$item['goodsno']][] = '</optionItem>';
					}
				}
			}
		}

		//선택한 옵션에 대한 정보만 입력한다
		//입력옵선 추가금액만 포함시킨다.
		$text_option_status = 'true';
		$add_step = $text_option_price = 0;

		if(isset($db_goods_add[$item['goodsno']]['I'])) {
			foreach($db_goods_add[$item['goodsno']]['I'] as $add_data) {
				if(is_int(array_search($add_data['sno'], $selected_add))) {//입력형 옵션
					$text_option_price += $add_data['addprice'];
					if($add_data['stats'] != '1') $text_option_status = 'false';

					$tmp_cart_text = array();
					$tmp_cart_text[] = $add_data['addno'];
					$tmp_cart_text[] = $text_data[$item['goodsno']][$add_data['step']][0].'^G';
					$tmp_cart_text[] = $add_data['addprice'];
					$cart_text_option[] = implode('^', $tmp_cart_text);
				}
			}
		}


		//재고설정 - 무제한 판매인 경우 99999설정
		if(!$db_goods[$item['goodsno']]['usestock'] && $db_goods_option[$item['goodsno']][$arr_option_info[0]]['stock'] < 1) $db_goods_option[$item['goodsno']][$arr_option_info[0]]['stock'] = '99999';

		$naver_option_item[$item['goodsno']][] = '<combination>';
		$naver_option_item[$item['goodsno']][] = '<manageCode>S' .$arr_option_info[0]. '</manageCode>';//옵션 sno

		$naver_option_item[$item['goodsno']][] = '<stockQuantity>'.$db_goods_option[$item['goodsno']][$arr_option_info[0]]['stock'].'</stockQuantity>';
		$naver_option_item[$item['goodsno']][] = '<options>';
		$naver_option_item[$item['goodsno']][] = '<id>S'.$arr_option_info[0].'</id>';
		$naver_option_item[$item['goodsno']][] = '<name><![CDATA['.$db_goods[$item['goodsno']]['option_name'].']]></name>';
		$naver_option_item[$item['goodsno']][] = '</options>';

		//추가상품 데이터 설정 - 추가상품에 - 금액이 설정되지 않아 옵션정보로 등록한다.
		$add_option_price = 0;
		$add_option_status = 'true';
		if(isset($db_goods_add[$item['goodsno']]['S'])) {
			foreach($db_goods_add[$item['goodsno']]['S'] as $add_data) {
				if(is_int(array_search($add_data['sno'], $selected_add))) {
					$naver_option_item[$item['goodsno']][] = '<options>';
					$naver_option_item[$item['goodsno']][] = '<id>A'.$add_data['sno'].'</id>';
					$naver_option_item[$item['goodsno']][] = '<name><![CDATA['.$array_add_name['S'][$add_data['step']].']]></name>';
					$naver_option_item[$item['goodsno']][] = '</options>';

					$add_option_price += $add_data['addprice'];
					if($add_data['stats'] != '1') $add_option_status = 'false';
				}
			}
		}


		//옵션별 추가금액(기본값0, 본상품가격 -50%이상 10,000원이면 -5000 이상) 옵션관리코드 필수
		$naver_option_item[$item['goodsno']][] = '<price>'.($db_goods_option[$item['goodsno']][$arr_option_info[0]]['price'] - $base_price[$item['goodsno']] + $text_option_price + $add_option_price).'</price>';// (기본판매가 - 옵션판매가) + 입력옵션 추가금액

		if($text_option_status == 'false' || $add_option_status == 'false') $naver_option_item[$item['goodsno']][] = '<status>false</status>';

		$naver_option_item[$item['goodsno']][] = '</combination>';


	} else {
		/* 선택옵션이 없는 경우 입력옵션체크, 입력옵션이 없으면 무옵션 상품으로 설정 */

		$text_option_bool = $add_option_bool = false;//입력옵션 여부 true=있음, false=없음
		$add_option_price = $add_step = 0;
		$add_option_status = 'true';
		$arr_temp_data = array();

		if(isset($db_goods_add[$item['goodsno']]['I'])) {
			foreach($selected_add as $selected_add_sno) {
				if(empty($selected_add_sno) === false && isset($array_add_name['I'][$add_step]) === true) {
						$arr_temp_data[] = '<options>';
						$arr_temp_data[] = '<name><![CDATA['.$array_add_name['I'][$add_step].']]></name>';
						$arr_temp_data[] = '<id>T'.$selected_add_sno.'</id>';
						$arr_temp_data[] = '</options>';

						$add_option_price += $db_goods_add[$item['goodsno']]['I'][$selected_add_sno]['addprice'];
						if($db_goods_add[$item['goodsno']]['I'][$selected_add_sno]['stats'] != '1') $add_option_status = 'false';
						$text_option_bool = true;

						$tmp_cart_text = array();
						$tmp_cart_text[] = $db_goods_add[$item['goodsno']]['I'][$selected_add_sno]['addno'];
						$tmp_cart_text[] = $text_data[$item['goodsno']][$db_goods_add[$item['goodsno']]['I'][$selected_add_sno]['step']][0].'^G';
						$tmp_cart_text[] = $db_goods_add[$item['goodsno']]['I'][$selected_add_sno]['addprice'];
						$cart_text_option[] = implode('^', $tmp_cart_text);
				}
				$add_step++;
			}
		}

		//추가상품 관련 데이터 설정 - 추가상품의 마이너스 금액을 설정할 수 없어 선택옵션으로 설정함
		$tmp_add_data = array();
		if(isset($db_goods_add[$item['goodsno']]['S'])) {

			foreach($db_goods_add[$item['goodsno']]['S'] as $add_data) {
				if(is_int(array_search($add_data['sno'], $selected_add))) {
					//전체옵션에 사용될 데이터
					$tmp_add_data[] = '<optionItem>';
					$tmp_add_data[] = '<type>SELECT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
					$tmp_add_data[] = '<name><![CDATA[' .$array_add_name['S'][$add_data['step']]. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
					$tmp_add_data[] = '</optionItem>';

					$tmp_add_data[] = '<value>';
					$tmp_add_data[] = '<id>A' .$add_data['sno']. '</id>';
					$tmp_add_data[] = '<text><![CDATA[' .$add_data['opt']. ']]></text>';
					$tmp_add_data[] = '</value>';

					//선택된 옵션에 사용될 데이터
					$arr_temp_data[] = '<options>';
					$arr_temp_data[] = '<id>A'.$add_data['sno'].'</id>';
					$arr_temp_data[] = '<name><![CDATA['.$array_add_name['S'][$add_data['step']].']]></name>';
					$arr_temp_data[] = '</options>';

					$add_option_price += $add_data['addprice'];
					if($add_data['stats'] != '1') $add_option_status = 'false';
					$add_option_bool = true;
				}
			}
		}


		if(isset($naver_all_option_item[$item['goodsno']]) === false) {
			//상품에 등록된 입력옵션 전체 데이터 설정
			if($text_option_bool !== false) {
				foreach($array_add_name['I'] as $add_option_name) {
					$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
					$naver_all_option_item[$item['goodsno']][] = '<type>INPUT</type>';//옵션유형 - SELECT=선택형, INPUT=입력형
					$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$add_option_name. ']]></name>';//옵션명 ex)색상, 사이즈 - 최대 20자
					$naver_all_option_item[$item['goodsno']][] = '</optionItem>';
				}
			}

			if($add_option_bool !== false) {
				$naver_all_option_item[$item['goodsno']][] = implode("\n", $tmp_add_data);
			}
		}

		if(empty($arr_temp_data) === false) {
			//주문시 선택한 입력옵션 데이터만 설정
			$naver_option_item[$item['goodsno']][] = '<combination>';
			$naver_option_item[$item['goodsno']][] = '<manageCode>'.$item['itemSno'].'</manageCode>';
			$naver_option_item[$item['goodsno']][] = '<stockQuantity>999</stockQuantity>';
			$naver_option_item[$item['goodsno']][] = '<status>'.$add_option_status.'</status>';
			$naver_option_item[$item['goodsno']][] = '<price>'.$add_option_price.'</price>';
			$naver_option_item[$item['goodsno']][] = implode("\n", $arr_temp_data);

			$naver_option_item[$item['goodsno']][] = '</combination>';
		}

	}


	/*추가상품 장바구니 데이터 설정*/
	foreach($selected_add as $sadd) {
		if(isset($db_goods_add[$item['goodsno']]['S'][$sadd])) {
			$check_add_option[$item['goodsno']][] = $sadd;

			$tmp_cart_add = array();
			$tmp_cart_add[] = $db_goods_add[$item['goodsno']]['S'][$sadd]['addno'];
			$tmp_cart_add[] = $add_goods_data[$item['goodsno']][$db_goods_add[$item['goodsno']]['S'][$sadd]['step']][0];
			$tmp_cart_add[] = $db_goods_add[$item['goodsno']]['S'][$sadd]['opt'];
			$tmp_cart_add[] = $db_goods_add[$item['goodsno']]['S'][$sadd]['addprice'];

			$cart_add_option[] = implode('^', $tmp_cart_add);
		}
	}

	//장바구니 등록 - 바로구매
	if($add_cart === true) $cart->addCart($item['goodsno'],$_opt,$cart_add_option,$cart_text_option,$arr_option_info[2],$_POST[goodsCoupon]);
}

/* 배송비 정보 설정 */
$param = array(
'mode' => '0',
'deliPoli' => 0,
'marketingType' => 'naverCheckout'
);
$tmp = getDeliveryMode($param);
//장바구니 정보 초기화 - 바로구매 내역 삭제
$cart->emptyCart();

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

//배송정책 정보설정
$delivery_data[] = '<shippingPolicy>';

$delivery_data[] = '<groupId>'.$naver_delivery['group_id'].'</groupId>';//배송비 묶음 그룹
$delivery_data[] = '<feeType>'.$naver_delivery['feeType'].'</feeType>';//배송비유형 FREE=무료, CHARGE=유료, CONDITIONAL_FREE=조건부무료, CHARGE_BY_QUANTITY=수량별부과
$delivery_data[] = '<feePayType>'.$naver_delivery['feePayType'].'</feePayType>';//배송비 결제방법 FREE=무료, PREPAYED=선불, CASH_ON_DELIVERY=착불
$delivery_data[] = '<feePrice>'.$naver_delivery['delivery_price'].'</feePrice>';//배송비 금액

//지역별 배송비 설정
if ($checkoutCfg['area_delivery'] != 'n' && $checkoutCfg['area_delivery'] != '') {
	$delivery_data[] = '<surchargeByArea>';
	$delivery_data[] = '<splitUnit>' .$checkoutCfg['area_delivery']. '</splitUnit>';//사용권역 2 = 2권역, 3 = 3권역
	if($checkoutCfg['area_delivery'] == '2') {
		$delivery_data[] = '<area2Price>' .$checkoutCfg['area22Price']. '</area2Price>';//2권역 지역별 배송비
	} else if($checkoutCfg['area_delivery'] == '3') {
		$delivery_data[] = '<area2Price>' .$checkoutCfg['area32Price']. '</area2Price>';//2권역 지역별 배송비
		$delivery_data[] = '<area3Price>' .$checkoutCfg['area33Price']. '</area3Price>';//3권역 지역별 배송비
	} else {
		//지역별 배송비가 잘못 설정되어 있는 경우
		exit;
	}
	$delivery_data[] = '</surchargeByArea>';
}

$delivery_data[] = '</shippingPolicy>';


/* 네이버 전송데이터 병합 */

$view_data[] = '<?xml version="1.0" encoding="utf-8"?>';
$view_data[] = '<products>';

foreach($naver_goods_data as $goodsno => $goods_data) {
	$view_data[] = '<product>';

	//상품정보
	$view_data[] = implode("\n", $goods_data);

	if(isset($naver_all_option_item[$goodsno])) {
		$view_data[] = '<optionSupport>true</optionSupport>';
		$view_data[] = '<option>';

		$view_data[] = implode("\n", $naver_all_option_item[$goodsno]);	//전체옵션정보
		
		if(isset($naver_option_item[$goodsno])) $view_data[] = implode("\n", $naver_option_item[$goodsno]);//선택옵션정보
		
		$view_data[] = '</option>';
	}

	//추가상품 데이터
	if(isset($naver_add_option[$goodsno])) $view_data[] = implode("\n", $naver_add_option[$goodsno]);

	//배송정보
	$view_data[] = implode("\n", $delivery_data);

	$view_data[] = '</product>';
}

$view_data[] = '</products>';

$post_data = iconv('euc-kr', 'utf-8', implode("\n", $view_data));

header('Content-Type: application/xml;charset=utf-8');
print_r($post_data);
exit;

?>
<?php
/*******************************************************************************************
 * ���̹����̿��� �ֹ�ID�� �߱޹ް� �ֹ������� �������� �ѱ� �� ���� �������� ��â���� ���
 *******************************************************************************************/

include "../lib/library.php";
include "../lib/load.class.php";
@include "../conf/naverCheckout.cfg.php";
require "../lib/naverCheckout.class.php";
require "../conf/config.php";
include "../lib/cart.class.php";

// ASCII(�����) ���ڰ� �ִ� ��� ���̹� ���� API���� �ֹ���ȸ�� ���� �ʾ� ��ǰ���� ���۽� ����
$not_string = Array('%01','%02','%03','%04','%05','%06','%07','%08','%09','%0A','%0B','%0C','%0D','%0E','%0F','%10','%11','%12','%13','%14','%15','%16','%17','%18','%19','%1A','%1B','%1C','%1D','%1E','%1F');

//��ǰv2 ���뿩�� üũ
$v2 = false;
if(is_dir($_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/lib/Clib') === true) $v2 = true;

//�⺻�� ����
$tax_type['1'] = 'TAX';
$tax_type['0'] = 'TAX_FREE';

$shippingType['PREPAYED'] = 'PAYED';//����
$shippingType['FREE'] = 'FREE';//����
$shippingType['CASH_ON_DELIVERY'] = 'ONDELIVERY';//�ĺ�

if ($_GET[mode]!='cart') {
	// ��ٱ��� ��尡 �ƴϸ�, ���� ��ǰ�� ��ٱ��Ͽ� ��� ó�� �Ѵ�.
	$_COOKIE['gd_isDirect'] = 1;
	$cart = new Cart($_COOKIE['gd_isDirect']);	// isdirect = 1

	// ��Ƽ�ɼ�
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

if($tmp['type'] == '����') {
	$naver_delivery['feeType'] = 'CHARGE';
	$naver_delivery['feePayType'] = 'PREPAYED';
	$naver_delivery['delivery_price'] = $tmp['price'];
} else if($tmp['type'] == '�ĺ�') {
	$naver_delivery['feeType'] = 'CHARGE';
	$naver_delivery['feePayType'] = 'CASH_ON_DELIVERY';
	$naver_delivery['delivery_price'] = $checkoutCfg['collect'];
} else if($tmp['type'] == '����') {//���Ǻ� ����
	$naver_delivery['feeType'] = 'FREE';
	$naver_delivery['feePayType'] = 'FREE';
	$naver_delivery['delivery_price'] = 0;
}

$NaverCheckout = &load_class('NaverCheckout','NaverCheckout');
if(!$NaverCheckout->check_use()) msg('���̹����� ����� �ߴ� �Ͽ����ϴ�.', -1);

if($v2 === true) chkOpenYn($item,"D", -1);

//���� �ֹ����� ����
$checkout_query = 'insert into '.GD_NAVERCHECKOUT.' set `shipping_type`="", `shipping_price`="", `total_price`="", `orderId`="", regdt=now()';
$db->query($checkout_query);
$checkSno = $db->lastID();
if($checkSno === false || $checkSno < 1) {
	msg('�ֹ����� ������ ���еǾ����ϴ�.', -1);
}

$arr_data[] = '<?xml version="1.0" encoding="utf-8"?>';
$arr_data[] = '<order>';
$arr_data[] = '<merchantId>' . $checkoutCfg['naverId'] . '</merchantId>';//���̹� ���� ����Ű
$arr_data[] = '<certiKey>' . $checkoutCfg['connectId'] . '</certiKey>';//���� ����Ű

foreach($item as $goods) {

	$arr_option_info = '';
	$tmp_array_option_info = Array();
	$tmp_array_option_info[] = $goods['optno'];//�ɼǹ�ȣ
	$tmp_array_option_info[] = $goods['addno'];//�߰��ɼǹ�ȣ - 1,2,3,4 or ,2,3,4
	$tmp_array_option_info[] = $goods['ea'];//����
	$option_info = implode('^', $tmp_array_option_info);


	if($v2 === true) {
		$query = "select a.*, b.sno as optsno, b.price, b.reserve, b.stock from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' where a.goodsno='$goods[goodsno]' and opt1='{$goods[opt][0]}' and opt2='{$goods[opt][1]}' limit 1";

		$basePrice = $db->fetch($db->_query_print('SELECT price FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] AND go_is_deleted=[i] ORDER BY link DESC', $goods[goodsno], 1), true);
	} else {
		$query = "select a.*, b.sno as optsno, b.price, b.reserve, b.stock from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno where a.goodsno='$goods[goodsno]' and opt1='{$goods[opt][0]}' and opt2='{$goods[opt][1]}' limit 1";

		$basePrice = $db->fetch($db->_query_print('SELECT price FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] ORDER BY link DESC', $goods[goodsno], 1), true);
	}

	$data = $db->fetch($query);

	if(!$NaverCheckout->check_banWords($data['goodsnm']))$errmsg=$data['goodsnm']."�� ���̹����� ���񽺿� ��밡���� ��ǰ�� �ƴմϴ�.";
	if(!$NaverCheckout->check_exceptions($data['goodsno']))$errmsg=$data['goodsnm']."�� ���̹����� ���񽺿� ��밡���� ��ǰ�� �ƴմϴ�.";
	if($data['optnm'])$optnm = explode('|',$data['optnm']);

	if(!$data['goodsno'])$errmsg="�ùٸ� ������ �ƴմϴ�.";
	if ($data['usestock'] && $data['stock']<$goods['ea'])$errmsg = $data['goodsnm']."�� ��� ���ڶ��ϴ�.";

	if (empty($goods[opt][0])) unset($goods[opt][0]);
	if (empty($goods[opt][1])) unset($goods[opt][1]);

	// �ּ�,�ִ뱸�ż���üũ
	$min_ea = $data['min_ea'];
	$max_ea = $data['max_ea'];

	if($goods['ea'] < $min_ea) {
		$errmsg=$data['goodsnm']." ��ǰ�� �ּұ��ż����� {$min_ea}�� �Դϴ�.";
	}
	else if($max_ea!='0' && $goods['ea'] > $max_ea) {
		$errmsg=$data['goodsnm']." ��ǰ�� �ִ뱸�ż����� {$max_ea}�� �Դϴ�.";
	}

	if($errmsg) msg($errmsg, -1);

	//��ǰ �̹��� ����
	$arr_img = explode('|', $goods['img_l']);

	$totalPrice += $data['price'] * $goods['ea'];


	//��ǰ �⺻���� ����
	$arr_data[] = '<product>';
	$arr_data[] = '<id>' .$goods['goodsno']. '</id>';//��ǰ�ڵ�
	$arr_data[] = '<merchantProductId>' .$data['goodscd']. '</merchantProductId>';//�Ǹ��ڻ�ǰ��ȣ
	$arr_data[] = '<ecMallProductId>' .$data['goodsno']. '</ecMallProductId>';//���ļ��� ep�� mall_pid
	$arr_data[] = '<name><![CDATA[' .urldecode(str_replace($not_string, '', urlencode($data['goodsnm']))). ']]></name>';//��ǰ��
	$arr_data[] = '<basePrice>' .$basePrice['price']. '</basePrice>';//����ǰ 1���� �ǸŰ���
	$arr_data[] = '<taxType>' .$tax_type[$data['tax']]. '</taxType>';//�������� TAX=����, TAX_FREE=�鼼, ZERO_TAX=����
	$arr_data[] = '<infoUrl><![CDATA[' ."http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$data['goodsno']. ']]></infoUrl>';//��ǰ�� ������ URL
	$arr_data[] = '<imageUrl><![CDATA[' .'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$arr_img[0]. ']]></imageUrl>';//��ǰ�����̹��� URL
//	$arr_data[] = '<giftName><![CDATA[]]></giftName>';//����ǰ��
	if($checkoutCfg['return_price'] != '') {
		$arr_data[] = '<returnShippingFee><![CDATA[' .$checkoutCfg['return_price']. ']]></returnShippingFee>';//�� ��ǰ ��ۺ�
		$arr_data[] = '<exchangeShippingFee><![CDATA[' .($checkoutCfg['return_price'] * 2). ']]></exchangeShippingFee>';//�� ��ǰ ��ۺ�
	}

	$totalMoney += $data['price'];

	//�ɼ� ������ ����
	$arr_option_name = $option_text = Array();

	//item ���̺� ����Ǵ� itemSno�� manageCode�� ����ϱ� ���� �켱 �����Ѵ�.
	if(isset($goods['opt'])) {
		$arr_option_name = explode('|', $data['optnm']);
		for($os = 0; $os < count($goods['opt']); $os++) {
			if($goods['opt'][$os] == '') msg('�ɼ��� �������ּ���', -1);
			$goods['opt'][$os] = urldecode(str_replace($not_string, '', urlencode($goods['opt'][$os])));

			$option_text[] = $arr_option_name[$os].'/'.$goods['opt'][$os];
		}
	}

	$item_query = 'insert into '.GD_NAVERCHECKOUT_ITEM.' set `goodsno`="'.$data['goodsno'].'", `goodsnm`="'.$data['goodsnm'].'", `price`="'.$data['price'].'", `ea`="'.$goods['ea'].'", `option`="'.implode(', ', $option_text).'", `option_info`="'.$option_info.'"';
	$db->query($item_query.", `checkoutSno`='$checkSno'");
	$naver_item_sno = $db->lastID();



	if(isset($goods['opt']) || empty($goods['select_addopt']) === false) {

		$arr_data[] = '<option>';

		$arr_data[] = '<manageCode>S' .$goods['optno']. '</manageCode>';//�ɼ� sno
		$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';//�ɼ� �ֹ����� 1�̻�

		if(isset($goods['opt'])) {
			$arr_data[] = '<selectedItem>';
			$arr_data[] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
			$arr_data[] = '<name><![CDATA[' .$data['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
			$arr_data[] = '<value>';
			$arr_data[] = '<id>S' .$data['optsno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
			$arr_data[] = '<text><![CDATA[' .implode(', ', $option_text). ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
			$arr_data[] = '</value>';
			$arr_data[] = '</selectedItem>';
		}

		//�Է¿ɼ�
		$text_option_price = 0;
		if(empty($goods['input_addopt']) === false) {
			foreach($goods['input_addopt'] as $input_option) {
				$arr_data[] = '<selectedItem>';
				$arr_data[] = '<type>INPUT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
				$arr_data[] = '<name><![CDATA[' .$input_option['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
				$arr_data[] = '<value>';
				$arr_data[] = '<id>T' .$input_option['sno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
				$arr_data[] = '<text><![CDATA[' .$input_option['opt']. ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
				$arr_data[] = '</value>';
				$arr_data[] = '</selectedItem>';
				$text_option_price += $input_option['price'];
			}
		}

		//�߰���ǰ ���� ���� - �߰���ǰ�� ��� ���̳ʽ� �ݾ��� ������ �� ���� �Ϲ� �ɼ����� �����Ѵ�.
		$add_option_price = 0;
		if($v2 === true) {
			if(empty($goods['select_addopt']) === false) {
				foreach($goods['select_addopt'] as $add_option) {
					$arr_data[] = '<selectedItem>';
					$arr_data[] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
					$arr_data[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
					$arr_data[] = '<value>';
					$arr_data[] = '<id>A' .$add_option['sno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
					$arr_data[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
					$arr_data[] = '</value>';
					$arr_data[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		} else {
			if(empty($goods['addopt']) === false) {
				foreach($goods['addopt'] as $add_option) {
					$arr_data[] = '<selectedItem>';
					$arr_data[] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
					$arr_data[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
					$arr_data[] = '<value>';
					$arr_data[] = '<id>A' .$add_option['sno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
					$arr_data[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
					$arr_data[] = '</value>';
					$arr_data[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		}

		$arr_data[] = '<price>'.($data['price'] - $basePrice['price'] + $text_option_price + $add_option_price).'</price>';//�ɼǱݾ� - �⺻�ǸŰ� + �Է����ɼ� �߰��ݾ�

		$arr_data[] = '</option>';
	} else if(empty($goods['input_addopt']) === false) {
		$arr_data[] = '<option>';

		$text_option_manageCode = Array();
		$text_option_price = 0;
		foreach($goods['input_addopt'] as $input_option) {
			$arr_data[] = '<selectedItem>';
			$arr_data[] = '<type>INPUT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
			$arr_data[] = '<name><![CDATA[' .$input_option['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
			$arr_data[] = '<value>';
			$arr_data[] = '<id>T' .$input_option['sno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
			$arr_data[] = '<text><![CDATA[' .$input_option['opt']. ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
			$arr_data[] = '</value>';
			$arr_data[] = '</selectedItem>';
			$text_option_manageCode[] = 'T'.$input_option['sno'];
			$text_option_price += $input_option['price'];
		}
		$arr_data[] = '<manageCode>' .$naver_item_sno. '</manageCode>';//�ɼ� sno
		$arr_data[] = '<price>'.$text_option_price.'</price>';
		$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';
		$arr_data[] = '</option>';

	} else {
		$none_option = true;

		//�߰��ɼǸ� �ִ°�� �ɼǵ����� ����
		$add_option_price = $add_manage_code = 0;
		$only_add = array();

		if($v2 === true) {
			if(empty($goods['select_addopt']) === false) {
				$none_option = false;
				foreach($goods['select_addopt'] as $add_option) {
					if($add_manage_code == 0) $add_manage_code = $add_option['sno'];
					$only_add[] = '<selectedItem>';
					$only_add[] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
					$only_add[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
					$only_add[] = '<value>';
					$only_add[] = '<id>A' .$add_option['sno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
					$only_add[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
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
					$only_add[] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
					$only_add[] = '<name><![CDATA[' .$add_option['optnm']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
					$only_add[] = '<value>';
					$only_add[] = '<id>A' .$add_option['sno']. '</id>';//�ɼǰ� ex)R, XL - ����,����, Ư������( !+-/=_| ) ������Ұ� - �ִ� 50��
					$only_add[] = '<text><![CDATA[' .$add_option['opt']. ']]></text>';//���ÿɼ� �ؽ�Ʈ�� ex)����, XL - �ִ� 50��
					$only_add[] = '</value>';
					$only_add[] = '</selectedItem>';
					$add_option_price += $add_option['price'];
				}
			}
		}

		// �ɼ� ������ ���� ����ǰ �ֹ��� ����      <single> ��Ұ� �ݵ�� ���� �Ѵ�.
		if ($none_option === true) {
			$arr_data[] = '<single>';
			$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';
			$arr_data[] = '</single>';
		} else {//�߰���ǰ������ �ִ°��
			$arr_data[] = '<option>';

			$arr_data[] = '<manageCode>A' .$add_manage_code. '</manageCode>';//�ɼ� sno
			$arr_data[] = '<quantity>' .$goods['ea']. '</quantity>';//�ɼ� �ֹ����� 1�̻�
			$arr_data[] = '<price>'.($data['price'] - $basePrice['price'] + $text_option_price + $add_option_price).'</price>';//�ɼǱݾ� - �⺻�ǸŰ� + �Է����ɼ� �߰��ݾ�

			$arr_data[] = implode("\n", $only_add);

			$arr_data[] = '</option>';
		}
	}


	//�����å ��������
	$arr_data[] = '<shippingPolicy>';

	$arr_data[] = '<groupId>'.$naver_delivery['group_id'].'</groupId>';//��ۺ� ���� �׷�
	$arr_data[] = '<feeType>'.$naver_delivery['feeType'].'</feeType>';//��ۺ����� FREE=����, CHARGE=����, CONDITIONAL_FREE=���Ǻι���, CHARGE_BY_QUANTITY=�������ΰ�
	$arr_data[] = '<feePayType>'.$naver_delivery['feePayType'].'</feePayType>';//��ۺ� ������� FREE=����, PREPAYED=����, CASH_ON_DELIVERY=����
	$arr_data[] = '<feePrice>'.$naver_delivery['delivery_price'].'</feePrice>';//��ۺ� �ݾ�

	//������ ��ۺ� ����
	if ($checkoutCfg['area_delivery'] != 'n' && $checkoutCfg['area_delivery'] != '') {
		$arr_data[] = '<surchargeByArea>';
		$arr_data[] = '<splitUnit>' .$checkoutCfg['area_delivery']. '</splitUnit>';//���ǿ� 2 = 2�ǿ�, 3 = 3�ǿ�
		if($checkoutCfg['area_delivery'] == '2') {
			$arr_data[] = '<area2Price>' .$checkoutCfg['area22Price']. '</area2Price>';//2�ǿ� ������ ��ۺ�
		} else if($checkoutCfg['area_delivery'] == '3') {
			$arr_data[] = '<area2Price>' .$checkoutCfg['area32Price']. '</area2Price>';//2�ǿ� ������ ��ۺ�
			$arr_data[] = '<area3Price>' .$checkoutCfg['area33Price']. '</area3Price>';//3�ǿ� ������ ��ۺ�
		} else {
			msg("������ ��ۺ� �߸����� �Ǿ� �ֽ��ϴ�.\n �����ڿ��� ������ �ּ���.");
			exit;
		}
		$arr_data[] = '</surchargeByArea>';
	}
	
	$arr_data[] = '</shippingPolicy>';

	$arr_data[] = '</product>';

}

$arr_data[] = '<interface>';
$arr_data[] = '<salesCode></salesCode>';
$arr_data[] = '<cpaInflowCode>'. $_COOKIE["CPAValidator"]. '</cpaInflowCode>';//���ļ��� ���������� ���.(��Ű���� CPAValidator ���� �Է�)
$arr_data[] = '<mileageInflowCode>'. $_COOKIE['NA_MI']. '</mileageInflowCode>';//���̹����� ����Ʈ ���������� ���(���̹� ���� ����Ʈ ���� ���̵� > ���� �Ķ���� ����)
$arr_data[] = '<naverInflowCode>'. $_COOKIE["NA_CO"]. '</naverInflowCode>';//��Ű���� NA_CO ���� �Է�
$arr_data[] = '<saClickId>'. $_COOKIE["NVADID"]. '</saClickId>';//���̹� �˻������� ���
$arr_data[] = '<merchantCustomCode1>'. $checkSno. '</merchantCustomCode1>';//���̹� �ֹ���� sno
$arr_data[] = '</interface>';

$backUrl = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$data['goodsno'];
$arr_data[] = '<backUrl><![CDATA[' . $backUrl.']]></backUrl>';

$arr_data[] = '</order>';

$post_data = iconv('euc-kr', 'utf-8', implode("\n", $arr_data));

//�ֹ� ���ȣ��
if($checkoutCfg['testYn']=='y') {
	$url = "https://test-api.pay.naver.com/o/customer/api/order/v20/register";//�׽�Ʈ
} else {
	$url = "https://api.pay.naver.com/o/customer/api/order/v20/register";//�Ǽ���
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

// �ֹ� ��� �� ���ó��
$param = explode(':', $response);

if ($param[0] == "SUCCESS") {
	// ������ ���
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

// �ֹ��� URL ������(redirect)
if($checkoutCfg['testYn']=='y') {
	$redirectUrl = "https://test-order.pay.naver.com/customer/buy".$requestParam;//�׽�Ʈ
} else {
	$redirectUrl = "https://order.pay.naver.com/customer/buy".$requestParam;//�Ǽ���
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
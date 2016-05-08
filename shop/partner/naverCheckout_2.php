<?php
/**************************************************
 * v2.1 ���̹� ���̿��� ��û�� ��ǰ���� ����
 * naverCheckout.php���� include�� ����Ѵ�.
 *************************************************/
parse_str(urldecode($_SERVER['QUERY_STRING']));

if($checkoutCfg['useYn']!='y') exit;

$NaverCheckout = &load_class('NaverCheckout','NaverCheckout');
if(!$NaverCheckout->check_use()) exit;

//�⺻�� ����
$tax_type['1'] = 'TAX';
$tax_type['0'] = 'TAX_FREE';

$shippingType['PREPAYED'] = 'PAYED';//����
$shippingType['FREE'] = 'FREE';//����
$shippingType['CASH_ON_DELIVERY'] = 'ONDELIVERY';//�ĺ�

if(empty($product)) exit;

//��ǰv2 ���뿩�� üũ
$v2 = false;
if(is_dir($_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/lib/Clib') === true) $v2 = true;

//����ǰ�� ���� ��ǰ��ȸ ����  true = ����ǰ��ȸ, false Ư����ǰ��ȸ
$total_goods_search = false;

//�ֹ���Ͻ� ��ϵ� DB[gd_navercheckout] sno
if(!$merchantCustomCode1) exit;//���̹����� v2.1 �ֹ��� �ʼ��� �����Ǵ� ���̴�.

// Todo gd_navercheckout �����Ͱ� �ʿ��Ѱ�?
$query = $db->_query_print('SELECT * FROM '.GD_NAVERCHECKOUT.' WHERE checkoutSno=[i]', $merchantCustomCode1);
$checkout = $db->fetch($query);

$query = $db->_query_print('SELECT * FROM '.GD_NAVERCHECKOUT_ITEM.' WHERE checkoutSno=[i]', $merchantCustomCode1);
$checkout_item = $db->_select($query);
if(empty($checkout_item)) exit;

//�ٷα��� ���� ��ٱ��� ȣ��
$_COOKIE['gd_isDirect'] = 1;
$cart = new Cart($_COOKIE['gd_isDirect']);	// isdirect = 1
$cart->get_uid(true);//��ٱ��� uid �ʱ�ȭ


$db_goods = $db_goods_option = $db_goods_add = array();//DB ��ǰ��������
$naver_goods_data = $naver_all_option_item = $naver_option_item = $naver_add_option = array();//���̹� ���ۿ� ������

$add_cart = true;

foreach($checkout_item as $item) {
	$cart_add_option = $cart_text_option = array();//īƮ��Ͻ� ���
	$sales = false;//��ǰ�Ǹ� ���ɿ��� ( false = �ǸŰ���, true = �ǸźҰ� )

	if(empty($db_goods[$item['goodsno']])) {
		$db_goods[$item['goodsno']] = $db->fetch($db->_query_print("select * from ".GD_GOODS." WHERE goodsno=[s]", $item['goodsno']), true);//��ǰ����

		if($v2 === true) {
			$tmp_goods_option = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno=[s] AND go_is_deleted=[i] ORDER BY link desc", $item['goodsno'], 1));//���ÿɼ�
			$tmp_goods_add = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_ADD." WHERE goodsno=[s] ORDER BY type desc, step asc", $item['goodsno'], 1));//�Է¿ɼ�, �߰���ǰ

			foreach($tmp_goods_add as $tga) {
				$db_goods_add[$item['goodsno']][$tga['type']][$tga['sno']] = $tga;
			}

		} else {
			$tmp_goods_option = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno=[s] ORDER BY link desc", $item['goodsno'], 1));//���ÿɼ�
			$tmp_goods_add = $db->_select($db->_query_print("SELECT * FROM ".GD_GOODS_ADD." WHERE goodsno=[s] ORDER BY step asc", $item['goodsno'], 1));//�Է¿ɼ�, �߰���ǰ

			//v2�������� ������ �߰��ɼǸ� ������
			foreach($tmp_goods_add as $tga) {
				$db_goods_add[$item['goodsno']]['S'][$tga['sno']] = $tga;
			}
		}

		//��ǰ �⺻ �ǸŰ�
		$base_price[$item['goodsno']] = $tmp_goods_option[0]['price'];

		//�ɼ�����
		if($tmp_goods_option[0]['opt1'] != '') $option_bool[$item['goodsno']] = true;
		else $option_bool[$item['goodsno']] = false;

		foreach($tmp_goods_option as $tgo) {
			$db_goods_option[$item['goodsno']][$tgo['optno']] = $tgo;
		}

		//��ٱ��Ͽ��� ���� �����͸� �����Ѵ�.
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



	//�ֹ��ɼ� ����
	/*
		0	�ɼǹ�ȣ
		1	�߰��ɼǹ�ȣ
		2	����
	*/
	$arr_option_info = explode('^', $item['option_info']);

	/*��ǰ������ ����*/
	if(!$NaverCheckout->check_banWords($db_goods[$item['goodsno']]['goodsnm'])) $sales = true;
	if(!$NaverCheckout->check_exceptions($item['goodsno'])) $sales = true;

	if($v2 === false && isset($db_goods[$item['goodsno']]['option_name']) === false) {
		$db_goods[$item['goodsno']]['option_name'] = $db_goods[$item['goodsno']]['optnm'];
	}

	if(!$db_goods[$item['goodsno']]['goodsno']) {
		echo "�ùٸ� ������ �ƴմϴ�.[empty goodsno]";
		exit;
	}

	//���̹��� ���۵� ��ǰ���� ���� ����
	if(empty($create_goods[$item['goodsno']])) $create_goods[$item['goodsno']] = true;
	else $create_goods[$item['goodsno']] = false;

	//�ּ�,�ִ뱸�ż���üũ
	$max_min[$item['goodsno']] = false;
	$arr_info = explode('^', $item['option_info']);
	if(($arr_info[2] < $db_goods[$item['goodsno']]['min_ea']) || ($db_goods[$item['goodsno']]['max_ea'] != '0' && $arr_info[2] > $db_goods[$item['goodsno']]['max_ea'])) {
		if($max_min[$item['goodsno']] !== true) {
			$max_min[$item['goodsno']] = true;
			$create_goods[$item['goodsno']] = true;
		}
	}

	//��ǰ ��������
	if($db_goods[$item['goodsno']]['open'] != '1') $sales = true;

	//��ǰ ǰ������ ����
	if($db_goods[$item['goodsno']]['runout'] == '1') $sales = true;

	if($create_goods[$item['goodsno']] === true) {
		unset($naver_goods_data[$item['goodsno']]);

		if($sales === true) {
			//���̹� ���� �������� �Ǹű��� ��ǰ���� �����Ǿ� �ִ� ���
			$naver_goods_data[$item['goodsno']][] = '<status>NOT_SALE</status>';//����ǰ �ŷ�����
			$add_cart = false;//�ּ�/�ִ뱸�ż����� �ʰ��ϴ� ��� alert�� ��µǾ� ī���� ����
		} else if($max_min[$item['goodsno']] === true) {
			//�ּ�/�ִ뱸�� ������ �ʰ��ϴ� ���
			$naver_goods_data[$item['goodsno']][] = '<status>NOT_SALE</status>';//����ǰ �ŷ�����
			$add_cart = false;//�ּ�/�ִ뱸�ż����� �ʰ��ϴ� ��� alert�� ��µǾ� ī���� ����
		} else {
			if ($db_goods[$item['goodsno']]['usestock'] && $db_goods[$item['goodsno']]['totstock'] < 1) {//������ �� ��� 1���� ���� ��� ǰ��ó��
				$naver_goods_data[$item['goodsno']][] = '<stockQuantity>0</stockQuantity>';//��ǰ���(0=ǰ�� ���źҰ�, 1�̻� ���Ű���)
				$naver_goods_data[$item['goodsno']][] = '<status>SOLD_OUT</status>';//������ ����ó��
			} else {// �������� ���ϰų� ��� �ִ� ���
				if(!$db_goods[$item['goodsno']]['usestock'] && $db_goods[$item['goodsno']]['totstock'] < 1) $db_goods[$item['goodsno']]['totstock'] = '10000';//�������� ���ϰ� �Էµ� ��� 0�� ���
				$naver_goods_data[$item['goodsno']][] = '<stockQuantity>'.$db_goods[$item['goodsno']]['totstock'].'</stockQuantity>';//��ǰ���(0=ǰ�� ���źҰ�, 1�̻� ���Ű���)
				$naver_goods_data[$item['goodsno']][] = '<status>ON_SALE</status>';
			}
		}

		//��ǰ �̹��� ����
		$arr_img = explode('|', $db_goods[$item['goodsno']]['img_l']);

		//��ǰ �⺻���� ����
		$naver_goods_data[$item['goodsno']][] = '<id>' .$db_goods[$item['goodsno']]['goodsno']. '</id>';//��ǰ�ڵ�
		$naver_goods_data[$item['goodsno']][] = '<merchantProductId>' .$db_goods[$item['goodsno']]['goodscd']. '</merchantProductId>';//�Ǹ��ڻ�ǰ��ȣ
		$naver_goods_data[$item['goodsno']][] = '<ecMallProductId>' .$db_goods[$item['goodsno']]['goodsno']. '</ecMallProductId>';//���ļ��� ep�� mall_pid
		$naver_goods_data[$item['goodsno']][] = '<name><![CDATA[' .urldecode(str_replace($not_string, '', urlencode($db_goods[$item['goodsno']]['goodsnm']))). ']]></name>';//��ǰ��
		$naver_goods_data[$item['goodsno']][] = '<basePrice>' .$base_price[$item['goodsno']]. '</basePrice>';//����ǰ 1���� �ǸŰ���
		$naver_goods_data[$item['goodsno']][] = '<taxType>' .$tax_type[$db_goods[$item['goodsno']]['tax']]. '</taxType>';//�������� TAX=����, TAX_FREE=�鼼, ZERO_TAX=����
		$naver_goods_data[$item['goodsno']][] = '<infoUrl><![CDATA[' ."http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$db_goods[$item['goodsno']]['goodsno']. ']]></infoUrl>';//��ǰ�� ������ URL
		$naver_goods_data[$item['goodsno']][] = '<imageUrl><![CDATA[' .'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$arr_img[0]. ']]></imageUrl>';//��ǰ�����̹��� URL
	//	$naver_goods_data[$item['goodsno']][] = '<giftName><![CDATA[]]></giftName>';//����ǰ��
		if($checkoutCfg['return_price'] != '') {
			$naver_goods_data[$item['goodsno']][] = '<returnShippingFee><![CDATA[' .$checkoutCfg['return_price']. ']]></returnShippingFee>';//�� ��ǰ ��ۺ�
			$naver_goods_data[$item['goodsno']][] = '<exchangeShippingFee><![CDATA[' .($checkoutCfg['return_price'] * 2). ']]></exchangeShippingFee>';//��ȯ��ۺ�
		}
	}



	//���ÿɼ� ��ٱ��� ������ ����
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

	//���ÿɼ� ���̹� ���� ������ ����
	if($option_bool[$item['goodsno']] === true) {

		$option_name = explode('|', $db_goods[$item['goodsno']]['option_name']);

		//��ǰ�� ��ϵ� ���ÿɼ� ��ü ������ ����
		if(isset($naver_all_option_item[$item['goodsno']]) === false) {
			$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
			$naver_all_option_item[$item['goodsno']][] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
			$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$db_goods[$item['goodsno']]['option_name']. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��

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

			//��ǰ�� ��ϵ� �Է¿ɼ� ��ü ������ ����
			$arr_text_option_check = explode('|', $db_goods[$item['goodsno']]['addoptnm']);
			if(empty($arr_text_option_check) === false) {
				for($o = 0; $o < count($arr_text_option_check); $o++) {
					if(substr($arr_text_option_check[$o], -1, 1) == 'I') {
						$arr_text = explode('^', $arr_text_option_check[$o]);
						$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
						$naver_all_option_item[$item['goodsno']][] = '<type>INPUT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
						$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$arr_text[0]. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
						$naver_all_option_item[$item['goodsno']][] = '</optionItem>';
					}
				}
			}

			//���õ� �߰��ɼǸ� ��ü�ɼ� �����Ϳ� �߰�������
			if(isset($db_goods_add[$item['goodsno']]['S'])) {
				foreach($db_goods_add[$item['goodsno']]['S'] as $add_data) {
					if(is_int(array_search($add_data['sno'], $selected_add))) {
						$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
						$naver_all_option_item[$item['goodsno']][] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
						$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$array_add_name['S'][$add_data['step']]. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��

						$naver_all_option_item[$item['goodsno']][] = '<value>';
						$naver_all_option_item[$item['goodsno']][] = '<id>A' .$add_data['sno']. '</id>';
						$naver_all_option_item[$item['goodsno']][] = '<text><![CDATA[' .$add_data['opt']. ']]></text>';
						$naver_all_option_item[$item['goodsno']][] = '</value>';
						$naver_all_option_item[$item['goodsno']][] = '</optionItem>';
					}
				}
			}
		}

		//������ �ɼǿ� ���� ������ �Է��Ѵ�
		//�Է¿ɼ� �߰��ݾ׸� ���Խ�Ų��.
		$text_option_status = 'true';
		$add_step = $text_option_price = 0;

		if(isset($db_goods_add[$item['goodsno']]['I'])) {
			foreach($db_goods_add[$item['goodsno']]['I'] as $add_data) {
				if(is_int(array_search($add_data['sno'], $selected_add))) {//�Է��� �ɼ�
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


		//����� - ������ �Ǹ��� ��� 99999����
		if(!$db_goods[$item['goodsno']]['usestock'] && $db_goods_option[$item['goodsno']][$arr_option_info[0]]['stock'] < 1) $db_goods_option[$item['goodsno']][$arr_option_info[0]]['stock'] = '99999';

		$naver_option_item[$item['goodsno']][] = '<combination>';
		$naver_option_item[$item['goodsno']][] = '<manageCode>S' .$arr_option_info[0]. '</manageCode>';//�ɼ� sno

		$naver_option_item[$item['goodsno']][] = '<stockQuantity>'.$db_goods_option[$item['goodsno']][$arr_option_info[0]]['stock'].'</stockQuantity>';
		$naver_option_item[$item['goodsno']][] = '<options>';
		$naver_option_item[$item['goodsno']][] = '<id>S'.$arr_option_info[0].'</id>';
		$naver_option_item[$item['goodsno']][] = '<name><![CDATA['.$db_goods[$item['goodsno']]['option_name'].']]></name>';
		$naver_option_item[$item['goodsno']][] = '</options>';

		//�߰���ǰ ������ ���� - �߰���ǰ�� - �ݾ��� �������� �ʾ� �ɼ������� ����Ѵ�.
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


		//�ɼǺ� �߰��ݾ�(�⺻��0, ����ǰ���� -50%�̻� 10,000���̸� -5000 �̻�) �ɼǰ����ڵ� �ʼ�
		$naver_option_item[$item['goodsno']][] = '<price>'.($db_goods_option[$item['goodsno']][$arr_option_info[0]]['price'] - $base_price[$item['goodsno']] + $text_option_price + $add_option_price).'</price>';// (�⺻�ǸŰ� - �ɼ��ǸŰ�) + �Է¿ɼ� �߰��ݾ�

		if($text_option_status == 'false' || $add_option_status == 'false') $naver_option_item[$item['goodsno']][] = '<status>false</status>';

		$naver_option_item[$item['goodsno']][] = '</combination>';


	} else {
		/* ���ÿɼ��� ���� ��� �Է¿ɼ�üũ, �Է¿ɼ��� ������ ���ɼ� ��ǰ���� ���� */

		$text_option_bool = $add_option_bool = false;//�Է¿ɼ� ���� true=����, false=����
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

		//�߰���ǰ ���� ������ ���� - �߰���ǰ�� ���̳ʽ� �ݾ��� ������ �� ���� ���ÿɼ����� ������
		$tmp_add_data = array();
		if(isset($db_goods_add[$item['goodsno']]['S'])) {

			foreach($db_goods_add[$item['goodsno']]['S'] as $add_data) {
				if(is_int(array_search($add_data['sno'], $selected_add))) {
					//��ü�ɼǿ� ���� ������
					$tmp_add_data[] = '<optionItem>';
					$tmp_add_data[] = '<type>SELECT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
					$tmp_add_data[] = '<name><![CDATA[' .$array_add_name['S'][$add_data['step']]. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
					$tmp_add_data[] = '</optionItem>';

					$tmp_add_data[] = '<value>';
					$tmp_add_data[] = '<id>A' .$add_data['sno']. '</id>';
					$tmp_add_data[] = '<text><![CDATA[' .$add_data['opt']. ']]></text>';
					$tmp_add_data[] = '</value>';

					//���õ� �ɼǿ� ���� ������
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
			//��ǰ�� ��ϵ� �Է¿ɼ� ��ü ������ ����
			if($text_option_bool !== false) {
				foreach($array_add_name['I'] as $add_option_name) {
					$naver_all_option_item[$item['goodsno']][] = '<optionItem>';
					$naver_all_option_item[$item['goodsno']][] = '<type>INPUT</type>';//�ɼ����� - SELECT=������, INPUT=�Է���
					$naver_all_option_item[$item['goodsno']][] = '<name><![CDATA[' .$add_option_name. ']]></name>';//�ɼǸ� ex)����, ������ - �ִ� 20��
					$naver_all_option_item[$item['goodsno']][] = '</optionItem>';
				}
			}

			if($add_option_bool !== false) {
				$naver_all_option_item[$item['goodsno']][] = implode("\n", $tmp_add_data);
			}
		}

		if(empty($arr_temp_data) === false) {
			//�ֹ��� ������ �Է¿ɼ� �����͸� ����
			$naver_option_item[$item['goodsno']][] = '<combination>';
			$naver_option_item[$item['goodsno']][] = '<manageCode>'.$item['itemSno'].'</manageCode>';
			$naver_option_item[$item['goodsno']][] = '<stockQuantity>999</stockQuantity>';
			$naver_option_item[$item['goodsno']][] = '<status>'.$add_option_status.'</status>';
			$naver_option_item[$item['goodsno']][] = '<price>'.$add_option_price.'</price>';
			$naver_option_item[$item['goodsno']][] = implode("\n", $arr_temp_data);

			$naver_option_item[$item['goodsno']][] = '</combination>';
		}

	}


	/*�߰���ǰ ��ٱ��� ������ ����*/
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

	//��ٱ��� ��� - �ٷα���
	if($add_cart === true) $cart->addCart($item['goodsno'],$_opt,$cart_add_option,$cart_text_option,$arr_option_info[2],$_POST[goodsCoupon]);
}

/* ��ۺ� ���� ���� */
$param = array(
'mode' => '0',
'deliPoli' => 0,
'marketingType' => 'naverCheckout'
);
$tmp = getDeliveryMode($param);
//��ٱ��� ���� �ʱ�ȭ - �ٷα��� ���� ����
$cart->emptyCart();

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

//�����å ��������
$delivery_data[] = '<shippingPolicy>';

$delivery_data[] = '<groupId>'.$naver_delivery['group_id'].'</groupId>';//��ۺ� ���� �׷�
$delivery_data[] = '<feeType>'.$naver_delivery['feeType'].'</feeType>';//��ۺ����� FREE=����, CHARGE=����, CONDITIONAL_FREE=���Ǻι���, CHARGE_BY_QUANTITY=�������ΰ�
$delivery_data[] = '<feePayType>'.$naver_delivery['feePayType'].'</feePayType>';//��ۺ� ������� FREE=����, PREPAYED=����, CASH_ON_DELIVERY=����
$delivery_data[] = '<feePrice>'.$naver_delivery['delivery_price'].'</feePrice>';//��ۺ� �ݾ�

//������ ��ۺ� ����
if ($checkoutCfg['area_delivery'] != 'n' && $checkoutCfg['area_delivery'] != '') {
	$delivery_data[] = '<surchargeByArea>';
	$delivery_data[] = '<splitUnit>' .$checkoutCfg['area_delivery']. '</splitUnit>';//���ǿ� 2 = 2�ǿ�, 3 = 3�ǿ�
	if($checkoutCfg['area_delivery'] == '2') {
		$delivery_data[] = '<area2Price>' .$checkoutCfg['area22Price']. '</area2Price>';//2�ǿ� ������ ��ۺ�
	} else if($checkoutCfg['area_delivery'] == '3') {
		$delivery_data[] = '<area2Price>' .$checkoutCfg['area32Price']. '</area2Price>';//2�ǿ� ������ ��ۺ�
		$delivery_data[] = '<area3Price>' .$checkoutCfg['area33Price']. '</area3Price>';//3�ǿ� ������ ��ۺ�
	} else {
		//������ ��ۺ� �߸� �����Ǿ� �ִ� ���
		exit;
	}
	$delivery_data[] = '</surchargeByArea>';
}

$delivery_data[] = '</shippingPolicy>';


/* ���̹� ���۵����� ���� */

$view_data[] = '<?xml version="1.0" encoding="utf-8"?>';
$view_data[] = '<products>';

foreach($naver_goods_data as $goodsno => $goods_data) {
	$view_data[] = '<product>';

	//��ǰ����
	$view_data[] = implode("\n", $goods_data);

	if(isset($naver_all_option_item[$goodsno])) {
		$view_data[] = '<optionSupport>true</optionSupport>';
		$view_data[] = '<option>';

		$view_data[] = implode("\n", $naver_all_option_item[$goodsno]);	//��ü�ɼ�����
		
		if(isset($naver_option_item[$goodsno])) $view_data[] = implode("\n", $naver_option_item[$goodsno]);//���ÿɼ�����
		
		$view_data[] = '</option>';
	}

	//�߰���ǰ ������
	if(isset($naver_add_option[$goodsno])) $view_data[] = implode("\n", $naver_add_option[$goodsno]);

	//�������
	$view_data[] = implode("\n", $delivery_data);

	$view_data[] = '</product>';
}

$view_data[] = '</products>';

$post_data = iconv('euc-kr', 'utf-8', implode("\n", $view_data));

header('Content-Type: application/xml;charset=utf-8');
print_r($post_data);
exit;

?>
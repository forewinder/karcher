<?
include '../_header.php';
include '../conf/fieldset.php';

$mode = empty($_REQUEST['mode']) ? 'list' : $_REQUEST['mode'];
$kind = empty($_REQUEST['pkind']) ? '' : $_REQUEST['pkind'];
$mno = $sess['m_no'] ? $sess['m_no'] : '';

if ($sess){
	switch($mode) {
		case 'form':
			$cnt_query = "SELECT COUNT(*) FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_m_no = ".$mno;
			list ($cnt) = $db->fetch($cnt_query);
			if($cnt >= 10 && !$_REQUEST['sno']) {
				msg('등록가능한 배송지를 모두 등록하였습니다. 배송지 삭제 후 다시 시도해주세요.', -1);
			} else {
				$q_select = "SELECT * FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_sno = ".$_REQUEST['sno'];
				$edit = $db->fetch($q_select);
				$a_zipcode = explode('-', $edit['gmd_zipcode']);
				$edit['zipcode'][0] = $a_zipcode[0];
				$edit['zipcode'][1] = $a_zipcode[1];
				$a_phone = explode('-', $edit['gmd_phone']);
				$edit['phone'][0] = $a_phone[0];
				$edit['phone'][1] = $a_phone[1];
				$edit['phone'][2] = $a_phone[2];
				$a_mobile = explode('-', $edit['gmd_mobile']);
				$edit['mobile'][0] = $a_mobile[0];
				$edit['mobile'][1] = $a_mobile[1];
				$edit['mobile'][2] = $a_mobile[2];
				$tpl->assign($edit);
			}
			$tpl->define(array(
				'deliveryData' => 'order/order_delivery_form.htm'
			));
			break;
		default: // list
			$cnt_query = "SELECT COUNT(*) FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_m_no = ".$mno;
			list ($cnt) = $db->fetch($cnt_query);

			### 배송지 목록 최초 접근 시 기본 배송지로 회원 데이터 동기화
			if($cnt == 0) {
				$query = "SELECT m.name, m.zipcode, m.address, m.road_address, m.address_sub, m.phone, m.mobile, m.zonecode FROM ".GD_MEMBER." m WHERE m.m_no=".$mno;
				$member = $db->fetch($query,1);
				if(str_replace('-', '', $member['zipcode']) || str_replace('-', '', $member['phone']) || str_replace('-', '', $member['mobile'])) {
					$q_insert = "
					INSERT INTO ".GD_MEMBER_DELIVERY." SET
						gmd_m_no = '".$mno."',
						gmd_default = 'y',
						gmd_title = '".$member['name']."',
						gmd_name = '".$member['name']."',
						".(str_replace('-', '', $member['zipcode']) ? "gmd_zipcode = '".$member['zipcode']."'," : '')."
						gmd_address = '".$member['address']."',
						".($member['road_address'] ? "gmd_road_address = '".$member['road_address']."'," : '')."
						".($member['address_sub'] ? "gmd_address_sub = '".$member['address_sub']."'," : '')."
						".(str_replace('-', '', $member['phone']) ? "gmd_phone = '".$member['phone']."'," : '')."
						".(str_replace('-', '', $member['mobile']) ? "gmd_mobile = '".$member['mobile']."'," : '')."
						".($member['zonecode'] ? "gmd_zonecode = '".$member['zonecode']."'," : '')."
						gmd_regdate = NOW()
					";
					$db->query($q_insert);
				}
			}
			$q_select = "SELECT * FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_m_no = ".$mno." ORDER BY dm.gmd_default DESC, dm.gmd_sno DESC";
			$delivery = $db->query($q_select);
			while ($list=$db->fetch($delivery)){
				$loop[] = $list;
			}
			$tpl->assign('list', $loop);
			$tpl->define(array(
				'deliveryData' => 'order/order_delivery_list.htm'
			));
			break;
	}
}

$tpl->assign('mode', $mode); // 레이아웃 종류(list:목록, form:등록/수정)
$tpl->assign('pkind', $_REQUEST['pkind']); // 페이지 종류(d:기본(페이지), p:팝업)
$tpl->print_('tpl');
?>
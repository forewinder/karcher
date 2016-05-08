<?
include "../lib.php";
include '../../conf/fieldset.php';

$mode = empty($_REQUEST['mode']) ? 'list' : $_REQUEST['mode'];
$mno = empty($_REQUEST['mno']) ? $sess['m_no'] : $_REQUEST['mno'];
switch($mode) {
	case 'add':
		$cnt_query = "SELECT COUNT(*) FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_m_no = ".$mno;
		list ($cnt) = $db->fetch($cnt_query);
		if($cnt >= 10) {
			msg('등록가능한 배송지를 모두 등록하였습니다. 배송지 삭제 후 다시 시도해주세요.', -1);
		} else {

			### 등록 시 기본배송지로 선택했을 경우 기존에 있던 기본 배송지 초기화
			if($_POST['gmd_default'] == 'y') {
				$q_update = "
				UPDATE ".GD_MEMBER_DELIVERY." SET
					gmd_default = '',
					gmd_moddate = NOW()
				WHERE gmd_m_no = ".$mno;
				$db->query($q_update);
			}

			$q_insert = "
			INSERT INTO ".GD_MEMBER_DELIVERY." SET
				gmd_m_no = '".$mno."',
				gmd_default = '".$_POST['gmd_default']."',
				gmd_title = '".trim($_POST['delivery_name'])."',
				gmd_name = '".trim($_POST['delivery_receiver'])."',
				gmd_zipcode = '".$_POST['zipcode'][0]."-".$_POST['zipcode'][1]."',
				gmd_address = '".$_POST['address']."',
				gmd_road_address = '".$_POST['road_address']."',
				gmd_address_sub = '".$_POST['address_sub']."',
				gmd_phone = '".$_POST['phoneReceiver'][0]."-".$_POST['phoneReceiver'][1]."-".$_POST['phoneReceiver'][2]."',
				gmd_mobile = '".$_POST['mobileReceiver'][0]."-".$_POST['mobileReceiver'][1]."-".$_POST['mobileReceiver'][2]."',
				gmd_zonecode = '".$_POST['zonecode']."',
				gmd_regdate = NOW()
			";
			$db->query($q_insert);

			### 기본배송지일경우 회원 테이블에 동기화
			$i_sno = $db->_last_insert_id();
			$def_query = "SELECT gmd_default FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_sno = ".$i_sno;
			list ($default) = $db->fetch($def_query);
			if($default == 'y') {
				$mem_query = "
				UPDATE ".GD_MEMBER." SET
					zipcode = '".$_POST['zipcode'][0]."-".$_POST['zipcode'][1]."',
					zonecode = '".$_POST['zonecode']."',
					address = '".$_POST['address']."',
					address_sub = '".$_POST['address_sub']."',
					road_address = '".$_POST['road_address']."',
					phone = '".$_POST['phoneReceiver'][0]."-".$_POST['phoneReceiver'][1]."-".$_POST['phoneReceiver'][2]."',
					mobile = '".$_POST['mobileReceiver'][0]."-".$_POST['mobileReceiver'][1]."-".$_POST['mobileReceiver'][2]."'
				WHERE m_no = ".$mno;
				msg($mem_query);
				$db->query($mem_query);
				echo "<script>opener.location.reload();</script>"; // 부모창 새로고침으로 기존 데이터 변경
			}

			go('delivery_list.php?mode=list&mno='.$mno);
		}
		break;
	case 'edit':
		$check_query = "
			SELECT gmd_default,
			(SELECT COUNT(*) FROM ".GD_MEMBER_DELIVERY." WHERE gmd_m_no = ".$mno." AND gmd_default = 'y') cnt
			FROM ".GD_MEMBER_DELIVERY." WHERE gmd_sno = ".$_POST['sno'];
		list($default, $cnt) = $db->fetch($check_query);
		if($default == 'y' && $cnt == 1 && !$_POST['gmd_default']) {
			msg('기본 배송지는 반드시 등록하셔야 합니다.', -1);
			break;
		}

		### 수정 시 기본배송지로 선택했을 경우 기존에 있던 기본 배송지 초기화
		if($_POST['gmd_default'] == 'y') {
			$q_update = "
			UPDATE ".GD_MEMBER_DELIVERY." SET
				gmd_default = '',
				gmd_moddate = NOW()
			WHERE gmd_m_no = ".$mno;
			$db->query($q_update);
		}

		$q_update = "
		UPDATE ".GD_MEMBER_DELIVERY." SET
			gmd_default = '".$_POST['gmd_default']."',
			gmd_title = '".trim($_POST['delivery_name'])."',
			gmd_name = '".trim($_POST['delivery_receiver'])."',
			gmd_zipcode = '".$_POST['zipcode'][0]."-".$_POST['zipcode'][1]."',
			gmd_address = '".$_POST['address']."',
			gmd_road_address = '".$_POST['road_address']."',
			gmd_address_sub = '".$_POST['address_sub']."',
			gmd_phone = '".$_POST['phoneReceiver'][0]."-".$_POST['phoneReceiver'][1]."-".$_POST['phoneReceiver'][2]."',
			gmd_mobile = '".$_POST['mobileReceiver'][0]."-".$_POST['mobileReceiver'][1]."-".$_POST['mobileReceiver'][2]."',
			gmd_zonecode = '".$_POST['zonecode']."',
			gmd_moddate = NOW()
		WHERE gmd_sno = ".$_POST['sno'];
		$db->query($q_update);

		### 기본배송지일경우 회원 테이블에 동기화
		$def_query = "SELECT gmd_default FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_sno = ".$_REQUEST['sno'];
		list ($default) = $db->fetch($def_query);
		if($default == 'y') {
			$mem_query = "
			UPDATE ".GD_MEMBER." SET
				zipcode = '".$_POST['zipcode'][0]."-".$_POST['zipcode'][1]."',
				zonecode = '".$_POST['zonecode']."',
				address = '".$_POST['address']."',
				address_sub = '".$_POST['address_sub']."',
				road_address = '".$_POST['road_address']."',
				phone = '".$_POST['phoneReceiver'][0]."-".$_POST['phoneReceiver'][1]."-".$_POST['phoneReceiver'][2]."',
				mobile = '".$_POST['mobileReceiver'][0]."-".$_POST['mobileReceiver'][1]."-".$_POST['mobileReceiver'][2]."'
			WHERE m_no = ".$mno;
			msg($mem_query);
			$db->query($mem_query);
			echo "<script>opener.location.reload();</script>"; // 부모창 새로고침으로 기존 데이터 변경
		}

		go('delivery_list.php?mode=list&mno='.$mno);
		break;
	case 'delete':
		$def_query = "SELECT gmd_default FROM ".GD_MEMBER_DELIVERY." dm WHERE dm.gmd_sno = ".$_REQUEST['sno'];
		list ($default) = $db->fetch($def_query);

		### 기본배송지 하나라도 필수값 있을 경우 삭제 안됨
		if($default == 'y' && ($checked['reqField']['address'] || $checked['reqField']['phone'] || $checked['reqField']['mobile'])) {
			msg('기본 배송지는 삭제할 수 없습니다.', -1);
		} else {
			$del_query = "DELETE FROM ".GD_MEMBER_DELIVERY." WHERE gmd_sno=".$_REQUEST['sno'];
			$db->query($del_query);
			if($default == 'y') {
				$mem_query = "
				UPDATE ".GD_MEMBER." SET
					zipcode = '',
					address = '',
					road_address = '',
					address_sub = '',
					phone = '',
					mobile = '',
					zonecode = ''
				WHERE m_no = ".$mno;
				$db->query($mem_query);
			}
			go('delivery_list.php?mode=list&mno='.$mno);
		}
		break;
}
?>
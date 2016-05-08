<?
	include "../_header.popup.php";
	include '../../conf/fieldset.php';

	$mode = empty($_REQUEST['mode']) ? 'list' : $_REQUEST['mode'];
	$mno = empty($_REQUEST['mno']) ? '' : $_REQUEST['mno'];

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
	$list = $loop;
?>
<style type="text/css">
	.delivery_div { overflow:hidden; }
	.delivery_div .dtitle { width:100%; font-weight:bold; font-size: 22px; height:50px; line-height:50px; }
	.delivery_div .dtitle_popup { width:100%; font-weight:bold; font-size: 22px; height:50px; line-height:50px; padding-left:15px; border-bottom:1px solid #D8D8D8; background:#fff; color:#000; }
	.dtitle_list { float:left; } 
	.dtitle_close { float:right; padding:10px 20px 0 0; }
	.delivery_div .dcontents { padding:15px 0 13px 15px; color:#444; }
	.delivery_div .dcontents_popup { padding:15px 0 0px 15px; color:#444; }
	.delivery_div .mydtit { height:25px; line-height:25px; }
	.contents_left { float:left; }
	.btn_right { text-align:right; padding-right:10px; }
	.d_list table { width:98%; border-top:1px solid #C1C1C1; margin:0 auto; }
	.d_list table th { height:50px; border-bottom:1px solid #C1C1C1; text-align:center; background:#FAFAFA; font-size:12px; color:#444; }
	.d_list table td { height:73px; border-bottom:1px solid #C1C1C1; text-align:center; color:#444; }
	.d_list table td a { color:#444; }
	.bold { font-weight:bold; }
	.delivery_default { color:#007FC8; font-weight:bold; }
</style>
<form  name="form" method="post" action="delivery_indb.php">
<input type="hidden" name="mode" value="<?=$mode?>" />
<input type="hidden" name="mno" value="<?=$mno?>" />
<div class="delivery_div">
	<div id="deliveryList" class="delivery_list">
		<div class="dtitle_popup">
			<span class="dtitle_list">배송지 목록</span>
			<span class="dtitle_close"><a href="javascript:self.close()"><img src="../img/btn_delivery_closed.png" title="닫기" alt="닫기" /></a></span>
		</div>
		<div class="dcontents">
			<div class="contents_left">
				배송을 원하는 주소를 선택하시면 주문서에 자동 입력됩니다.<br />
				배송지는 <span class="bold">최대 10개</span>까지 등록이 가능합니다.
			</div>
			<div class="btn_right">
				<a href="javascript:deli_add();" /><img src="../img/btn_delivery_add.png" title="배송지 추가" /></a>
			</div>
		</div>
		<div class="d_list">
			<table cellpadding="0" cellspacing="0">
				<colgroup><col width="20%"><col width="*"><col width="20%"><col width="15%"></colgroup>
				<tr>
					<th>배송지명/<br />받으실분</th>
					<th>받으실곳</th>
					<th>연락처</th>
					<th>수정/삭제</th>
				</tr>
				<? if(count($list) == 0) { ?>
				<tr>
					<td colspan="4">데이터가 없습니다.</td>
				</tr>
				<? } else { ?>
					<? for($i=0 ; $i<count($list) ; $i++) { ?>
					<tr>
						<td><?=$list[$i]['gmd_title']?><br /><?=$list[$i]['gmd_name']?></td>
						<td style="text-align:left;">
							<?=$list[$i]['gmd_zonecode']?> (<?=$list[$i]['gmd_zipcode']?>)
							<?=($list[$i]['gmd_default'] == 'y' ? '<span class="delivery_default">기본배송지</span>' : '') ?><br />
							<?=($list[$i]['gmd_road_address'] ? $list[$i]['gmd_road_address'] : $list[$i]['gmd_address']) ?>
							<br /><?=$list[$i]['gmd_address_sub']?>
						</td>
						<td><?=$list[$i]['gmd_mobile']?><br /><?=$list[$i]['gmd_phone']?></td>
						<td>
							<a href="javascript:deli_edit(<?=$list[$i]['gmd_sno']?>);"><img src="../img/btn_delivery_edit.png" alt="수정" title="수정" /></a>
							<a href="javascript:deli_delete(<?=$list[$i]['gmd_sno']?>);"><img src="../img/btn_delivery_delete.png" alt="삭제" title="삭제" /></a>
						</td>
					</tr>
					<? } ?>
				<? } ?>
			</table>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
	var mode = "<?=$mode?>";
	var mno = "<?=$mno?>";

	function deli_add() {
		location.href="./delivery_form.php?mode=add&mno="+mno;
	}

	function deli_edit(sno) {
		location.href="./delivery_form.php?mode=edit&mno="+mno+"&sno="+sno;
	}

	function deli_delete(sno) {
		location.href="./delivery_indb.php?mode=delete&mno="+mno+"&sno="+sno;
	}

</script>
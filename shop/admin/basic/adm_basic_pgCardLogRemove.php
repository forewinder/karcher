<?php
$location = '기타관리 > 주문서 결제 로그 관리';
include '../_header.php';

//PG CARD NUMBER 로그 삭제 여부
$pgCardLogRemove = array();
if(!is_object($config)){
	$config = Core::loader('config');
}
$pgCardLogRemove = $config->load('pgCardLogRemove');
?>
<style type="text/css">
.pgCardLogRemove-infoLayout {
	border: solid #000 1px;
	padding: 15px 0px 15px 15px;
	margin: 10px 0px 30px 0px;
}
.pgCardLogRemove-infoLayout-title {
	font-weight: bold;
}
.pgCardLogRemove-infoLayout-content {
	margin-left: 15px;
	color: #666666;
}
.pgCardLogRemove-infoLayout-content-node {
	margin-top: 10px;
}
.pgCardLogRemove-infoLayout-content-node span{
	color: red;
	font-weight: bold;
}
</style>

<div class="title title_top">주문서 결제 로그 관리</div>

<div class="pgCardLogRemove-infoLayout">
	<div class="pgCardLogRemove-infoLayout-title">※ 개인정보보호법, 정보통신망법 준수 방법 안내</div>
	<div class="pgCardLogRemove-infoLayout-content">
		<div>개인정보의 기술적 관리적 보호조치 기준 제 6조 (개인정보의 암호화) 에 의거하여 구매자의 신용카드번호를 저장할 수 없으며, 저장하려면 암호화하여 저장해야 합니다.</div>
		<div class="pgCardLogRemove-infoLayout-content-node">
			<div>2016년 1월 28일 이 후의 결제건들에 대해서는 주문서 결제로그에 신용카드 정보를 저장하지 않도록 처리되고 있습니다.</div>
			<div>다만, <span>2016년 1월 28일 이 전의 결제건들에 대한 주문서 결제로그에는 이미 신용카드 번호가 저장 및 출력되고 있으므로, 가급적 이전 결제로그에서 신용카드 번호를 일괄 삭제해 주시기 바랍니다.</span></div>
		</div>
	</div>
</div>

<table class="admin-form-table">
<tr>
	<th style="width: 150px;">신용카드 번호 일괄삭제</th>
	<td>
		<?php if($pgCardLogRemove['pgCardLogRemove'] != 'y'){ ?>
		<div><img src="../img/btn_cardNumber_del.png" border="0" class="hand" style="vertical-align: bottom;" id="cardLogSubmitButton" /> <span class="extext">2016년 1월 28일 이전 주문서의 결제로그에서 신용카드 번호를 일괄삭제 합니다.</span></div>
		<div style="margin-top: 3px; color: red;">※ 일괄삭제 중 예기치 못한 오류로 인하여 처리가 중단된 경우 다시 시도하여 삭제를 완료해주세요.</div>
		<?php } else { ?>
		<span style="font:13px dotum; color: #627dce; font-weight: bold;">이미 결제로그에서 신용카드 번호가 삭제 완료된 상태입니다.</span>
		<?php } ?>
	</td>
</tr>
</table>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#cardLogSubmitButton").click( function(){
		if(confirm("결제로그에서 신용카드 번호 정보가 모두 삭제됩니다.\n계속하시겠습니까?")){
			jQuery(window).bind("beforeunload",function(e){
				var nav = navigator.userAgent.toLowerCase();
				var event = e || window.event;
				var message = "결제로그에서 신용카드 번호 정보를 삭제중입니다.\n새로고침을 하거나 브라우저를 닫을 경우 정상처리 되지 않을 수 있습니다.\n계속하시겠습니까?";

				if(nav.indexOf("chrome") != -1 || nav.indexOf("firefox") != -1){
					return message;

					if(nav.indexOf("safari") != -1) return false;
					eventStop(event);
				}
				else {
					if(!confirm(message)) {
						if(nav.indexOf("safari") != -1) return false;
						eventStop(event);
					}
				}
			});

			showCardLogProgressBar();

			var ajaxTransfer =  jQuery.ajax({
				method: "POST",
				url: "adm_basic_pgCardLogRemove.indb.php",
				data: { mode: 'remove'}
			});
			ajaxTransfer.done(function( resultMessage ) {
				alert(resultMessage);
				window.location.reload();
			});
			ajaxTransfer.fail(function() {
				alert("통신에러가 발생하였습니다.\n다시한번 시도하여 주세요.");
			});
			ajaxTransfer.always(function() {
				jQuery(window).unbind("keydown beforeunload");
				hiddenCardLogProgressBar();
			});
		}
	});

	function showCardLogProgressBar(){
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);

		jQuery("body").append('<div id="cardLogProgressBar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;" /></div>');
	}

	function hiddenCardLogProgressBar(){
		jQuery("#cardLogProgressBar").remove();
	}

	function eventStop(event){
		if(event.preventDefault){
			event.preventDefault();
		}
		else {
			event.returnValue = false;
		}
	}
});
</script>

<?php include '../_footer.php'; ?>
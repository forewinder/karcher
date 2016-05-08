<?
$location = "기타관리 > 개인정보처리 로그 확인 ";
include "../_header.php";
$logDataArray = array();

if ($_GET['mode'] == 'searchLog') {
	$logDate = $_GET['logDate'];

	if (!$logDate[0] || !$logDate[1]) {
		msg('날짜를 입력해주세요.',-1);
		exit;
	}

	$getLogDate1 = strtotime($logDate[0]);
	$getLogDate2 = strtotime($logDate[1]);
	$interval = round(($getLogDate2 - $getLogDate1) / (60*60*24));

	if ($interval < 0) {
		msg('조회기간을 확인해주세요.',-1);
		exit;
	} else if ($interval > 7) {
		msg('최대 일주일동안의 로그만 조회가 가능합니다.',-1);
		exit;
	}

	include_once "../../lib/pclzip/pclzip.lib.php";

	$logPath = array('../../log/','../../log/admin/logbasic/','../../log/admin/loginfo/'); // 로그저장경로
	$logContinuePath = array('.','..'); // 해당 폴더의 내용을 읽어올시 continue
	$monthArray = array_unique(array(date('Ym',$getLogDate1),date('Ym',$getLogDate2)));
	$logInOut = array('login_','logout_');
	$adminID = array();

	$query = $db->query("SELECT m_id FROM ".GD_MEMBER." WHERE level >= 80");
	while ($data = $db->fetch($query)) $adminID[] = $data['m_id'];

	$chkarray = array(
		'member/list.php' => '회원리스트',
		'member/Crm_view.php' =>'CRM',
		'member/Crm_info.php' => '회원정보수정',
		'member/indb.php' => '회원정보 처리',
		'member/orderlist.php' => '구매내역',
		'member/popup.emoney.php' => '적립금내역',
		'member/popup.coupon.php' => '쿠폰내역',
		'member/Crm_counsel.php' => '상담내역',
		'member/Crm_member_qna.php' => '1:1문의내역',
		'member/info.php' => '회원정보수정',
		'member/hack.php' => '회원탈퇴/삭제내역',
		'member/hack_register.php' => '회원탈퇴내역 상세내용',
		'member/hack_indb.php' => '회원탈퇴 처리',
		'member/batch.php' => '회원일괄관리',
		'member/mail.php' => '개별/전체 메일보내기',
		'member/iframe.email.php' => '개별/전체 메일보내기',
		'member/popup.sms.php' => 'SMS 작성',
		'member/sms.php' => 'SMS 보내기',
		'member/sms.member_list.php' => 'SMS 회원 주소록',
		'member/sms.address_list.php' => 'SMS 일반 주소록',
		'member/popup.sms.sendList.php' => 'SMS 발송결과 상세',
		'member/delivery_list.php' => '배송지 목록',
		'member/delivery_form.php' => '배송지 등록',
		'member/delivery_indb.php' => '배송지 처리',
		'dormant/adm_dormant_dormantMemberList.php' => '휴면 회원 리스트',
		'dormant/adm_dormant_indb.php' => '휴면 회원 처리',
		'dormant/adm_dormant_dormantTobeMemberList.php' => '휴면 전환 예정 회원 리스트',
		'auctionIpay/orderlist.php' => '옥션 iPay 결제 주문리스트',
		'order/list.php' => '주문 리스트',
		'order/view.php' => '주문상세내역',
		'order/indb.php' => '주문내역 처리',
		'order/dnXls.php' => '주문 엑셀파일',
		'order/_paper.php' => '주문내역 프린트',
		'order/misu_list.php' => '입금대기리스트',
		'order/cs.php' => '주문취소리스트',
		'order/regoods.php' => '반품/교환접수리스트',
		'order/repay.php' => '환불접수리스트',
		'order/member_qna.php' => '1:1 문의관리',
		'order/list.integrate.php' => '주문통합리스트',
		'order/dnXls_integrate.php' => '주문 엑셀파일',
		'order/list.step0.php' => '입금대기 리스트',
		'order/list.step1.php' => '입금확인 리스트',
		'order/list.step2.php' => '배송준비중 리스트',
		'order/list.step3.php' => '배송중 리스트',
		'order/list.step4.php' => '배송완료 리스트',
		'order/list.cancel.php' => '취소 리스트',
		'order/list.claim.php' => '반품/교환 리스트',
		'order/list.repay.php' => '환불 리스트',
		'order/self_order.php' => '주문 수기등록',
		'order/indb.self_order.php' => '주문 수기등록 처리',
		'order/favoriteAddress_list.php' => '자주 쓰는 주소록',
		'order/indb.favorite_address.php' => '자주 쓰는 주소록 처리',
		'order/popup.favorite_address.php' => '자주 쓰는 주소록 등록',
		'order/todayshop_list_i.php' => '투데이샵 주문리스트',
		'order/todayshop_list_b.php' => '투데이샵 주문리스트',
		'order/checkout.list.php' => '네이버체크아웃 전체주문조회',
		'order/checkout.placeorder.php' => '네이버체크아웃 발주처리',
		'order/checkout.shiporder.php' => '네이버체크아웃 발송처리',
		'order/checkout.cancelsale.php' => '네이버체크아웃 판매취소처리',
		'order/checkout.cancelorder.php' => '네이버체크아웃 주문취소처리',
		'order/checkout.cancelshipping.php' => '네이버체크아웃 발송취소처리',
		'order/checkout.inquiry.php' => '네이버체크아웃 문의관리',
		'order/checkout.api.PlaceOrder.php' => '네이버 체크아웃',
		'order/checkout.api.ShipOrder.php' => '네이버 체크아웃',
		'order/checkout.api.CancelSale.php' => '네이버 체크아웃',
		'order/checkout.api.CancelOrder.php' => '네이버 체크아웃',
		'order/checkout.api.CancelShipping.php' => '네이버 체크아웃',
		'order/post_list.php' => '우체국택배 송장번호발급',
		'order/indb.godopost.assign.php' => '우체국택배 처리',
		'order/post_reserve.php' => '우체국택배 예약하기',
		'order/indb.godopost.reserve.php' => '우체국택배 처리',
		'order/goodsflow.invoice.php' => '굿스플로 송장번호 발급',
		'order/indb.goodsflow.php' => '굿스플로 처리',
		'order/goodsflow.standby.php' => '굿스플로 배송대기리스트',
		'order/bankmatch.php' => '입금조회 / 실시간입금확인',
		'order/bankmatch.ajax.php' => '입금조회 / 실시간입금 처리',
		'order/ghostbanker.php' => '미확인 입금자 리스트 관리',
		'order/ax.indb.ghostbanker.php' => '미확인 입금자 처리',
		'order/cashreceipt.list.php' => '현금영수증 발급/조회',
		'order/cashreceipt.indb.php' => '현금영수증 처리',
		'order/cashreceipt.info.php' => '현금영수증 신청정보 조회',
		'order/cashreceipt.singly.php' => '현금영수증 개별발급',
		'order/cashreceipt.indb.php' => '현금영수증 발급 처리',
		'order/taxapp.php' => '일반발행요청리스트',
		'order/tax_indb.php' => '일반발행요청 처리',
		'order/taxissue.php' => '일반발행내역리스트',
		'order/tax_dnXls.php' => '일반발행내역 처리',
		'order/godotax.list.php' => '전자세금계산서 발급요청목록',
		'order/checkout.orderlist.php' => '네이버체크아웃 4.0 전체주문조회',
		'order/checkout.view.php' => '네이버체크아웃 4.0 주문상세',
		'order/checkout.payed.php' => '네이버체크아웃 4.0 입금확인리스트',
		'order/checkout.placed.php' => '네이버체크아웃 4.0 배송준비중리스트',
		'order/checkout.delivering.php' => '네이버체크아웃 4.0 배송중리스트',
		'order/checkout.delivered.php' => '네이버체크아웃 4.0 배송완료리스트',
		'order/checkout.complete.php' => '네이버체크아웃 4.0 구매확정리스트',
		'order/checkout.cancel.php' => '네이버체크아웃 4.0 취소리스트',
		'order/checkout.return.php' => '네이버체크아웃 4.0 반품리스트',
		'order/checkout.exchange.php' => '네이버체크아웃 4.0 교환리스트',
		'order/checkout.api.process.php' => '네이버체크아웃 4.0 처리',
		'order/checkout.inquiry.php' => '네이버 체크아웃 문의관리',
		'board/customer_register.php' => '1:1문의 답변쓰기',
		'board/customer_indb.php' => '문의 처리',
		'board/list_management.php' => '게시글 통합관리',
		'board/indb.php' => '게시글 처리',
		'board/list_excel_management.php' => '게시글 엑셀다운로드',
		'board/list_management_indb.php' => '게시글 처리',
		'board/goods_qna.php' => '상품문의관리',
		'board/goods_qna_excel_list.php' => '상품문의 엑셀다운로드',
		'board/goods_qna_indb.php' => '상품문의 처리',
		'board/goods_review.php' => '상품후기관리',
		'board/goods_review_indb.php' => '상품후기 처리',
		'board/goods_review_excel_list.php' => '상품후기 엑셀다운로드',
		'board/member_qna.php' => '1:1 문의관리',
		'board/member_qna_indb.php' => '1:1 문의 처리',
		'board/member_qna_excel_list.php' => '1:1 문의 엑셀다운로드',
		'board/cooperation.php' => '광고·제휴문의',
		'board/cooperation_indb.php' => '광고·제휴문의 처리',
		'event/coupon_apply.php' => '쿠폰발급/조회',
		'event/popup.member.php' => '회원검색',
		'event/attendance_list.php' => '출석체크 리스트',
		'event/attendance_detail.php' => '출석체크 출석자 현황',
		'event/attendance.indb.php' => '출석체크 처리',
		'log/index.php' => '방문분석',
		'log/popu.cart.detail.php' => '이 상품을 장바구니에 담은 고객 리스트',
		'log/mem.emoney.php' => '회원 적립금 분석',
		'log/indb.excel.mem.emoney.php' => '회원 적립금 분석 엑셀다운로드',
		'data/data_membercsv_indb.php' => '회원CSV파일 올리기',
		'data/adm_data_member_excel_download.php' => '회원DB다운로드',
		'data/data_memberxls_indb.php' => '회원DB다운로드',
		'goods/goods_qna.php' => '상품문의관리',
		'goods/goods_review.php' => '상품후기관리',
		'login_' => '로그인',
		'logout_' => '로그아웃',
	);
	$chkmode = array(
		'member/hack_indb.php?delete' => '회원삭제',
		'member/indb.php?modify' => '수정',
		'member/delivery_list.php?' => '조회',
		'member/delivery_form.php?add' => '등록폼',
		'member/delivery_form.php?edit' => '등록폼',
		'member/delivery_indb.php?add' => '추가',
		'member/delivery_indb.php?edit' => '수정',
		'member/delivery_indb.php?delete' => '삭제',
		'board/customer_register.php?memberQnaReply' => '등록폼',
		'board/customer_indb.php?memberQnaReply' => '1:1문의 답변등록',
		'board/customer_indb.php?reply_end' => '1:1문의 답변등록',
		'board/customer_indb.php?reviewReply' => '1:1문의 답변등록',
		'board/list_excel_management.php?' => '엑셀다운로드',
		'board/list_management_indb.php?reply_end' => '게시글 답변 등록',
		'board/goods_qna_excel_list.php?' => '엑셀다운로드',
		'board/goods_qna_indb.php?delete' => '문의글 삭제',
		'board/goods_review_indb.php?delete' => '상품후기 삭제',
		'board/goods_review_excel_list.php?' => '엑셀다운로드',
		'board/member_qna_indb.php?delete' => '1:1 문의 삭제',
		'board/member_qna_excel_list.php?' => '엑셀다운로드',
		'board/cooperation_indb.php?modify' => '답변',
		'board/cooperation_indb.php?delete' => '삭제',
		'order/dnXls_integrate.php?goods' => '상품별 엑셀파일 다운로드',
		'order/todayshop_list_b.php?goods' => '실물(일괄발송)',
		'order/checkout.api.PlaceOrder.php?' => '접수처리',
		'order/checkout.api.ShipOrder.php?' => '발송처리',
		'order/checkout.api.CancelSale.php?' => '판매취소처리',
		'order/checkout.api.CancelOrder.php?' => '취소처리',
		'order/checkout.api.CancelShipping.php?' => '발송취소처리',
		'order/ax.indb.ghostbanker.php?download' => '엑셀다운로드',
		'order/tax_dnXls.php?tax' => '엑셀다운로드',
		'order/dnXls.php?goods' => '상품별 엑셀파일 다운로드',
		'order/_paper.php?tax' => '세금계산서',
		'order/_delivery.php?' => '목록',
		'order/_delivery.php?list' => '목록',
		'order/_delivery.php?form' => '등록폼',
		'order/_delivery.php?add' => '추가',
		'order/_delivery.php?edit' => '수정',
		'order/_delivery.php?delete' => '삭제',
		'log/indb.excel.mem.emoney.php?' => '엑셀다운로드',
		'data/data_membercsv_indb.php?' => '회원CSV업로드',
		'order/cashreceipt.indb.php?cancel' => '현금영수증 취소',
		'data/data_memberxls_indb.php?' => '회원DB다운로드',
		'emoney_add' => '적립금지급/차감',
		'emoney_delete' => '적립금내역 삭제',
		'dormantRestoreAdmin' => '휴면회원 해제',
		'dormantMemberDelete' => '휴면회원 삭제',
		'dormantAdmin' => '휴면회원 전환',
		'dormantMemberToBeDelete' => '회원 삭제',
		'emoney' => '적립금 일괄지금/차감',
		'level' => '회원그룹 일괄변경',
		'status' => '회원승인상태 일괄변경',
		'sms' => 'SMS 일괄발송',
		'sendmail' => '메일발송',
		'member/popup.sms.php?' => 'SMS 작성',
		'send_sms' => 'SMS 발송',
		'group' => '조회',
		'modOrder' => '주문수정',
		'order' => '주문별 엑셀파일 다운로드',
		'report' => '주문내역서',
		'report_customer' => '주문내역서(고객용)',
		'reception' => '간이영수증',
		'particular' => '거래명세서',
		'requestSMS' => '입금요청 SMS 발송',
		'regoods' => '반품완료',
		'exc_ok' => '교환완료 후 재주문 넣기',
		'repay' => '환불완료',
		'integrate_multi_action31' => '반품완료',
		'integrate_multi_action41' => '교환완료 후 재주문 넣기',
		'writeOrder' => '주문 수기등록',
		'orderDeliveryPay' => '배송비 계산',
		'specialDC' => '할인 계산',
		'selectMember' => '회원선택',
		'memberDC' => '회원 할인 계산',
		'addGoods' => '상품 추가',
		'destroyUniqueId' => '종료',
		'faRegist' => '등록',
		'faDelete' => '삭제',
		'groupcoupon' => '쿠폰(즉시발급)',
		'coupon' => '쿠폰(일괄발급)',
		'groupgoods' => '실물(즉시발송)',
		'order_assign' => '송장번호 발급받기',
		'order_reserve' => '예약하기',
		'casebyorder' => '주문건별 송장번호 발급',
		'package' => '합포장 송장번호 발급',
		'order/indb.goodsflow.php?delivery' => '배송중 처리',
		'bankMatching' => '실시간 입금확인',
		'accountList' => '통장입금내역 실시간 조회',
		'bankUpdate' => '일괄수정',
		'approval' => '현금영수증 처리',
		'refuse' => '현금영수증 거절',
		'put' => '발급',
		'allmodify' => '일괄수정',
		'agree' => '신청승인',
		'PlaceProductOrder' => '발주확인',
		'DelayProductOrder' => '발송지연',
		'CancelSale' => '판매취소',
		'ShipProductOrder' => '발송처리',
		'RequestReturn' => '반품신청',
		'ApproveCancelApplication' => '취소요청승인',
		'ApproveReturnApplication' => '반품요청승인',
		'ApproveCollectedExchange' => '교환수거완료',
		'ApproveExchangeApplication' => '교환요청승인',
		'ReDeliveryExchange' => '교환재배송',
		'list_delete' => '게시글 삭제',
		'reply' => '게시글 답변 등록',
		'reserve' => '적립금 수동지급',
		'event/attendance.indb.php?add' => '출석체크 등록',
		'attd_delete' => '출석체크 삭제',
		'log/index.php?' => '방문자 분석',
		'referer' => '방문 경로분석',
		'client' => '방문자 환경분석',
		'iplist' => '방문자 IP확인',
		'downloadPasswordExcel' => '회원DB다운로드',
		'batch_emoney' => '적립금 일괄지금/차감',
		'batch_level' => '회원그룹 일괄변경',
		'batch_status' => '	회원승인상태 일괄변경',
		'batch_sms' => 'SMS 일괄발송',
		'casebyorderinvoice' => '주문건별 송장번호 발급',
		'packageinvoice' => '합포장 송장번호 발급',
		'qnaReply' => '문의답변',
		'login_' => '로그인',
		'logout_' => '로그아웃',
	);

	foreach ($logPath as $key => $path) {
		if ($key === 0) {
			foreach ($monthArray as $month) foreach ($logInOut as $logHead) {
				$fullPath = $path.$logHead.$month.'.log';
				if (file_exists($fullPath)) {
					$fp = @fopen($fullPath,'r');
					$fr = @fread($fp,filesize($fullPath));
					@fclose($fp);

					$logLine = array_filter(explode(PHP_EOL,$fr));
					foreach ($logLine as $k => $logLineData) {
						$logLineDataArray = explode('	',$logLineData);
						if (in_array(date('Ymd',strtotime($logLineDataArray[0])),range(date('Ymd',$getLogDate1),date('Ymd',$getLogDate2))) && in_array($logLineDataArray[2],$adminID)) {
							$REQUEST_TIME = $REQUEST_TIME_KEY = strtotime($logLineDataArray[0]);
							if ($logDataArray[$REQUEST_TIME_KEY]) $REQUEST_TIME_KEY .= '_'.$k;
							$SCRIPT_FILENAME = $logHead;
							$REMOTE_ADDR = $logLineDataArray[1];
							$adminId = $logLineDataArray[2];

							$logDataArray[$REQUEST_TIME_KEY] = array('REQUEST_TIME'=>$logLineDataArray[0], 'SCRIPT_FILENAME'=>$SCRIPT_FILENAME, 'REMOTE_ADDR'=>$REMOTE_ADDR, 'adminId'=>$adminId, 'mode'=>$SCRIPT_FILENAME);
						}
					}
				} else {/* 해당 월의 로그파일 없음 */}
			}
		} else {
			foreach ($monthArray as $month) {
				if (!is_dir($path.$month)) continue;
				$dir = @opendir($path.$month);
				while ($logFile = @readdir($dir)) {
					if (in_array($logFile,$logContinuePath)) continue;
					$fullPath = $path.$month.'/'.$logFile;
					$ext = explode('.',$logFile);

					if ((date('Ymd',$getLogDate1) <= $month.$ext[0]) && (date('Ymd',$getLogDate2) >= $month.$ext[0])) {
						if (strtolower($ext[count($ext)-1]) == 'zip') {
								$zip = new PclZip($fullPath);
								$extract = $zip->extract(PCLZIP_OPT_PATH,'/');

								if ($extract[0]['status'] == 'ok') {
									$fullPath = $extract[0]['filename'];
									$logDataArray = logSearchData($fullPath,$logDataArray,$chkarray);

									@unlink($extract[0]['filename']);
								} else {/* 로그파일 압축해제 실패*/}
							
						} else {
							$fp = fopen($fullPath);
							$fr = fread($fp,filesize($fullPath));
							fclose($fp);

							$logDataArray = logSearchData($fullPath,$logDataArray,$chkarray);
						}
					}
				}
				@fclose($dir);
			}
		}
	}
} else {
	$logDate = array(date('Ymd'),date('Ymd'));
}
$logDataArray = array_filter($logDataArray);
krsort($logDataArray);
?>
<script type="text/javascript">
function searchLog(){
	var logDate1 = document.getElementsByName('logDate[]')[0].value;
	var logDate2 = document.getElementsByName('logDate[]')[1].value;

	if (!logDate1 || !logDate2) {
		alert("날짜를 입력해주세요.");
		return false;
	} else {
		var date1 = new Date(logDate1.substr(0,4),logDate1.substr(4,2)-1,logDate1.substr(6,2));
		var date2 = new Date(logDate2.substr(0,4),logDate2.substr(4,2)-1,logDate2.substr(6,2));
		var interval = (date2 - date1) / (1000*60*60*24);

		if (interval < 0) {
			alert("조회기간을 확인해주세요.");
			return false;
		} else if (interval > 7) {
			alert("최대 일주일동안의 로그만 조회가 가능합니다.");
			return false;
		}
	}
}
</script>

<div class="title title_top">개인정보처리 로그 확인 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<div style="border:1px solid #000; padding:5px 5px 5px 15px; margin-bottom:20px;">
	<div style="font-weight:bold;">※ 개인정보의 안전성 확보에 필요한 조치에 관한 사항</div>
	<div style="padding-left:13px;">
		개인정보의 기술적 관리적 보호조치 기준에 따라 정보통신서비스 제공자등은 개인정보취급자가 개인정보처리시스템에 접속한 기록을 월 1회 이상 정기적으로 확인·감독하여야 하며, 시스템 이상 유무의 확인 등을 위해 최소 6개월 이상 접속기록을 보존·관리하여야 합니다.
	</div>
</div>

<form method="get" onsubmit="return searchLog();">
<input type="hidden" name="mode" value="searchLog"/>
<table class="tb" style="width: 100%;">
<col class=cellC><col class=cellL>
<tr>
	<th style="width: 150px;">조회 일자</th>
	<td>
		<input type="text" name="logDate[]" value="<?=$logDate[0]?>" maxlength="8" onclick="calendar(event)" onkeydown="onlynumber()" class="ac" readonly /> - <input type="text" name="logDate[]" value="<?=$logDate[1]?>" maxlength="8" onclick="calendar(event)" onkeydown="onlynumber()" class="ac" readonly /> 
		<a href="javascript:setDate('logDate[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" valign="middle"></a> <a href="javascript:setDate('logDate[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" valign="middle"></a><br />
		<span class="extext">최근 6개월 내 쇼핑몰 로그만 확인이 가능합니다.</span>
	</td>
</tr>
</table>

<div style="width: 100%; padding: 20px; text-align: center;">
	<input type="image" src="../img/btn_search2.gif" style="border:none;">
</div>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="150"><col width="200"><col width="150"><col><col width="200">
<tr><td class="rnd" colspan="16"></td></tr>
<tr class="rndbg">
	<th>접속일시</th>
	<th>IP</th>
	<th>운영자 ID</th>
	<th>접속페이지 (개인정보 관련)</th>
	<th>수행업무</th>
</tr>
<tr><td class="rnd" colspan="16"></td></tr>
</table>
<div style="width:100%; height:700px; border-left:1px solid #dcd8d6; border-bottom:1px solid #dcd8d6; overflow-y:scroll">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="150"><col width="200"><col width="150"><col><col width="200">
<?
foreach ($logDataArray as $k => $v) if ($k) {
$mode = $v['mode'].$v['func'].$v['type'].$v['ord_status'].$v['goodstype'].$v['process'];
$urlMode = $v['SCRIPT_FILENAME'].'?'.$mode;
?>
<tr height=30 align="center">
	<td><?=$v['REQUEST_TIME']?></td>
	<td><?=$v['REMOTE_ADDR']?></td>
	<td><?=$v['adminId']?></td>
	<td align="left"><?=$chkarray[$v['SCRIPT_FILENAME']]?></td>
	<td><?=$chkmode[$urlMode] ? $chkmode[$urlMode] : ($chkmode[$mode] ? $chkmode[$mode] : '조회')?></td>
</tr>
<tr><td colspan="16" class="rndline"></td></tr>
<?}?>
</table>
</div>

<?php include "../_footer.php"; ?>
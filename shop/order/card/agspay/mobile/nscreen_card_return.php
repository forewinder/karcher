<?php
/********************************************************************************
*
* AGSMobile V2.0
*
* 올더게이트 모바일 승인 페이지 (EUC-KR)
*
********************************************************************************/

include "../../../../lib/library.php";
include "../../../../conf/config.php";
include "../../../../conf/pg.agspay.php";
include "../../../../conf/config.mobileShop.php";
require_once ("./lib/AGSMobile.php");

$page_type = $_GET['page_type'];

if($page_type=='mobile') {
	$order_end_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php';
	$order_fail_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php';
}
else {
	$order_end_page = $cfg['rootDir'].'/order/order_end.php';
	$order_fail_page = $cfg['rootDir'].'/order/order_fail.php';
}

function iconvArray($data)
{
	if(is_array($data)){
		return array_map('iconvArray', $data); 
	}
	else {
		return iconv("UTF-8", "EUC-KR", $data);
	}
}
$banks = array(
	'39' => '경남은행',
	'34' => '광주은행',
	'04' => '국민은행',
	'03' => '기업은행',
	'11' => '농협',
	'31' => '대구은행',
	'32' => '부산은행',
	'02' => '산업은행',
	'45' => '새마을금고',
	'07' => '수협',
	'88' => '신한은행',
	'48' => '신협',
	'05' => '외환은행',
	'20' => '우리은행',
	'71' => '우체국',
	'37' => '전북은행',
	'35' => '제주은행',
	'81' => '하나은행',
	'27' => '한국씨티은행',
	'23' => 'SC은행',
	'09' => '동양증권',
	'78' => '신한금융투자증권',
	'40' => '삼성증권',
	'30' => '미래에셋증권',
	'43' => '한국투자증권',
	'69' => '한화증권',
);
$cards = array(
	'0100' => '비씨',
	'0202' => '수협',
	'0203' => '한미',
	'0205' => '우리',
	'0206' => '씨티',
	'0207' => '신세계한미',
	'0302' => '광주',
	'0303' => '전북',
	'0200' => 'KB국민',
	'0201' => 'NH',
	'0300' => '하나(구 외환)',
	'0700' => '해외JCB', // 온라인거래 불가
	'1000' => '해외visa', // 온라인거래 불가
	'1100' => '해외master', // 온라인거래 불가
	'0310' => '하나',
	'0400' => '삼성',
	'0901' => '해외AMEX', // 온라인거래 불가
	'0500' => '신한',
	'0301' => '제주',
	'0800' => '현대',
	'0208' => '신협체크',
	'0801' => '해외Diners', // 온라인거래 불가
	'0110' => '해외은련', // 온라인거래 불가
	'0900' => '롯데',
);

$log_path = '/../../../../../log/agspay/log';
// log파일 저장할 폴더의 경로를 지정합니다.
// 경로의 값이 null로 되어있을 경우 "현재 작업 디렉토리의 /lib/log/"에 저장됩니다.

$agsMobile = new AGSMobile($_REQUEST["StoreId"], $_REQUEST["tracking_id"], $_REQUEST["transaction"], $log_path);
$agsMobile->setLogging(true); //true : 로그기록, false : 로그기록안함.

////////////////////////////////////////////////////////
//
// getTrackingInfo() 는 최초 올더게이트 페이지를 호출할 때 전달 했던 Form 값들이 Array()로 저장되어 있습니다.
//
////////////////////////////////////////////////////////

$info = $agsMobile->getTrackingInfo(); //$info 변수는 array() 형식입니다.
if(is_array($info)){
	$info = array_map('iconvArray', $info);
}

/////////////////////////////////////////////////////////////////////////////////
//	-- tracking_info에 들어있는 컬럼 --
//
//	결제방법 : AuthTy (card,hp,virtual)
//	서브결제방법 : SubTy (카드일 경우 세팅 : isp,visa3d)
//
//	회원아이디 : UserId
//	구매자이름 : OrdNm
//	상점이름 : StoreNm
//	결제방법 : Job
//	상품명 : ProdNm
//
//	휴대폰번호 : OrdPhone
//	수신자명 : RcpNm
//	수신자연락처 : RcpPhone
//	주문자주소 : OrdAddr
//	주문번호 : OrdNo
//	배송지주소 : DlvAddr
//	상품코드 : ProdCode
//	입금예정일 : VIRTUAL_DEPODT
//	상품종류 : HP_UNITType
//	성공 URL : RtnUrl
//	상점아이디 : StoreId
//	가격 : Amt
//	이메일 : UserEmail
//	상점URL : MallUrl
//	취소 URL : CancelUrl
//	통보페이지 : MallPage
//
//	기타요구사항 : Remark
//	추가사용필드1 : Column1
//	추가사용필드1 : Column2
//	추가사용필드1 : Column3
//	CP아이디 : HP_ID
//	CP비밀번호 :  HP_PWD
//	SUB-CP아이디 : HP_SUBID
//	상품코드 :  ProdCode
//	결제정보 : DeviId ( 9000400001:일반결제, 9000400002:무이자결제)
//	카드사선택 : CardSelect
//	할부기간 :  QuotaInf
//	무이자 할부기간: NointInf
//
////////////////////////////////////////////////////////////////////////////////////////////////

// tracking_info의 정보들은 아래의 방법으로 가져오시면 됩니다
//
//	print_r($info); //tracking_info
//	echo "주문번호 : ".$info["OrdNo"]."</br>";
//	echo "상품명 : ".$info["ProdNm"]."</br>";
//	echo "결제방법 : ".$info["Job"]."</br>";
//	echo "회원아이디 : ".$info["UserId"]."</br>";
//	echo "구매자이름 : ".$info["OrdNm"]."</br>";
////////////////////////////////////////////////////////////////////////////////////////////////


// PG결제 위변조 체크 및 유효성 체크
if (forge_order_check($info['OrdNo'], $info['Amt']) === false) {
	msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.', $order_fail_page.'?ordno='.$info['OrdNo'], 'parent');
	exit();
}

// 올더게이트 결제서버로 결제 요청
$ret = $agsMobile->approve();
if($ret['message']){
	$ret['message'] = iconv("UTF-8", "EUC-KR", $ret['message']);
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// 결제결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
// 아래의 결과값들을 통하여 각 결제수단별 결제결과값을 사용하실 수 있습니다.
//
// $ret는 array() 형식으로 다음과 같은 구조를 가집니다.
//
// $ret = array (
//	   'status' => 'ok' | 'error' //승인성공일 경우 ok , 실패면 error
//		  'message' => '에러일 경우 에러메시지'
//		  'data' => 결제수단별 정보 array() //승인성공일 경우만 세팅됩니다.
//	)
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//주문번호
$ordno = $info['OrdNo'];

//로그
$tmp = '';
if ($info['AuthTy'] == 'card') {
	if ($info['SubTy'] == 'isp') {
		$tmp = '신용카드결제-안전결제(ISP)';
	} else if($info['SubTy'] == 'visa3d') {
		$tmp = '신용카드결제-안심클릭';
	} else if($info['SubTy'] == 'normal') {
		$tmp = '신용카드결제-일반결제';
	}
} else if($info['AuthTy'] == 'hp') {
	$tmp = '핸드폰결제';
} else if($info['AuthTy'] == 'virtual') {
	$tmp = '가상계좌결제';
}

$tmp_log = array();
$tmp_log[] = '결제형태 : '.$tmp;
$tmp_log[] = '상점아이디 : '.$info['StoreId'];
$tmp_log[] = '주문번호 : '.$info['OrdNo'];
$tmp_log[] = '주문자명 : '.$info['OrdNm'];
$tmp_log[] = '상품명 : '.$info['ProdNm'];
$tmp_log[] = '결제금액 : '.$info['Amt'];
$tmp_log[] = '성공여부 : '.$ret['status'];
$tmp_log[] = '결과내용 : '.$ret['message'];
switch($info['AuthTy']){
	case 'card' :
		if($ret["data"]["CardCd"] && !$cards[$ret["data"]["CardCd"]]) $card_nm = $ret["data"]["CardNm"];

		$tmp_log[] = '승인시각 : '.$ret["data"]['AdmTime'];
		$card_nm = $cards[$ret["data"]["CardCd"]];
		$tmp_log[] = '전문코드 : '.$ret["data"]["BusiCd"];
		$tmp_log[] = '승인번호 : '.$ret["data"]["AdmNo"];
		$tmp_log[] = '카드사코드 : '.$ret["data"]["CardCd"].'('.$card_nm.')';
		$tmp_log[] = '거래번호 : '.$ret["data"]["DealNo"];
		$tmp_log[] = '에스크로여부 : '.$ret["data"]["EscrowYn"];  //y이면 escrow
		$tmp_log[] = '에스크로전문번호 : '.$ret["data"]["EscrowSendNo"];
		$tmp_log[] = '무이자여부: '.$ret["data"]["NoInt"];  //y이면 무이자
	break;

	case 'hp' :
		$tmp_log[] = '승인시각 : '.$ret["data"]['AdmTime'];
		$tmp_log[] = '핸드폰결제TID : '.$ret["data"]["AdmTID"];
		$tmp_log[] = '핸드폰결제핸드폰번호 : '.$ret["data"]["Phone"];
		$tmp_log[] = '핸드폰결제통신사명 : '.$ret["data"]["PhoneCompany"];
	break;

	case 'virtual' :
		$tmp_log[] = '승인시각 : '.$ret["data"]['SuccessTime'];
		$bank_nm = $banks[$ret["data"]["BankCode"]];
		$tmp_log[] = '가상계좌번호 : '.$ret["data"]["VirtualNo"];
		$tmp_log[] = '가상계좌은행코드 : ' . $bank_nm;
		$tmp_log[] = '입금기한 : '.$ret["data"]["DueDate"];
		$tmp_log[] = '에스크로여부 : '.$ret["data"]["EscrowYn"];  //y이면 escrow
		$tmp_log[] = '에스크로전문번호 : '.$ret["data"]["EscrowSendNo"];
	break;
}

$settlelog = $ordno." (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

//DB(성공&실패) 처리
$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='".$ordno."'");

// 중복결제
if ($oData['step'] > 0 || $oData['vAccount'] != '') {
	$db->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'".$settlelog."') WHERE ordno='".$ordno."'");
	go($order_end_page . '?ordno='.$ordno.'&card_nm='.$card_nm);
	exit;
}

//item check stock
if($info['AuthTy'] != 'virtual'){
	include '../../../../lib/cardCancel.class.php';
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno)){
		$cancel->cancel_db_proc($ordno);
		go($order_fail_page . '?ordno='.$ordno, 'parent');
		exit;
	}
}

// 결제 성공
if($ret['status'] == 'ok'){
	$query = "SELECT * FROM ".GD_ORDER." a LEFT JOIN ".GD_LIST_BANK." b ON a.bankAccount = b.sno WHERE a.ordno='".$ordno."'";
	$data = $db->fetch($query);

	//에스크로 여부 확인
	if ($ret["data"]["EscrowYn"] == 'y') {
		$escrowyn = 'y';
		$escrowno = $ret["data"]["EscrowSendNo"];
	} else {
		$escrowyn = 'n';
		$escrowno = '';
	}

	//결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	//현금영수증 저장
	if (strpos($ret["message"], '현금영수증발행성공') !== false) {
		$qrc1 .= "cashreceipt='pg-agspay',";
	}

	//PG정보 저장
	switch($info['AuthTy']){
		//신용카드
		case 'card':
			$qrc1 .= "
				cardtno		= '".$ret["data"]["DealNo"]."',
				pgAppNo		= '".$ret["data"]["AdmNo"]."',
				pgCardCd		= '".$ret["data"]["CardCd"]."',
				pgAppDt		= '".$ret["data"]['AdmTime']."',
			";
		break;

		//가상계좌
		case 'virtual':
			$step = 0;
			$qrc1 = $qrc2 = '';
			//가상계좌결제의 경우 입금이 완료되지 않은 입금대기상태(가상계좌 발급성공)이므로 상품을 배송하시면 안됩니다.
			$vAccount = $bank_nm.' '.$ret["data"]["VirtualNo"].' '.$ret['StoreNm'];
			$qrc1 .= "
				pgAppDt		= '".$ret["data"]['SuccessTime']."',
			";
		break;

		//휴대폰결제
		case 'hp':
			$qrc1 .= "
				cardtno		= '".$ret["data"]["AdmTID"]."',
				pgAppDt		= '".$ret["data"]['AdmTime']."',
			";
		break;
	}

	//실데이타 저장
	$db->query("
		UPDATE ".GD_ORDER." SET ".$qrc1."
			step		= '".$step."',
			step2		= '',
			escrowyn	= '".$escrowyn."',
			escrowno	= '".$escrowno."',
			vAccount	= '".$vAccount."',
			settlelog	= concat(ifnull(settlelog,''),'".$settlelog."')
		WHERE ordno='".$ordno."'"
	);
	$db->query("UPDATE ".GD_ORDER_ITEM." SET ".$qrc2." istep='".$step."' WHERE ordno='".$ordno."'");

	//주문로그 저장
	orderLog($ordno, $r_step2[$data['step2']]." > ".$r_step[$step]);

	//재고 처리
	setStock($ordno);

	//상품구입시 적립금 사용
	if ($data['m_no'] && $data['emoney']) {
		setEmoney($data['m_no'], -$data['emoney'], '상품구입시 적립금 결제 사용', $ordno);
	}

	//주문확인메일
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'], 0, getMailOrderData($ordno));
	}

	//SMS 변수 설정
	$dataSms = $data;

	if ($info['AuthTy'] != 'virtual') {
		//입금확인메일
		sendMailCase($data['email'], 1, $data);
		//입금확인SMS
		sendSmsCase('incash', $data['mobileOrder']);
	} else {
		//주문확인SMS
		sendSmsCase('order', $data['mobileOrder']);
	}

	go($order_end_page . '?ordno='.$ordno.'&card_nm='.$card_nm);

}
else {
	// 결제 실패
	$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') WHERE ordno='".$ordno."'");
	$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno='".$ordno."'");

	go($order_fail_page . '?ordno='.$ordno, 'parent');
}
?>
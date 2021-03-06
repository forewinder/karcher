<?
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

// 주문정보
$ordno = $_GET[ordno];
$query = "
select
	orddt,deliverycomp,ddt,deliverycode,nameOrder,mobileOrder,confirmdt,nameReceiver
from
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);

// 발송정보만 등록되면 데이콤이 아래 15개택배사에 수령정보를 자동으로 확인함
$compcode = array();
$compcode['대한통운']		= 'KE'; // 대한통운
$compcode['로젠택배']		= 'LG'; // 로젠택배
$compcode['아주택배']		= 'AJ'; // 아주택배
$compcode['앨로우캡']		= 'YC'; // 엘로우캡
$compcode['우체국택배']		= 'PO'; // 우체국택배
$compcode['이젠택배']		= 'EZ'; // 이젠택배
$compcode['트라넷']			= 'TN'; // 트라넷
$compcode['한진택배']		= 'HJ'; // 한진택배
$compcode['현대택배']		= 'HD'; // 현대택배
$compcode['훼미리택배']		= 'FE'; // 훼미리택배
//$compcode['']				= 'BE'; // Bell Express
//$compcode['']				= 'CJ'; // CJ GLS
$compcode['삼성택배HTH']	= 'SS'; // HTH
$compcode['KGB택배']		= 'KB'; // KGB택배
$compcode['KT로지스']		= 'KT'; // KT로지스택배

//**************************//
// 배송결과 송신 PHP
// 발송과 수령정보 중 한가지만 송신.
//**************************//

if($pg['serviceType'] == "test"){
	$service_url = "http://pgweb.dacom.net:7085/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp";	// 테스트용
}else{
	$service_url = "http://pgweb.dacom.net/pg/wmp/mertadmin/jsp/escrow/rcvdlvinfo.jsp";		// 서비스용
}

$datasize		= 1;													// 여러건 전송일대 상점셋팅
$mid			= (("test" == $pg['serviceType'])?"t":"").$pg['id'];	// 상점ID
$mertkey		= $pg['mertkey'];										// 상점키
$oid			= $ordno;												// 주문번호
$productid		= "";													// 상품ID
$orderdate		= date_form( $data['orddt'] );							// 주문일자
$dlvtype		= "03";													// 등록내용구분(03:발송, 01:수령 중 택일)

if ( "03" == $dlvtype )
{
	// 발송정보(상점에서 상품을 배송업체를 통하여 수취인에게 발송한 정보)

	$dlvdate		= date_form( $data['ddt'] );						// 발송일자
	$dlvcompcode	= $compcode[ $data['deliverycomp'] ];				// 배송회사코드
	$dlvcomp		= str_replace( " ", "||", $data['deliverycomp'] );	// 배송회사명
	$dlvno			= $data['deliverycode'];							// 운송장번호
	$dlvworker		= $data['nameOrder'];								// 배송자명
	$dlvworkertel	= $data['mobileOrder'];								// 배송자전화번호

	if ( $dlvcompcode == '' ){											// 수령정보(위 15개사외 택배사 사용시 또는 상점 직접 배송)
		$rcvdate		= date_form( $data['confirmdt'] );				// 실수령일자
		$rcvname		= $data["nameReceiver"];						// 실수령인명
		$rcvrelation	= '본인';										// 관계
	}

	$hashdata = md5($mid.$oid.$dlvdate.$dlvcompcode.$dlvno.$mertkey);	// 인증키
}
else if ( "01" == $dlvtype )
{
	// 수령정보(상품을 수취인(또는 대리인)이 실제로 수령한 정보)
	$rcvdate		= date_form( $data['confirmdt'] );					// 실수령일자
	$rcvname		= $data['nameReceiver'];							// 실수령인명
	$rcvrelation	= '본인';											// 관계

	$hashdata = md5($mid.$oid.$dlvtype.$rcvdate.$mertkey);				// 인증키
}


// 데이콤의 배송결과등록페이지를 호출하여 배송정보등록함
/*
*	아래 URL 을 호출시 파라메터의 값에 공백이 발생하면 해당 URL이 비정상적으로 호출됩니다.
*	배송사명등을 파라메터로 등록시 공백을 "||" 으로 변경하여 주시기 바랍니다.
*/
$str_url = $service_url."?mid=$mid&oid=$oid&productid=$productid&orderdate=$orderdate&dlvtype=$dlvtype&rcvdate=$rcvdate&rcvname=$rcvname&rcvrelation=$rcvrelation&dlvdate=$dlvdate&dlvcompcode=$dlvcompcode&dlvno=$dlvno&dlvworker=$dlvworker&dlvworkertel=$dlvworkertel&hashdata=$hashdata";

/*
*	curl 방식
*	php 4.3 버전 이상에서 사용가능
*/

$ch = curl_init();

curl_setopt ($ch, CURLOPT_URL, $str_url);
curl_setopt ($ch, CURLOPT_COOKIEJAR, COOKIE_FILE_PATH);
curl_setopt ($ch, CURLOPT_COOKIEFILE, COOKIE_FILE_PATH);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

$fp = curl_exec ($ch);

if(curl_errno($ch)){
	// 연결실패시 DB 처리 로직 추가
	echo '<script>alert("배송확인요청이 비정상처리되었습니다. 재시도하세요.\\n' . str_replace( array("\n","\r"), "", curl_error($ch) ) . '");</script>'; // 비정상처리 되었을때 DB 처리
}else{
	if(trim($fp)=="OK"){
		// 정상처리되었을때 DB 처리
		$db->query("update ".GD_ORDER." set escrowconfirm=1 where ordno='$ordno'");
		echo '<script>alert("배송확인요청이 정상처리되었습니다.");</script>'; // 정상처리되었을때 DB 처리
	}else{
		// 비정상처리 되었을때 DB 처리
		echo '<script>alert("배송확인요청이 비정상처리되었습니다. 재시도하세요.\\n' . str_replace( array("\n","\r"), "", curl_error($ch) ) . '");</script>'; // 비정상처리 되었을때 DB 처리
	}
}
curl_close($ch);

//**********************************
// 아래 있는 그대로 사용하십시요.
//**********************************
function get_param($name)
{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	if (!isset($HTTP_POST_VARS[$name]) || $HTTP_POST_VARS[$name] == "") {
		if (!isset($HTTP_GET_VARS[$name]) || $HTTP_GET_VARS[$name] == "") {
			return false;
		} else {
			 return $HTTP_GET_VARS[$name];
		}
	}
	return $HTTP_POST_VARS[$name];
}

### YYYYMMDDHHSS 형식 리턴
function date_form( $dt ){
	$dt = str_replace( array( "-", ":", " " ), "", $dt );
	$dt = substr( $dt, 0, -2 );
	return $dt;
}
?>
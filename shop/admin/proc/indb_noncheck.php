<?

include dirname(__FILE__)."/../../lib/library.php";
### 고도 설정 화일 체크
$file	= dirname(__FILE__)."/../../conf/godomall.cfg.php";
if (!is_file($file)) msg("고도 설정파일을 등록하세요",1);
$file	= file($file);
$godo	= decode($file[1],1);

require_once("../../lib/qfile.class.php");
include "../../conf/config.php";
$cfg = (array)$cfg;
$qfile = new qfile();

switch ($_POST[mode]){

	case "imgSize":

		$cfg = array_merge($cfg,(array)$_POST);
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		if(preg_match('/goods/',$_SERVER['HTTP_REFERER'])){
			go($_SERVER['HTTP_REFERER']);
		}else{
			echo "<script>parent.closeLayer()</script>";
		}

		exit;
		break;

	case "getPanel": # 고도몰 기획코너

		header("Content-type: text/html; charset=euc-kr");

		/* 관리자 로그인 페이지 배너 */
		if($_POST['idnm'] == 'adminlogin' && $_POST['section'] == 'bannerPanel') {
			$isCachePanel = 'no';
			$filenm = 'season4_login/panel/bannerPanel.php';
		}

		/* 관리자 로그인 페이지 게시판 */
		else if($_POST['idnm'] == 'adminlogin2' && $_POST['section'] == 'boardpanel') {
			$isCachePanel = 'no';
			$filenm = 'season4_login/panel/boardPanel.php';
		}

		// 캐시 사용
		if ($isCachePanel == 'no') {
			if ($filenm != '') $out = readurl("http://gongji.godo.co.kr/userinterface/{$filenm}");
			if (strpos($out, 'Not Found') !== false) $out = '';
		}
		else if (($out = Core::helper('Cache','admin_panel')->get($filenm, 1800)) === false) {	// 1800 = 30분

			if ($filenm != '') $out = readurl("http://gongji.godo.co.kr/userinterface/{$filenm}");
			if (strpos($out, 'Not Found') !== false) $out = '';

			$out = trim($out);

			if ($out) {
				Core::helper('Cache','admin_panel')->set($filenm, $out);
			}

		}
		echo $out;
		exit;
		break;

}

switch ($_GET[mode]){

	case "eduExtend" :
		$data['shop_key'] = $godo['sno'];
		$out = readpost("http://gongji.godo.co.kr/userinterface/season4/freeDisk_API.php", $data);
		if ($out == 'ok') {
			msg('기본용량이 증설되었습니다.');
		} else {
			msg($out);
		}
	break;

}

go($_SERVER[HTTP_REFERER]);

?>

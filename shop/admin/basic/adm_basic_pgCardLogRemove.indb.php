<?php
include '../lib.php';

set_time_limit(0);
ini_set("memory_limit", -1);

if($_POST['mode'] == 'remove'){
	try {
		$patternArray = array(
			'/신용카드번호\s:\s[0-9]+(\*)*[0-9]+\s\(일반\s가맹점은\s\*처리됨\)/', //lgdacom
			'/카드번호\s:\s[0-9]+(\*)*[0-9]+/', //페이코
			'/카드번호\s:\s[0-9]+/', //이지페이
		);

		$query = "SELECT settlelog, ordno FROM ".GD_ORDER." WHERE settlekind='c' AND settlelog!='' ";
		$res = $db->query($query);
		if(!$res){
			throw new exception("주문정보를 불러오지 못하였습니다.\n고객센터의 문의하여 주시기 바랍니다.");
		}
		while($row = $db->fetch($res, 1)){
			$reSettleLog = '';
			if(trim($row['settlelog']) != ''){
				$reSettleLog = preg_replace($patternArray, array(), $row['settlelog']);
				if(trim($reSettleLog) && $row['ordno'] && !preg_match("/카드번호/", $reSettleLog)){
					$resUpdate = $db->query("UPDATE ".GD_ORDER." SET settlelog='".$reSettleLog."' WHERE ordno='".$row['ordno']."' LIMIT 1");
					if(!$resUpdate){
						throw new exception("주문정보 업데이트를 실패하였습니다.\n고객센터의 문의하여 주시기 바랍니다.");
					}
				}
			}
		}

		//신용카드 정보 삭제 여부 저장
		if(!is_object($config)){
			$config = Core::loader('config');
		}
		$config->save('pgCardLogRemove', array('pgCardLogRemove' => 'y'));

		echo '신용카드 번호 일괄삭제가 완료되었습니다.';
	}
	catch(Exception $e){
		echo $e->getMessage();
	}
}
?>
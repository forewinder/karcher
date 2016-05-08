<?php
class AdminLog extends Log {

	/**
	 * 관리자 로그 기본 정책
	 * View 는 기본적으로 모두 로그 처리 안함
	 * Action 은 기본적으로 모두 로그 처리 함
	 *
	 * 개인정보관련 저장(6개월 보관) - 저장위치 shop/log/admin/loginfo
	 * 개인정보취급자 저장(3년 보관) - 저장위치 shop/log/admin/logadmin - (level , nowLevel 이 80 이상 경우)
	 * 기본 로그 저장(14일 보관) - 저장위치 shop/log/admin/logbasic
	 */
	// 로그 저장 기본 dir
	var $logAdminDir;
	// 로그 저장 내용
	var $dataServer;
	var $dataPost;
	var $dataGet;
	var $dataSession;
	// 로그 저장 여부
	var $logException;
	// 실행 모드
	var $transMode;
	// 실행 파일
	var $transFile;
	// 로그 파일 만료 기간
	var $expiredAdmin = 0;
	var $expiredInfo = 0;
	var $expiredBasic = 0;
	// 기본 View / Action 로그 예외 파일
	var $logViewException;
	var $logActionException;
	// 개인정보 파일
	var $logPersonal;

	function AdminLog($dataServer, $dataPost) {
		// 관리자 로그 기본 Dir
		$this->setLogAdminDir();
		// 관리자 로그 보호 Dir
		$this->setLogProtectDir($this->getLogAdminDir());
		// 관리자 로그 만료일
		$this->setExpiredTime();
		// 관리자 로그 저장 설정 파일(View/Action/Personal)
		$this->setLogViewException();
		$this->setLogActionException();
		$this->setLogPersonal();
		// 관리자 로그 내용
		$this->setServerData($dataServer);
		$this->setPostData($dataPost);
		$this->setSessionData();
		if($dataServer['QUERY_STRING']) {
			$this->setGetData($dataServer['QUERY_STRING']);
		}
		if($this->getServerData()) {
			// 관리자 로그 실행 파일(order/indb.php)
			$this->setTransFile();
		}
		if($this->getPostData() || $this->getGetData()) {
			// 관리자 로그 실행 모드
			$this->setTransMode();
		}
		// 관리자 로그 처리 여부(로그 저장할지 안할지)
		$this->setLogException();

		// 금일 로그파일이 존재하면 압축/만료삭제를 진행하지 않음
		$todayLogFile = $this->buildPath($this->getLogAdminDir(), "logbasic", date('Ym'), date('d').".log");
		if(!file_exists($todayLogFile)) {
			$this->makeZipAdminLog();
			$this->deletePathArray($this->getExpiredAdminLog());
		}
	}

	function setLogAdminDir() {
		$this->logAdminDir = $this->getAbsolutePath(dirname(__FILE__).'/../log/admin');
	}

	function setExpiredTime() {
		$nowYear = date('Y');
		$nowMon = date('m');
		$nowDay = date('d');
		$this->expiredAdmin = mktime(0, 0, 0, $nowMon, $nowDay-1, $nowYear-3);
		$this->expiredInfo = mktime(0, 0, 0, $nowMon-6, $nowDay-1, $nowYear);
		$this->expiredBasic = mktime(0, 0, 0, $nowMon, $nowDay-15, $nowYear);
	}

	function setServerData($dataServer) {
		$nowtime = $dataServer['REQUEST_TIME'] ? $dataServer['REQUEST_TIME'] : time();
		$this->dataServer = array(
			"REQUEST_TIME"=>date("Y.m.d H:i:s", $nowtime),
			"SCRIPT_FILENAME"=>$dataServer['SCRIPT_FILENAME'],
			"REMOTE_ADDR"=>$dataServer['REMOTE_ADDR'],
//			"REMOTE_PORT"=>$dataServer['REMOTE_PORT'],
//			"DOCUMENT_ROOT"=>$dataServer['DOCUMENT_ROOT'],
//			"HTTP_USER_AGENT"=>$dataServer['HTTP_USER_AGENT'],
//			"HTTP_REFERER"=>$dataServer['HTTP_REFERER'],
//			"HTTP_COOKIE"=>$dataServer['HTTP_COOKIE'],
		);
	}

	function setPostData($dataPost) {
		if($this->getTransFile() == 'order/indb.php') {
			$this->dataPost = $dataPost;
		} else {
			$this->dataPost = array(
				"mode"=>$dataPost['mode'],
				"mId"=>$dataPost['m_id'],
				"level"=>$dataPost['level'],
				"ordNo"=>$dataPost['ordno'],
				"step"=>$dataPost['step'],
				"chk"=>$dataPost['chk'],
				"step"=>$dataPost['case'],
				"sno"=>$dataPost['sno'],
				"goodsno"=>$dataPost['goodsno'],
				"category"=>$dataPost['category'],
				"hidden"=>$dataPost['hidden'],
				"hiddenMoblie"=>$dataPost['hidden_moblie'],
				"price"=>$dataPost['goods_price'],
				"ord_status"=>$dataPost['ord_status'],
				"process"=>$dataPost['process'],
			);
			if($this->dataPost['mId'] && $this->dataPost['level']) {
				$this->dataPost['nowLevel'] = $this->getMemberLevel($this->dataPost['mId']);
			}
		}
	}

	function setGetData($queryString) {
		parse_str($queryString,$dataGet);
		if($this->getTransFile() == 'order/indb.php') {
			$this->dataGet = $dataGet;
		} else {
			$this->dataGet = array(
				"mode"=>$dataGet['mode'],
				"mId"=>$dataGet['m_id'],
				"level"=>$dataGet['level'],
				"ordNo"=>$dataGet['ordno'],
				"sno"=>$dataGet['sno'],
				"goodsno"=>$dataGet['goodsno'],
				"category"=>$dataGet['category'],
				"hidden"=>$dataGet['hidden'],
				"hiddenMoblie"=>$dataGet['hidden_moblie'],
				"price"=>$dataGet['goods_price'],
				"func"=>$dataGet['func'],
				"type"=>$dataGet['type'],
				"goodstype"=>$dataGet['goodstype'],
				"OrderID"=>$dataGet['OrderID'],
				"ProductOrderIDList"=>$dataGet['ProductOrderIDList'],
			);
			if($this->dataGet['mId'] && $this->dataGet['level']) {
				$this->dataGet['nowLevel'] = $this->getMemberLevel($this->dataGet['mId']);
			}
		}
	}

	function setSessionData() {
		$this->dataSession = array(
			"adminId"=>$_SESSION['sess']['m_id'],
			"adminLevel"=>$_SESSION['sess']['level'],
		);
	}

	function setTransFile() {
		$dataServer = $this->getServerData();
		// 관리자 실행 파일의 폴더/파일명 (ex, order/indb.php)
		$fileUrl = $dataServer['SCRIPT_FILENAME'];
		$thisUrlArr = explode(DIRECTORY_SEPARATOR, $fileUrl);
		$this->transFile = $thisUrlArr[sizeof($thisUrlArr)-2].DIRECTORY_SEPARATOR.$thisUrlArr[sizeof($thisUrlArr)-1];
	}

	function setTransMode() {
		$dataPost = $this->getPostData();
		$dataGet = $this->getGetData();
		// 관리자 처리 mode
		$this->transMode = $dataPost['mode'] ? $dataPost['mode'] : $dataGet['mode'];
	}

	function setLogViewException() {
		$this->logViewException = array(
			"File" => array(
				"member/info.php",// 회원 개인 정보
				"member/Crm_view.php",// CRM 회원 개인 정보
				"order/view.php",// 주문 개인 정보
				"member/list.php",// 회원리스트
				"member/Crm_info.php",// CRM
				"member/orderlist.php",// 구매내역
				"member/popup.emoney.php",// 적립금내역
				"member/popup.coupon.php",// 쿠폰내역
				"member/Crm_counsel.php",// 상담내역
				"member/Crm_member_qna.php",// 1:1문의내역
				"member/hack_register.php",// 회원탈퇴내역 상세내용
				"member/hack.php",// 회원탈퇴/삭제내역
				"member/batch.php",// 회원일괄관리
				"member/mail.php",// 개별/전체 메일보내기
				"member/sms.php",// SMS 보내기
				"member/popup.sms.php",// SMS 작성
				"member/sms.member_list.php",// SMS 회원 주소록
				"member/sms.address_list.php",// SMS 일반 주소록
				"member/popup.sms.sendList.php",// SMS 발송결과 상세
				"log/popu.cart.detail.php",// 이 상품을 장바구니에 담은 고객 리스트
				"log/index.php",// 방문분석
				"log/mem.emoney.php",// 회원 적립금 분석
				"dormant/adm_dormant_dormantMemberList.php",// 휴면 회원 리스트
				"dormant/adm_dormant_dormantTobeMemberList.php",// 휴면 전환 예정 회원 리스트
				"order/_paper.php",// 주문내역 프린트
				"order/list.php",// 주문 리스트
				"order/misu_list.php",// 입금대기리스트
				"order/cs.php",// 주문취소리스트
				"order/regoods.php",// 반품/교환접수리스트
				"order/repay.php",// 환불접수리스트
				"order/member_qna.php",// 1:1 문의관리
				"order/list.integrate.php",// 주문통합리스트
				"order/dnXls_integrate.php",// 주문 엑셀파일
				"order/list.step0.php",// 입금대기 리스트
				"order/list.step1.php",// 입금확인 리스트
				"order/list.step2.php",// 배송준비중 리스트
				"order/list.step3.php",// 배송중 리스트
				"order/list.step4.php",// 배송완료 리스트
				"order/list.cancel.php",// 취소 리스트
				"order/list.claim.php",// 반품/교환 리스트
				"order/list.repay.php",// 환불 리스트
				"order/favoriteAddress_list.php",// 자주 쓰는 주소록
				"board/list_management_indb.php",// 게시글 처리
				"board/list_management.php",// 게시글 통합관리
				"board/goods_qna.php",// 상품문의관리
				"board/goods_review.php",// 상품후기관리
				"board/member_qna.php",// 1:1 문의관리
				"board/cooperation.php",// 광고·제휴문의
				"board/customer_register.php",// 1:1문의 답변쓰기
				"event/coupon_apply.php",// 쿠폰발급/조회
				"event/popup.member.php",// 회원검색
				"event/attendance_list.php",// 출석체크 리스트
				"event/attendance_detail.php",// 출석체크 출석자 현황
				"event/attendance.indb.php",// 출석체크 처리
				"order/todayshop_list_i.php",// 투데이샵 주문리스트
				"order/todayshop_list_b.php",// 투데이샵 주문리스트
				"order/checkout.orderlist.php",// 네이버체크아웃 4.0 전체주문조회
				"order/checkout.view.php",// 네이버체크아웃 4.0 주문상세
				"order/checkout.api.process.php",// 네이버체크아웃 4.0 처리
				"order/checkout.payed.php",// 네이버체크아웃 4.0 입금확인리스트
				"order/checkout.placed.php",// 네이버체크아웃 4.0 배송준비중리스트
				"order/checkout.delivering.php",// 네이버체크아웃 4.0 배송중리스트
				"order/checkout.delivered.php",// 네이버체크아웃 4.0 배송완료리스트
				"order/checkout.complete.php",// 네이버체크아웃 4.0 구매확정리스트
				"order/checkout.cancel.php",// 네이버체크아웃 4.0 취소리스트
				"order/checkout.return.php",// 네이버체크아웃 4.0 반품리스트
				"order/checkout.exchange.php",// 네이버체크아웃 4.0 교환리스트
				"order/checkout.list.php",// 네이버체크아웃 전체주문조회
				"order/checkout.placeorder.php",// 네이버체크아웃 발주처리
				"order/checkout.shiporder.php",// 네이버체크아웃 발송처리
				"order/checkout.cancelsale.php",// 네이버체크아웃 판매취소처리
				"order/checkout.cancelorder.php",// 네이버체크아웃 주문취소처리
				"order/checkout.cancelshipping.php",// 네이버체크아웃 발송취소처리
				"order/checkout.api.PlaceOrder.php",// 네이버 체크아웃
				"order/checkout.api.ShipOrder.php",// 네이버 체크아웃
				"order/checkout.api.CancelSale.php",// 네이버 체크아웃
				"order/checkout.api.CancelOrder.php",// 네이버 체크아웃
				"order/checkout.api.CancelShipping.php",// 네이버 체크아웃
				"order/checkout.inquiry.php",// 네이버체크아웃 문의관
				"order/cashreceipt.list.php",// 현금영수증 발급/조회
				"order/cashreceipt.info.php",// 현금영수증 신청정보 조회
				"order/taxapp.php",// 일반발행요청리스트
				"order/taxissue.php",// 일반발행내역리스트
				"order/tax_dnXls.php",// 일반발행내역 처리
				"order/godotax.list.php",// 전자세금계산서 발급요청목록
				"order/godotax.modify.indb.php",// 
				"order/godotax.send.indb.php",// 
				"order/indb.godopost.reserve.php",// 우체국택배 처리
				"order/post_list.php",// 우체국택배 송장번호발급
				"order/post_reserve.php",// 우체국택배 예약하기
				"order/goodsflow.invoice.php",// 굿스플로 송장번호 발급
				"order/popup.favorite_address.php",// 자주 쓰는 주소록 처리
				"order/ghostbanker.php",// 미확인 입금자 리스트 관리
				"order/bankmatch.php",// 입금조회 / 실시간입금확인
				"order/goodsflow.standby.php",// 굿스플로 배송대기리스트
				"auctionIpay/orderlist.php",// 옥션 iPay 결제 주문리스트
				"goods/goods_qna.php",// 상품문의관리
				"goods/goods_review.php",// 상품후기관리
				"member/delivery_list.php",//배송지 관련 팝업
				"member/delivery_form.php",//배송지 관련 팝업
				"member/delivery_indb.php",//배송지 관련 팝업
			),
		);
	}

	function setLogActionException() {
		$this->logActionException = array(
			// 기본 Action 로그저장 이나 로그제외 파일리스트
			"File" => array(
				"basic/popup.newAreaDeliveryAdd.php",// 지역별배송비 등록하는 팝업
				"design/iframe.default.php",// 디자인 기본 메인
				"codi/_ajax.php",// 디자인 기본
				"proc/adm_panel_API.php",// 관리자 메인 로드
				"basic/adm_basic_widget_service_execute.php",// 관리자 메인 로드
				"proc/remote_godopage.php",// 고도서버
			),
			// 기본 Action 로그저장 이나 로그제외 mode리스트
			"Mode" => array(
				"getPanel",// 패널
				"getCodiTree",// 패널
				"month_info",// 패널
				"get",// 패널
				"getCategory",// 패널
			),
		);
	}

	function setLogPersonal() {
		$this->logPersonal = array(
			// 개인정보로 처리되는 파일리스트
			"Information" => array(
				"member/indb.php?addGrp", // 회원 그룹추가
				"member/indb.php?modGrp", // 회원 그룹수정
				"member/indb.php?delGrp", // 회원 그룹삭제
				"member/indb.php?modify", // 회원 수정
				"member/indb.php?delete", // 회원 삭제
				"member/info.php",// 회원 개인 정보
				"member/Crm_view.php",// CRM 회원 개인 정보
				"order/view.php",// 주문 개인 정보
				"member/indb.php?emoney_add",
				"member/indb.php?emoney_delete",
				"member/hack_indb.php?delete",
			),
			// 개인정보취급자로 처리되는 파일리스트
			"Admin" => array(
				"member/indb.php?adminModify", // 관리자 등급 수정
				"member/indb.php?addGrp", // 회원 그룹추가
				"member/indb.php?modGrp", // 회원 그룹수정
				"member/indb.php?delGrp", // 회원 그룹삭제
				"member/indb.php?modify", // 회원 수정
				"member/indb.php?delete", // 회원 삭제
			),
		);
	}

	/**
	 * 로그 기록하지 않는 파일 확인
	 * 설정을 저장,수정,삭제가 아닌 불러오기 위한 file 은 로그 기록 제외
	 */
	function setLogException() {
		$this->logException = FALSE;
		$thisFile = $this->getTransFile();
		$thisMode = $this->getTransMode();

		$logView = 0;
		foreach($this->logViewException['File'] as $value) {
			if($thisFile == $value) {
				$logView++;
			}
		}

		if(ADMINLOGSTATE != 'NO' || $logView > 0) {
			$logFile = 0;
			foreach($this->logActionException['File'] as $value) {
				if($thisFile == $value) {
					$logFile++;
				}
			}
			$logMode = 0;
			foreach($this->logActionException['Mode'] as $value) {
				if($thisMode == $value) {
					$logMode++;
				}
			}
			if($logFile == 0 && $logMode == 0) {
				$this->logException = TRUE;
			}
		}
	}

	function getExpiredTime() {
		$expiredTime['logadmin'] = $this->expiredAdmin;
		$expiredTime['loginfo'] = $this->expiredInfo;
		$expiredTime['logbasic'] = $this->expiredBasic;
		return $expiredTime;
	}

	function getTransFile() {
		return $this->transFile;
	}
	function getTransMode() {
		return $this->transMode;
	}

	function getServerData() {
		return $this->dataServer;
	}

	function getPostData() {
		return $this->dataPost;
	}

	function getGetData() {
		return $this->dataGet;
	}

	function getSessionData() {
		return $this->dataSession;
	}

	function getLogException() {
		return $this->logException;
	}

	function getLogAdminDir() {
		return $this->logAdminDir;
	}

	function getLogViewException() {
		return $this->logViewException;
	}

	function getLogActionException() {
		return $this->logActionException;
	}

	function getLogPersonal() {
		return $this->logPersonal;
	}
	/**
	 * 개인정보/개인정보처리자/기본 의 확인
	 * 개인정보 및 개인정보처리자 처리는 별도 폴더에 저장하기 위함
	 */
	function getLogDirectory() {
		// 기본
		$logDirType = 'logbasic';
		$thisFile = $this->getTransFile();
		$thisMode = $this->getTransMode();
		$logPersonal = $this->getLogPersonal();
		$dataPost = $this->getPostData();
		$dataGet = $this->getGetData();

		// 선언된 개인정보 관련 파일 체크
		foreach($logPersonal['Information'] as $value) {
			$urlParse = parse_url($value);
			$url = $urlParse['path'];
			$mode = $urlParse['query'];
			if($thisFile == $url && $thisMode == $mode) {
				$logDirType = 'loginfo';
			}
		}
		// 선언된 개인정보관리자 관련 파일 체크
		foreach($logPersonal['Admin'] as $value) {
			$urlParse = parse_url($value);
			$url = $urlParse['path'];
			$mode = $urlParse['query'];
			if($thisFile == $url && $thisMode == $mode) {
				// old_level / level 이 80 이상 체크
				if($dataPost['nowLevel'] >= 80 || $dataPost['level'] >= 80) {
					$logDirType = 'logadmin';
				} else if($dataGet['nowLevel'] >= 80 || $dataGet['level'] >= 80) {
					$logDirType = 'logadmin';
				}
			}
		}
		// shop/log/admin/해당폴더/년월/
		$saveDir = $this->buildPath($this->getLogAdminDir(), $logDirType, date('Ym'));
		return $saveDir;
	}

	/**
	 * 회원 현재의 LEVEL
	 * @param string $memberId 회원아이디
	 * @return number 처리결과
	 */
	function getMemberLevel($memberId) {
		global $db;
		$query = "select level from ".GD_MEMBER." where m_id = '".$memberId."'";
		list($nowLevel) = $db->fetch($query);
		return (int)$nowLevel;
	}

	/**
	 * 로그 보관기간 만료 파일 찾기
	 *
	 * ??
	 * 현재 처리된 파일의 관련된 (기본 / 개인정보 / 개인정보취급자) 것만 처리 할 것인지 ??
	 * 아래와 같이 어떤 처리 파일이든 동시 처리 할 것인지 ??
	 */
	function getExpiredAdminLog() {
		$delDate = $this->getExpiredTime();
		$expiredPath = array();
		foreach($delDate as $logDir=>$expiredDate) {
			$expiredDirFilePath = array();
			$expiredDir = date('Ym', $expiredDate);
			$expiredDay = date('d', $expiredDate);
			$expiredDirPath = $this->buildPath($this->getLogAdminDir(), $logDir);
			$targetDirPathArr = glob($expiredDirPath.DIRECTORY_SEPARATOR."*",GLOB_ONLYDIR);
			foreach($targetDirPathArr as $value) {
				$thisValueArr = explode(DIRECTORY_SEPARATOR, $value);
				$thisValue = $thisValueArr[sizeof($thisValueArr)-1];
				if($thisValue < $expiredDir) {
					$expiredPath[] = $value;
				}
			}
			$expiredDirPath = $this->buildPath($this->getLogAdminDir(), $logDir, $expiredDir);
			$targetFilePathArr = glob($expiredDirPath.DIRECTORY_SEPARATOR."*.log.zip",GLOB_BRACE);
			foreach($targetFilePathArr as $value) {
				$thisValueArr = explode(DIRECTORY_SEPARATOR, $value);
				$thisValue = $thisValueArr[sizeof($thisValueArr)-1];

				$thisFileValueArr = explode('.', $thisValue);
				$thisFileValue = $thisFileValueArr[0];
				if($thisFileValue < $expiredDay) {
					$expiredPath[] = $value;
				}
			}
		}

		return $expiredPath;
	}

	/**
	 * 로그 파일 압축
	 * 관리자 접속 기준 (shop/admin/lib.php 실행 시)
	 * 접속 일의 하루 전 로그 파일이 존재 시 압축
	 * shop/log/admin/해당폴더/날짜.log.zip 생성됨
	 *
	 * ??
	 * 현재 처리된 파일의 관련된 (기본 / 개인정보 / 개인정보취급자) 것만 처리 할 것인지 ??
	 * 아래와 같이 어떤 처리 파일이든 동시 처리 할 것인지 ??
	 */
	function makeZipAdminLog() {
		$nowYear = date('Y');
		$nowMon = date('m');
		$nowDay = date('d');

		$yesterDay = mktime(0, 0, 0, $nowMon, $nowDay-1, $nowYear);
		$zipTargetDir = date('Ym', $yesterDay);
		$zipTargetFile = date('d',$yesterDay);

		// 관리자 로그 저장되는 dir
		$logDirArr = array("logadmin", "loginfo", "logbasic");
		foreach($logDirArr as $logDir) {
			$dir = $this->buildPath($this->getLogAdminDir(), $logDir);
			$returnZip = array();
			// 관리자 로그 저장된 날짜 dir
			$dirArr = glob($dir.DIRECTORY_SEPARATOR."*",GLOB_ONLYDIR);
			foreach($dirArr as $dirPath) {
				$thisDirPathArr = explode(DIRECTORY_SEPARATOR, $dirPath);
				$thisDirPath = $thisDirPathArr[sizeof($thisDirPathArr)-1];
				// (검색된 관리자로그 dir < 어제 관리자로그 dir) 에서
				// zip 안된 file은 모두 zip
				if($thisDirPath < $zipTargetDir) {
					$filePathArr = glob($dirPath.DIRECTORY_SEPARATOR."*.log",GLOB_BRACE);
					foreach($filePathArr as $filePath) {
						$thisFilePathArr = explode(DIRECTORY_SEPARATOR, $filePath);
						$thisFile = $thisFilePathArr[sizeof($thisFilePathArr)-1];
						if(@file_exists($filePath)) {
							$this->makeZipLogFile($dirPath, $thisFile);
							$returnZip[] = $filePath;
						} else {
							// 검색된 파일이 있는 데 file_exists 에서 없다고 할 때
						}
					}
				// (검색된 관리자로그 dir == 어제 관리자로그 dir) 에서
				// 오늘 날짜 로그 파일보다 작은 zip 안된 file은 모두 zip
				} else if($thisDirPath == $zipTargetDir) {
					$filePathArr = glob($dirPath.DIRECTORY_SEPARATOR."*.log",GLOB_BRACE);
					foreach($filePathArr as $filePath) {
						$thisFilePathArr = explode(DIRECTORY_SEPARATOR, $filePath);
						$thisFile = $thisFilePathArr[sizeof($thisFilePathArr)-1];
						$thisFileArr = explode('.', $thisFile);
						$thisFileValue = $thisFileArr[0];
						if($thisFileValue < $nowDay) {
							if(@file_exists($filePath)) {
								$this->makeZipLogFile($dirPath, $thisFile);
								$returnZip[] = $filePath;
							} else {
								// 검색된 파일이 있는 데 file_exists 에서 없다고 할 때
							}
						} else {
							// 오늘 날짜 로그 파일보다 작은 zip 안된 파일이 없을 때
						}
					}
				} else {
					// 검색된 관리자 로그 dir 이 어제 관리자 로그 보다 클 때
				}
			}
		}

		return $returnZip;
	}

	/**
	 * 로그 기록 데이터 가공
	 */
	function makeMsg() {
		$msg[] = "=============";
		$msg[] = "*SERVER=>".urldecode($this->_http_build_query($this->getServerData()));
		$msg[] = "*SESSION=>".urldecode($this->_http_build_query($this->getSessionData()));
		$msg[] = "*POST=>".urldecode($this->_http_build_query($this->getPostData()));
		$msg[] = "*GET=>".urldecode($this->_http_build_query($this->getGetData()));
		$msg[] = "";

		$msg = implode(PHP_EOL, $msg);
		return $msg;
	}

	/**
	 * http_build_query php5 이상으로
	 * php5미만 용 lib.core.php 에 http_build_query를 생성되어 있으나
	 * 빈값에 대한 처리가 되지 않아 로그에서는 빈값은 필요없어 제거하기 위한
	 * 별도 생성하여 로그에서만 사용하는 용도로 만듬
	 */
	function _http_build_query( $formdata, $numeric_prefix = null, $key = null ) {
		$res = array();
		foreach((array)$formdata as $k=>$v) {
			if($v) {
				$tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
				if ($key) {
					$tmp_key = $key.'['.$tmp_key.']';
				}
				if ( is_array($v) || is_object($v) ) {
					$res[] = $this->_http_build_query($v, null, $tmp_key);
				} else {
					$res[] = $tmp_key."=".urlencode($v);
				}
			}
		}
		return implode("&", $res);
	}

	/**
	 * 관리자 로그 작성
	 */
	function writeAdminLog() {
		if($this->getLogException()) {
			// 관리자 로그 저장 내용
			$this->setLogMsg($this->makeMsg());
			// 관리자 로그 저장 Dir
			$this->setLogDir($this->getLogDirectory());
			// 관리자 로그 저장 파일명
			$this->setLogFile(date("d").".log");
			$this->writeLog();
		}
	}
}
?>
<?php /* Template_ 2.2.7 2016/05/04 23:13:30 /www/hummingimc_godo_co_kr/shop/data/skin/standard/outline/header/main.htm 000020803 */ ?>
<header>
<nav class="navbar">
    <div class="navbar-header">
        <div id="logo" class="visible-sm visible-xs pull-left">
            <a href="/"><img src="/karcher-main_files/kaercher_logo.png" alt="K&#228;rcher Logo"></a>
        </div>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav">
            <span class="sr-only"><font><font>Toggle navigation</font></font></span> <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
        </button>
    </div>
    <div id="nav" class="collapse navbar-collapse navbar-responsive-collapse">
        <div id="top-bar">
            <div class="container dropdown">
                <section id="language" class="navbar-left">
                    <ul>
                        <li><a href="#" class="icon-worldmap country-selector"><font><font>
                            국가(한국)
                            </font></font><b class="caret"></b></a>
                        </li>
                    </ul>
                </section>
<section id="user" class="navbar-right">
    <ul>
<?php if($TPL_VAR["page_cache_enabled"]){?>
				<li class="user-status-logout" style="display: none;"><a href="<?php echo url("member/login.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_login.jpg"></a></li>
				<li class="user-status-logout" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li class="user-status-logout" style="display: none;"><a href="<?php echo url("member/join.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_join.jpg"></a></li>
				<li class="user-status-logout" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li class="user-status-login" style="display: none;"><a href="<?php echo url("member/logout.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_logout.jpg"></a></li>
				<li class="user-status-login" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li class="user-status-login" style="display: none;"><a href="<?php echo url("mypage/mypage.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?>><img src="/shop/data/skin/standard/img/main/topmenu_mypage.jpg"></a></li>
				<li class="user-status-login" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
<?php }else{?>
<?php if(!$GLOBALS["sess"]){?>
				<li><a href="<?php echo url("member/login.php")?>&">로그인</a></li>
				<li><a href="<?php echo url("member/join.php")?>&">회원가입</a></li>
<?php }else{?>
				<li><a href="<?php echo url("member/logout.php")?>&">로그아웃</a></li>
				<li><a href="<?php echo url("mypage/mypage.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?>>마이페이지</a></li>
<?php }?>
<?php }?>
				<li><a href="<?php echo url("mypage/mypage_orderlist.php")?>&">주문/배송조회</a></li>
				
                
            <li id="product-basket-nav">
				<a href="/shop/goods/goods_cart.php" class="hidden-sm hidden-xs shopping-cart" style="position: relative; display: inherit; height:38px;">
                    <span class="icon-shop">장바구니 </span><span class="badge" style="position: absolute; top: 2px; right: -25px;"><font><font><?php echo number_format($GLOBALS["sess"]["cart_count"])?></font></font></span>
                </a>
                
				<a href="/shop/goods/goods_cart.php" class="shoplink visible-sm visible-xs hidden" style="position: relative; display: inherit;">
                    <span class="icon-shop"></span><span><font><font>장바구니 </font></font></span><span class="badge" style="margin-left: 6px;"><font><font><?php echo number_format($GLOBALS["sess"]["cart_count"])?></font></font></span>
                </a>
            </li>
        <li>
            <span itemscope="" itemtype="http://schema.org/WebSite">
                <meta itemprop="url" content="https://www.kaercher.com/jp/">
                <form class="navbar-form" role="search" method="get" itemprop="potentialAction" itemscope="" itemtype="http://schema.org/SearchAction" action="/">
                    <div class="form-group" id="header-search-form">
                        <meta itemprop="target" content="https://www.kaercher.com/jp/search_result.html?query=<?php echo $TPL_VAR["query"]?>">
                        <input itemprop="query-input" type="text" class="form-control ui-autocomplete-input" placeholder="검색" name="query" required="" autocomplete="off">
                        <input type="submit" class="submit" value="">
                    </div>
                </form>
            </span>
        </li>
    </ul>
</section>
            </div>
        </div>
        <div id="main-nav" class="">
            <div class="container dropdown">
                <ul>
					<li class="hidden-sm hidden-xs"><a href="/"><img src="/karcher-main_files/kaercher_logo.png" alt="K&#228;rcher Logo"></a></li>
                    <li>
                        <a href="#" class="dropdown-toggle"><font><font>가정용 제품</font></font></a>
                        <div class="dropdown-menu container hidden-xs hidden-sm" style="display:none;">
                            <div class="dropdown-content">
                                <div class="row">
                                    <div class="pull-left tiles col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <!--FRONTEND begin-->    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1601940_std_3-53248-RAW.jpg);" href="/shop/goods/goods_list.php?category=001001001"><font><font>
스팀 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1194701_dd_1_300x200.jpg);" href="/shop/goods/goods_list.php?category=001001002"><font><font>
        로봇 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/2645166_std_1-29626-RAW.jpg);" href="/shop/goods/goods_list.php?category=001001003"><font><font>
        진공 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1766200_std_1-57274-RAW.jpg);" href="/shop/goods/goods_list.php?category=001001004"><font><font>
        고압 세척기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1629840_std_1-65823-RAW.jpg);" href="/shop/goods/goods_list.php?category=001001005"><font><font>
        유리창 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1258501_dd_6_300x200_1.jpg);" href="/shop/goods/goods_list.php?category=001001006"><font><font>
        전기 빗자루
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1512350_Std_2_300x200.jpg);" href="/shop/goods/goods_list.php?category=001001007"><font><font>
        정원용 제품
     </font></font></a>

<!--FRONTEND end-->
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><font><font>업무용 제품</font></font></a>
                        <div class="dropdown-menu container hidden-xs hidden-sm" style="display:none;">
                            <div class="dropdown-content">
                                <div class="row">
                                    <div class="pull-left tiles col-xs-12 q col-sm-12 col-md-12 col-lg-12">
                                        <!--FRONTEND begin-->    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1077410_std_1_300x200.jpg);" href="#"><font><font>
        고압 세척기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/11468200_std_1-58066-RAW.jpg);" href="#"><font><font>
        청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/15332102_std_1_300x200.jpg);" href="#"><font><font>
        바닥 세척기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1100130_std_1-63467-RAW.jpg);" href="#"><font><font>
        카펫 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1047300_std_1_300x200.jpg);" href="#"><font><font>
        스위퍼, 진공 스위퍼
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/MC_50_std_3-28928-RAW.jpg);" href="#"><font><font>
        시티 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1092104_std_3-75306-RAW.jpg);" href="#"><font><font>
        스팀 청소기
     </font></font></a>
    <a style="background-image: url(//s4.kaercher-media.com/image/pim/1574104_std_2_300x200.jpg);" href="#"><font><font>
        드라이 아이스 세척
    </font></font></a>
<!--FRONTEND end-->
                                    </div>
                                    
                                </div>
                            </div>
                         </div>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><font><font>서비스</font></font></a>
                        <div class="dropdown-menu container hidden-xs hidden-sm" style="display:none;">
                            <div class="dropdown-content">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="accentuated image-fit">
                <a href="#">
                    <img class="" alt="mg_hg_repair_11_406x203" src="/karcher-main_files/mg_hg_repair_11_406x203.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                가정용 제품
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>애프터 서비스</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>정액 수리 요금표</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>수리 Q &amp; A</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>수리 신청 방법</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>수리 대응 종료 제품 목록</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>수리 문의</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>문제 해결</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>설명서 다운로드</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>등록</font></font></a>
    </li>
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>문의</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="accentuated image-fit">
                <a href="#">
                    <img class="" alt="검사 기관" src="/karcher-main_files/service_inspection.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                업무용 제품
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>신뢰의 애프터 서비스</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>카처 케어</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>정기 점검 계약 (유지 보수 서비스)</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>수리 메뉴</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>업무용 제품 등록</font></font></a>
    </li>
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>문의</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="accentuated image-fit last-col">
                <a href="#">
                    <img class="" alt="지원 서비스" src="/karcher-main_files/service_support.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                지원
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>쇼핑</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>온라인 쇼핑</font></font></a>
    </li>
    <li class="secondarylink">
        <a href="#"><font><font>온라인 쇼핑 이용 가이드</font></font></a>
    </li>
    <li class="secondarylink">
        <a href="#"><font><font>카처 센터</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>제품 카탈로그 다운로드</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>물질 안전 보건 자료의 청구</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>개인 정보 보호 정책</font></font></a>
    </li>
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>문의</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle"><font><font>회사 정보</font></font></a>
                        <div class="dropdown-menu container hidden-xs hidden-sm" style="display:none;">
                            <div class="dropdown-content">
                                <div class="row">
                                    <div class="col-md-3 col-sm-12">
                                        <div class="accentuated image-fit">
                <a href="#">
                    <img class="" alt="Unternehmen" src="/karcher-main_files/unternehmen.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                기업 정보
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>회사 정보</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>영업 거점</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>뉴스</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>홍보지</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>전시회 예정</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>저작권 · 면책 사항</font></font></a>
    </li>
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>개인 정보 보호 정책</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="accentuated image-fit">
                <a href="#">
                    <img class="" alt="Careers_navigation_406x203" src="/karcher-main_files/careers_navigation_406x203.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                채용 정보
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>2016 년 신입 사원 채용</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>업무용 제품 영업직</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>가정용 제품 영업직</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>엔지니어 (본사 근무)</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>서비스 엔지니어 (각 거점)</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>업무용 제품 제품</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>마케팅 (본사 근무)</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>응모에서 입사 후의 흐름</font></font></a>
    </li>
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>채용 응모 양식</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="accentuated image-fit">
                <a href="#">
                    <img class="" alt="home nachhaltigkeit" src="/karcher-main_files/home_nachhaltigkeit.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                친환경
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>환경에 대한 배려</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="accentuated image-fit last-col">
                <a href="#">
                    <img class="" alt="마운트 러시 모어" src="/karcher-main_files/mount_rushmore.jpg" style="display: inline;">
                </a>
            <div class="headline">
                <a href="#"><font><font>
                사회 공헌 및 자원 봉사 활동
                </font></font></a>
            </div>
        <ul class="list-no-bullets">
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>청소 프로젝트</font></font></a>
    </li>
    <li class="primarylink arrow-left-right ">
        <a href="#"><font><font>에코 아트 프로젝트</font></font></a>
    </li>
    <li class="primarylink arrow-left-right last">
        <a href="#"><font><font>스폰서 협찬</font></font></a>
    </li>
        </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
</header>
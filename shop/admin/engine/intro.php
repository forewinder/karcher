<?

$location = "가격비교사이트 입점 > 입점 안내";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_danawa_enuri'
);
?>


<div class="title title_top">입점 안내 <span>가격비교사이트에 대한 소개 / 장점 / 입점 등을 안내해 드리는 컨텐츠 페이지입니다</span></div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>
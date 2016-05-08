<?
include "../_header.php";

$data = array();

$goods_idx = $_COOKIE['todayGoodsMobileIdx'];
$goods_arr = array_filter(@explode(',',$goods_idx));

if(!empty($goods_arr) && is_array($goods_arr)) {
	$idx = 0;
	foreach($goods_arr as $goodsno) {
		if($idx > 8) break;
		$query = "
			SELECT
				g.*,go.price
			FROM
				".GD_GOODS." g
				LEFT JOIN ".GD_GOODS_OPTION." go ON g.goodsno = go.goodsno AND go_is_deleted <> '1' AND go_is_display = '1'
			WHERE
				g.goodsno = '".$goodsno."'
		";
		$res = $db->query($query);
		$goods_row = $db->fetch($res,1);
		if ($goods_row['use_mobile_img'] === '1') {
			$goods_row['img'] = $goods_row['img_x'];
		} else if ($goods_row['use_mobile_img'] === '0') {
			$todayImg = explode('|', $goods_row[$goods_row['img_pc_x']]);
			$goods_row['img'] = $todayImg[0];
		}
		if($goods_row['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			if($GLOBALS['cfgMobileShop']['mobileShopRootDir'] == "/m2"){
				$skin_folder = "/skin_mobileV2";
			} else {
				$skin_folder = "/skin_mobile";
			}
			$goods_row['img'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
		}
		$goods_data[] = $goods_row;
		$idx ++;
	}
}

//페이코
if(is_file($shopRootDir . '/lib/payco.class.php')){
	$Payco = Core::loader('payco')->getButtonHtmlCode('CHECKOUT', true, 'goodsView');
	if($Payco) $tpl->assign('Payco', $Payco);
}

### 템플릿 출력
$tpl->assign('goods_data', $goods_data);
$tpl->print_('tpl');
?>
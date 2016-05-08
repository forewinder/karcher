<?
include "../lib.php";
include "../../conf/config.php";
include "../../lib/page.class.php";

$db_table = "
".GD_GOODS." b JOIN ".GD_SHOPTOUCH_GOODS." sg ON b.goodsno=sg.goodsno,
".GD_GOODS_OPTION." c
";

if ($_GET[goodsnm]) {
	$arr_where[] = "g.goodsnm like '%$_GET[goodsnm]%'";
}
else {
	$arr_where[] = "1=1";
}

$where = " WHERE ".implode(' AND ', $arr_where);

$goods_query = "
SELECT 
	DISTINCT
	g.goodsno,
	g.goodsnm,
	go.price,
	sg.img_shopTouch
FROM
	".GD_GOODS." g 
	JOIN ".GD_SHOPTOUCH_GOODS." sg ON g.goodsno=sg.goodsno
	JOIN ".GD_GOODS_OPTION." go ON g.goodsno=go.goodsno AND go.link
$where ";

$goods_res = $db->_select($goods_query);

?>

<link rel="styleSheet" href="../style.css">
<script>

</script>

<body onselectstart="return false" style="margin:0">

<div id=register_goods style="padding:3px">
<div class=boxTitle>- 상품리스트 <font class=small color=#F2F2F2>(등록하려면 더블클릭)</font></div>

<table id=tb0 class=tb>
<?
foreach ($goods_res as $data){
	$bgcolor = ($idx++%2) ? "#f7f7f7" : "#ffffff";
?>
<tr bgcolor="<?=$bgcolor?>" ondblclick="javascript:parent.selectGoods('<?=strip_tags($data[goodsnm])?>','<?=$data[goodsno]?>')" class=hand>
	<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_shopTouch],"40,40",'',1)?></a></td>
	<td width=100% nowrap>
	<div style="overflow:hidden;"><?=strip_tags($data[goodsnm])?></div>
	<span>가격:<b><?=number_format($data[price])?></b></span>
	<input type=hidden name=e_<?=$_GET[name]?>[] value="<?=$data[goodsno]?>">
	</td>
</tr>
<? } ?>
</table>

<div align=center class=eng><?=$pg->page[navi]?></div>
</div>
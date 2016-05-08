<? ### KGB(http://www.kgbls.co.kr/sub/trace.asp?f_slipno=)
$out = split_betweenStr($out,'<img src="/images/img_sub0401_01.png" />','</div>');
$out = str_replace('<img src="/images/img_sub0505_02.png" onclick="history.back()" style="cursor:pointer;">', '', $out[0]);
?>
<style>
.board_topline { background-color:#19b3bf; height: 2px; }
.board_middleline { background-color:#e0e0e0; height: 1px; }
.board_title { background-color:#f6f4f3; height: 1px; font-family: "µ¸¿ò", "Verdana", "Arial", "Helvetica", "sans-serif"; font-size: 9pt; color: #262626; height:32px; vertical-align:middle; font-weight: bold; }
.text01, .board_title table td { font-weight:bold; color:#000; }
table td { color: #707070; }
</style>
<?=$out?>
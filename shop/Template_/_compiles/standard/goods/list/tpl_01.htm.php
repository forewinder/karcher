<?php /* Template_ 2.2.7 2016/05/04 23:42:59 /www/hummingimc_godo_co_kr/shop/data/skin/standard/goods/list/tpl_01.htm 000002167 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>
<!-- 상품 리스트 -->
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>

<div class="col-sm-3 col-xs-6 product-item">
                    <div class="product-image image-fit">
                                                    <a itemprop="url" href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a>
                                            </div>
                    <div class="product-info" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                        <link itemprop="availability" href="http://schema.org/InStock">
                        <h6 class="product-label"><a href="<?php echo $TPL_V1["goods_view_url"]?>" itemprop="url"><span itemprop="name"><font><font><?php echo $TPL_V1["goodsnm"]?></font></font></span></a></h6>
                        <div class="product-compare">
                            
                        </div>
                        <div class="product-price2"><span itemprop="price" content="62618"><font><font><?php echo number_format($TPL_V1["price"])?> </font></font></span> <span itemprop="priceCurrency" content="JPY"><font><font>원</font></font></span><font><font> </font></font></div>

                    </div>
                    <div class="product-actions hidden-print">
                        <div class="product-shoppable" style="">
                            <a href="<?php echo $TPL_V1["goods_view_url"]?>" class="col-xs-12 btn btn-flatyellow btn-uppercase"><font><font>상세보기 </font></font></a>
                        </div>
                    </div>
                </div>                



<?php }}?>
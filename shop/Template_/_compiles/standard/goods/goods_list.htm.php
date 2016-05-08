<?php /* Template_ 2.2.7 2016/04/12 09:07:16 /www/hummingimc_godo_co_kr/shop/data/skin/standard/goods/goods_list.htm 000003340 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<main>
        
<!--FRONTEND begin-->

    <section id="ribbon">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-9">
                <section class="hidden-print" id="breadcrumbs">
    <div class="row">
        <div class="col-sm-12">
<?php echo currPosition($GLOBALS["category"])?>

        </div>
    </div>
</section><h1>
				스팀 청소기
				</h1>
                        <p><?php echo stripslashes($TPL_VAR["lstcfg"]["body"])?>

                        </p>
                            </div>
        </div>
    </div>
</section>
        <section class="container">
        <div class="row">
            <div class="col-sm-12 image-fit">
                <img class="imagemobile" alt="K&#228;rcher 고압 세척기로 집도 차도 카처하자!" src="/karcher-depth2_files/Vario_power_nozzle_car_app_11-23970-RAW.jpg" style="display: inline;">
            </div>
        </div>
    </section>
    
</nav><div class="affix-placeholder" style="height: 77px;"></div>

</section>    
    <section class="container hidden-print">
        <div class="row section-headline">
            <div class="col-sm-12 text-right">
<a href="#" class="kaercher-product-filter-sort" data-sortby="1"><font><font>이름순 정렬 </font></font></a>
                    <span class="product-sort-price"><font><font>
                    |
                     </font></font><a href="#" class="kaercher-product-filter-sort" data-sortby="2"><font><font>가격으로 정렬</font></font></a>
                    </span>
                            </div>
        </div>
    </section>
    <section class="container product-list" itemscope="" itemtype="http://schema.org/Product">
        <div class="row product-offerInfo">
            <div class="col-sm-12">
                <span itemprop="name"><font><font>온라인 쇼핑 판매 상품　　</font></font></span>
            </div>
        </div>
        <div class=" kaercher-product-filter-target">
            <div class="row">
                <?php echo $this->assign('loop',$TPL_VAR["loopM"])?>

<?php echo $this->assign('slevel',$TPL_VAR["slevel"])?>

<?php echo $this->assign('clevel',$TPL_VAR["clevel"])?>

<?php echo $this->assign('clevel_auth',$TPL_VAR["clevel_auth"])?>

<?php echo $this->assign('auth_step',$TPL_VAR["auth_step"])?>

<?php echo $this->assign('dpCfg',$GLOBALS["dpCfg"]["tpl"])?>

<?php echo $this->assign('cols',$TPL_VAR["lstcfg"]["cols"])?>

<?php echo $this->assign('size',$TPL_VAR["lstcfg"]["size"])?>

<?php echo $this->define('tpl_include_file_1',"goods/list/".$TPL_VAR["lstcfg"]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

        </div>
    </section>
</div><!--FRONTEND end-->

        <!--TYPO3SEARCH_end-->
    <div class="modal fade" id="video-modal" tabindex="-1"><div class="modal-dialog"><div class="close" data-dismiss="modal"></div><div id="video-modal-content"></div></div></div></main>
    <div class="to-top hidden-print" style="bottom: -50px; opacity: 0;"><span class="glyphicon glyphicon-upload"></span></div>

<?php $this->print_("footer",$TPL_SCP,1);?>
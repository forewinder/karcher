<?php /* Template_ 2.2.7 2016/05/04 23:17:53 /www/hummingimc_godo_co_kr/shop/data/skin/standard/goods/goods_view.htm 000025742 */  $this->include_("dataGoodsRelation");
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


    <main>
        <!--TYPO3SEARCH_begin-->
        <!--FRONTEND begin-->
<div class="product" itemscope="" itemtype="http://schema.org/Product">
    <div itemprop="brand" itemscope="" itemtype="http://schema.org/Organization">
        <meta itemprop="name" content="K&#228;rcher">
    </div>
    <div itemprop="manufacturer" itemscope="" itemtype="http://schema.org/Organization">
        <meta itemprop="name" content="Alfred K&#228;rcher Vertriebs-GmbH">
    </div>
    <meta itemprop="mpn" content="16019420">
    <meta itemprop="url" href="#">
    
    <div class="modal" id="add-to-basket-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><font><font>×</font></font></button>
                <h4 class="modal-title"><font><font>
                    다음 항목이 장바구니에 추가되었습니다</font></font></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- 바로구매 눌렀을 때 모달창 -->

<div class="modal" id="directbuy" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">제품명 : <?php echo $TPL_VAR["goodsnm"]?></h4>
        </div>
        <div class="modal-body">
			<div class="row modal-product-item">	
				<div class="col-sm-4 image-fit">
					<?php echo goodsimg($TPL_VAR["r_img"][ 0], 0)?>

				</div>
				<div class="col-sm-6">
					제품명 : <?php echo $TPL_VAR["goodsnm"]?> <br>
					배송방법 : <?php echo $TPL_VAR["delivery_method"]?> <br>
					제품코드 : <?php echo $TPL_VAR["goodscd"]?> <br>
					원산지/제조사 : <?php echo $TPL_VAR["origin"]?>/<?php echo $TPL_VAR["maker"]?>

				</div>
				<div class="col-sm-2">
					<?php echo number_format($TPL_VAR["price"])?>원

				</div>
			</div>
			<div>
				<input type="button" value="쇼핑 계속하기" data-dismiss="modal" class="btn btn-grey btn-shopping" style="border-radius: 0px">
				<a class="btn btn-yellow pull-right"  href="javascript:act('../order/order')" style="background-color: #ffed00;border-radius: 0px;color:#333">바로구매</a>
			</div>
        </div>
        <div class="modal-footer">
            <div class="modal-body-accessory" style="text-align: left;">
                액세서리
            </div>
          <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
        </div>
      </div>

    </div>
  </div>

<!-- end -->

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
</section><h1 itemprop="name"><font><font>
                    <?php echo $TPL_VAR["goodsnm"]?>

                                    </font></font></h1>
                                    <p><?php echo $TPL_VAR["shortdesc"]?></p>
                            </div>
        </div>
    </div>
</section>    
    <section class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="row product-stage">
                <div class="col-sm-7 col-md-8 col-no-padding product-stage-left">
                    <ul class="product-awards list-no-bullets">
                                                                        </ul>
                    <div class="product-image" id="product-stage">

                        <?php echo goodsimg($TPL_VAR["r_img"][ 0], 0,'id=objImg')?>

                                            </div>
                                            <div class="product-imagerail">
                            <ul>
<?php if($TPL_t_img_1){foreach($TPL_VAR["t_img"] as $TPL_V1){?>
                                <li>
<?php echo goodsimg($TPL_V1, 0,"onmouseover='chgImg(this)' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

        </li>


<?php }}?>
                   
    </ul>                        </div>
                                    </div>
				
               
<div id="goods_spec" class="col-sm-5 col-md-4 product-box-container">
					<form name=frmView method=post onsubmit="return false">
						<div class="product-box product-salesdata" style="border-bottom:none;">    

					                <div class="product-priceinfo">
제품명 : <?php echo $TPL_VAR["goodsnm"]?>

        </div>
					<div class="product-price">판매가 : 
            <span class="product-price-singleprice"><font><font>
                <?php echo number_format($TPL_VAR["price"])?>원
            </font></font></span>
                                </div>
                <div class="product-priceinfo">
배송방법 : <?php echo $TPL_VAR["delivery_method"]?>

        </div>
        <div class="product-priceinfo">
<?php if($TPL_VAR["delivery_type"]== 1){?>
	배송비 : 무료배송
<?php }elseif($TPL_VAR["delivery_type"]== 2){?>
	개별배송비 : <?php echo number_format($TPL_VAR["goods_delivery"])?>원
<?php }elseif($TPL_VAR["delivery_type"]== 3){?>
	착불배송비 : <?php echo number_format($TPL_VAR["goods_delivery"])?>원
<?php }elseif($TPL_VAR["delivery_type"]== 4){?>
	고정배송비 : <?php echo number_format($TPL_VAR["goods_delivery"])?>원
<?php }elseif($TPL_VAR["delivery_type"]== 5){?>
	수량별배송비 : <?php echo number_format($TPL_VAR["goods_delivery"])?>원 (수량에 따라 배송비가 추가됩니다.)
<?php }?>
</div>
    

<div class="product-priceinfo">
    제품코드 : <?php echo $TPL_VAR["goodscd"]?>

</div>
<div class="product-priceinfo">
	원산지/제조사 : <?php echo $TPL_VAR["origin"]?>/<?php echo $TPL_VAR["maker"]?>

</div>
	<div class="product-priceinfo">							
<?php if(!$GLOBALS["opt"]){?>

	구매수량 : 
	
<?php if(!$TPL_VAR["runout"]){?>
	<span><img src="/shop/data/skin/standard/img/common/btn_minus.gif" onClick="chg_cart_ea(frmView.ea,'dn')" style="cursor:pointer"></span>
	<input type=text name=ea size=2 value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?> class=line style="text-align:center;" step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" onblur="chg_cart_ea(frmView.ea,'set');">
	<span><img src="/shop/data/skin/standard/img/common/btn_plus.gif" onClick="chg_cart_ea(frmView.ea,'up')" style="cursor:pointer"></span>	

	
	개
	
<?php }else{?>
	품절된 상품입니다
<?php }?>
	
<?php }else{?>
	<input type=hidden name=ea step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>>
<?php }?>
							</div>

							<!-- <p style="margin-top:20px"><a class="btn btn-yellow btn-block btn-uppercase" href="javascript:act('../order/order')">
                    바로구매
                </a></p> -->
							<p style="margin-top:20px"><a class="btn btn-yellow btn-block btn-uppercase" href="javascript:act('../order/order')" data-toggle="modal" data-target="#directbuy">
                    바로구매
                </a></p>
            <p><a class="btn btn-yellow btn-block btn-uppercase" href="javascript:cartAdd(frmView,'<?php echo $TPL_VAR["cartCfg"]->redirectType?>')">
                    장바구니
                </a></p>
        </div>
						<input type=hidden name=mode value="addItem">
<input type=hidden name=goodsno value="<?php echo $TPL_VAR["goodsno"]?>">
<input type=hidden name=goodsCoupon value="<?php echo $TPL_VAR["coupon"]?>">
					</form>
        </div>
                                            
                </div>
            </div>
        </div>
</section>
            <div class="container">
    
</div>    
    
<div class="affix-placeholder" style="height: 77px;"></div>


		
		<section class="container">
<div class="row" id="detergents">
    <div class="col-sm-12">
        <h5 class="section-headline"><font><font>호환가능제품</font></font></h5>
        <div class="product-list">
            <div id="detergents_pages" class="kaercher-product-filter-target">
                <div class="pagination-page">
                    <div class="row">
<?php if((is_array($TPL_R1=dataGoodsRelation($TPL_VAR["goodsno"], 100))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
						<div class="col-sm-3 col-xs-6 product-item product-reference" style="padding-bottom:5px">
                            <a itemprop="url" href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>" target="_blank">
                                <div class="product-reference-image">
                                    <?php echo goodsimg($TPL_V1["img_s"], 200)?>

                                </div>
                                <div class="product-reference-info">
                                    <h6 itemprop="name"><font><?php echo $TPL_V1["goodsnm"]?></font></h6>
                                    
                                    <div class="product-price2"><span itemprop="price" content="3683">
										<font><?php echo number_format($TPL_V1["price"])?></font></span> 
										<span itemprop="priceCurrency"><font><font>원</font></font></span></div>
                                </div>
                            </a>
                            
                        </div>
<?php }}?>
                    </div>
                </div>
                
                </div>
            </div>
        </div>
    </div>        
</section>
	
	<section class="container">
<div class="row" id="description">
    <div class="col-sm-12">
<?php echo $TPL_VAR["longdesc"]?>

    </div>
</div>            
</section>

        <!--TYPO3SEARCH_end-->
    <div class="modal fade" id="video-modal" tabindex="-1"><div class="modal-dialog"><div class="close" data-dismiss="modal"></div><div id="video-modal-content"></div></div></div></main>
    <div class="to-top hidden-print" style="bottom: -50px; opacity: 0;"><span class="glyphicon glyphicon-upload"></span></div>

		<script>
	
	function chkGoodsForm(form) {

	if (form.min_ea)
	{
		if (parseInt(form.ea.value) < parseInt(form.min_ea.value))
		{
			alert('최소구매수량은 ' + form.min_ea.value+'개 입니다.');
			return false;
		}
	}

	if (form.max_ea)
	{
		if (parseInt(form.ea.value) > parseInt(form.max_ea.value))
		{
			alert('최대구매수량은 ' + form.max_ea.value+'개 입니다.');
			return false;
		}
	}

	try
	{
		var step = form.ea.getAttribute('step');
		if (form.ea.value % step > 0) {
			alert('구매수량은 '+ step +'개 단위만 가능합니다.');
			return false;
		}
	}
	catch (e)
	{}

	var res = chkForm(form);

	// 입력옵션 필드값 설정
	if (res)
	{
		var addopt_inputable = document.getElementsByName('addopt_inputable[]');
		for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

			if (typeof addopt_inputable[i] == 'object') {
				var v = addopt_inputable[i].value.trim();
				if (v) {
					var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
					tmp[2] = v;
					v = tmp.join('^');
				}
				else {
					v = '';
				}
				document.getElementsByName('_addopt_inputable[]')[i].value = v;
			}
		}
	}

	return res;

}
	
var nsGodo_MultiOption = function() {

	function size(e) {

		var cnt = 0;
		var type = '';

		for (var i in e) {
			cnt++;
		}

		return cnt;
	}

	return {
		_soldout : <?php if($TPL_VAR["runout"]){?>true<?php }else{?>false<?php }?>,
		data : [],
		data_size : 0,
		_optJoin : function(opt) {

			var a = [];

			for (var i=0,m=opt.length;i<m ;i++)
			{
				if (typeof opt[i] != 'undefined' && opt[i] != '')
				{
					a.push(opt[i]);
				}
			}

			return a.join(' / ');

		},
		getFieldTag : function (name, value) {
			var el = document.createElement('input');
			el.type = "hidden";
			el.name = name;
			el.value = value;

			return el;

		},
		clearField : function() {

			var form = document.getElementsByName('frmView')[0];

			var el;

			for (var i=0,m=form.elements.length;i<m ;i++) {
				el = form.elements[i];

				if (typeof el == 'undefined' || el.tagName == "FIELDSET") continue;

				if (/^multi\_.+/.test(el.name)) {
					el.parentNode.removeChild(el);
					i--;
				}

			}

		},
		addField : function(obj, idx) {

			var _tag;
			var form = document.getElementsByName('frmView')[0];

			for(var k in obj) {

				if (typeof obj[k] == 'undefined' || typeof obj[k] == 'function' || (k != 'opt' && k != 'addopt' && k != 'ea' && k != 'addopt_inputable')) continue;

				switch (k)
				{
					case 'ea':
						_tag = this.getFieldTag('multi_'+ k +'['+idx+']', obj[k]);
						form.appendChild(_tag);
						break;
					case 'addopt_inputable':
					case 'opt':
					case 'addopt':
						//hasOwnProperty
						for(var k2 in obj[k]) {
							if (typeof obj[k][k2] == 'function') continue;
							_tag = this.getFieldTag('multi_'+ k +'['+idx+'][]', obj[k][k2]);
							form.appendChild(_tag);
						}

						break;
					default :
						continue;
						break;
				}
			}
		},
		set : function() {

			var add = true;

			// 선택 옵션
			var opt = document.getElementsByName('opt[]');
			for (var i=0,m=opt.length;i<m ;i++ )
			{
				if (typeof(opt[i])!="undefined") {
					if (opt[i].value == '') add = false;
				}
			}

			// 추가 옵션?
			var addopt = document.getElementsByName('addopt[]');
			for (var i=0,m=addopt.length;i<m ;i++ )
			{
				if (typeof(addopt[i])!="undefined") {
					if (addopt[i].value == '' /*&& addopt[i].getAttribute('required') != null*/) add = false;
				}
			}

			// 입력 옵션은 이곳에서 체크 하지 않는다.
			if (add == true)
			{
				this.add();
			}
		},
		del : function(key) {

			this.data[key] = null;
			var tr = document.getElementById(key);
			tr.parentNode.removeChild(tr);
			this.data_size--;

			// 총 금액
			this.totPrice();

		},
		add : function() {

			var self = this;

			if (self._soldout)
			{
				alert("품절된 상품입니다.");
				return;
			}

			var form = document.frmView;
			if(!(form.ea.value>0))
			{
				alert("구매수량은 1개 이상만 가능합니다");
				return;
			}
			else
			{
				try
				{
					var step = form.ea.getAttribute('step');
					if (form.ea.value % step > 0) {
						alert('구매수량은 '+ step +'개 단위로만 가능합니다.');
						return;
					}
				}
				catch (e)
				{}
			}

			if (chkGoodsForm(form)) {

				var _data = {};

				_data.ea = document.frmView.ea.value;
				_data.sales_unit = document.frmView.ea.getAttribute('step') || 1;
				_data.opt = new Array;
				_data.addopt = new Array;
				_data.addopt_inputable = new Array;

				// 기본 옵션
				var opt = document.getElementsByName('opt[]');

				if (opt.length > 0) {

					_data.opt[0] = opt[0].value;
					_data.opt[1] = '';
					if (typeof(opt[1]) != "undefined") _data.opt[1] = opt[1].value;

					var key = _data.opt[0] + (_data.opt[1] != '' ? '|' + _data.opt[1] : '');

					// 가격
					if (opt[0].selectedIndex == 0) key = fkey;
					key = self.get_key(key);	// get_js_compatible_key 참고

					if (typeof(price[key])!="undefined"){

						_data.price = price[key];
						_data.reserve = reserve[key];
						_data.consumer = consumer[key];
						_data.realprice = realprice[key];
						_data.couponprice = couponprice[key];
						_data.coupon = coupon[key];
						_data.cemoney = cemoney[key];
						_data.memberdc = memberdc[key];
						_data.special_discount_amount = special_discount_amount[key];

					}
					else {
						// @todo : 메시지 정리
						alert('추가할 수 없음.');
						return;
					}

				}
				else {
					// 옵션이 없는 경우(or 추가 옵션만 있는 경우) 이므로 멀티 옵션 선택은 불가.
					return;
				}

				// 추가 옵션
				var addopt = document.getElementsByName('addopt[]');
				var tmp_arr_addopt	= [];	// 추가옵션 체크키
				var addopt_key		= '';	// 추가옵션 키
				for (var i=0,m=addopt.length;i<m ;i++ ) {

					if (typeof addopt[i] == 'object') {
						_data.addopt.push(addopt[i].value);

						// 추가옵션 키 정렬
						tmp_arr_addopt	= addopt[i].value.split('^');
						addopt_key		= addopt_key + tmp_arr_addopt[0];
					}

				}
				// 추가옵션 키 생성
				if (addopt_key) {
					addopt_key	= self.get_key(addopt_key);
				}

				// 입력 옵션
				var addopt_inputable = document.getElementsByName('addopt_inputable[]');
				var addopt_input_key	= '';	// 입력옵션 키
				for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

					if (typeof addopt_inputable[i] == 'object') {
						var v = addopt_inputable[i].value.trim();
						if (v) {
							var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
							tmp[2] = v;
							_data.addopt_inputable.push(tmp.join('^'));

							// 입력옵션 키 정렬
							addopt_input_key	= addopt_input_key + v;
						}

						// 필드값 초기화
						addopt_inputable[i].value = '';

					}

				}
				// 입력옵션 키 생성
				if (addopt_input_key) {
					addopt_input_key	= self.get_key(addopt_input_key);
				}

				// 상품키 재세팅
				var key	= key + (addopt_key != '' ? '^' + addopt_key : '') + (addopt_input_key != '' ? '^' + addopt_input_key : '');

				// 이미 추가된 옵션인지
				if (self.data[key] != null)
				{
					alert('이미 추가된 옵션입니다.');
					return false;
				}

				// 옵션 박스 초기화
				for (var i=0,m=addopt.length;i<m ;i++ )
				{
					if (typeof addopt[i] == 'object') {
						addopt[i].selectedIndex = 0;
					}
				}
				//opt[0].selectedIndex = 0;
				//subOption(opt[0]);

				document.getElementById('el-multi-option-display').style.display = 'block';

				// 행 추가
				var childs = document.getElementById('el-multi-option-display').childNodes;
				for (var k in childs)
				{
					if (childs[k].tagName == 'TABLE') {
						var table = childs[k];
						break;
					}
				}

				var td, tr = table.insertRow(0);
				var html = '';

				tr.id = key;

				// 입력 옵션명
				td = tr.insertCell(-1);
				html = '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				// 옵션명
				html += '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				html += self._optJoin(_data.opt);
				html += '</div>';

				// 추가 옵션명
				html += '<div style="font-size:11px;color:#A0A0A0;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				td.innerHTML = html;

				// 수량
				td = tr.insertCell(-1);
				html = '';
				html += '<div style="float:left;"><input type=text name=_multi_ea[] id="el-ea-'+key+'" size=2 value='+ _data.ea +' style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px" onblur="nsGodo_MultiOption.ea(\'set\',\''+key+'\',this.value);"></div>';
				html += '<div style="float:left;padding-left:3">';
				html += '<div style="padding:1 0 2 0"><img src="/shop/data/skin/standard/img/common/btn_multioption_ea_up.gif" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '<div><img src="/shop/data/skin/standard/img/common/btn_multioption_ea_down.gif" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '</div>';
				td.innerHTML = html;

				// 옵션가격
				_data.opt_price = _data.price;
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				td = tr.insertCell(-1);
				td.style.cssText = 'padding-right:10px;text-align:right;font-weight:bold;color:#6A6A6A;';
				html = '';
				html += '<span id="el-price-'+key+'">'+comma( _data.opt_price *  _data.ea) + '원</span>';
				html += '<a href="javascript:void(0);" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;"><img src="/shop/data/skin/standard/img/common/btn_multioption_del.gif"></a>';
				td.innerHTML = html;

				self.data[key] = _data;
				self.data_size++;

				// 총 금액
				self.totPrice();


			}
		},
		ea : function(dir, key,val) {	// up, down

			var min_ea = 0, max_ea = 0, remainder = 0;

			if (document.frmView.min_ea) min_ea = parseInt(document.frmView.min_ea.value);
			if (document.frmView.max_ea) max_ea = parseInt(document.frmView.max_ea.value);

			if (dir == 'up') {
				this.data[key].ea = (max_ea != 0 && max_ea <= this.data[key].ea) ? max_ea : parseInt(this.data[key].ea) + parseInt(this.data[key].sales_unit);
			}
			else if (dir == 'down')
			{
				if ((parseInt(this.data[key].ea) - 1) > 0)
				{
					this.data[key].ea = (min_ea != 0 && min_ea >= this.data[key].ea) ? min_ea : parseInt(this.data[key].ea) - parseInt(this.data[key].sales_unit);
				}

			}
			else if (dir == 'set') {

				if (val && !isNaN(val))
				{
					val = parseInt(val);

					if (max_ea != 0 && val > max_ea)
					{
						val = max_ea;
					}
					else if (min_ea != 0 && val < min_ea) {
						val = min_ea;
					}
					else if (val < 1)
					{
						val = parseInt(this.data[key].sales_unit);
					}

					remainder = val % parseInt(this.data[key].sales_unit);

					if (remainder > 0) {
						val = val - remainder;
					}

					this.data[key].ea = val;

				}
				else {
					alert('수량은 1 이상의 숫자로만 입력해 주세요.');
					return;
				}
			}

			document.getElementById('el-ea-'+key).value = this.data[key].ea;
			document.getElementById('el-price-'+key).innerText = comma(this.data[key].ea * this.data[key].opt_price) + '원';

			// 총금액
			this.totPrice();

		},
		totPrice : function() {
			var self = this;
			var totprice = 0;
			for (var i in self.data)
			{
				if (self.data[i] !== null && typeof self.data[i] == 'object') totprice += self.data[i].opt_price * self.data[i].ea;
			}

			document.getElementById('el-multi-option-total-price').innerText = comma(totprice) + '원';
		},
		get_key : function(str) {

			str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

			var _key = "";

			for (var i=0,m=str.length;i<m;i++) {
				_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
			}

			return _key.toUpperCase();
		}
	}
}();

	
function act(target)
{
	var form = document.frmView;
	form.action = target + ".php";

	var opt_cnt = 0, data;

	nsGodo_MultiOption.clearField();

	for (var k in nsGodo_MultiOption.data) {
		data = nsGodo_MultiOption.data[k];
		if (data && typeof data == 'object') {
			nsGodo_MultiOption.addField(data, opt_cnt);
			opt_cnt++;
		}
	}

	if (opt_cnt > 0) {
		form.submit();
	}
	else {
		if (chkGoodsForm(form)) form.submit();
	}

	return;
}
	
function chgImg(obj)
{
    var objImg = document.getElementById('objImg');
    if (obj.getAttribute("ssrc")) objImg.src = obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("ssrc");
    else objImg.src = obj.src.replace("/t/","/");
<?php if($TPL_VAR["detailView"]=='y'){?>
    // 디테일뷰 추가내용 2010.11.09
    if (obj.getAttribute("lsrc")) { 
        if (obj.getAttribute("lsrc").match( (/^http(s)?:/)) != null) { 
            objImg.setAttribute("lsrc", obj.getAttribute("lsrc")); 
        } 
        else { 
            objImg.setAttribute("lsrc", obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("lsrc")); 
        } 
    }
    else objImg.setAttribute("lsrc", obj.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
    ImageScope.setImage(objImg, beforeScope, afterScope);
    // 디테일뷰 추가내용 2010.11.09
<?php }?>
}
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>
<!-- 개인정보 취급업무 위탁 -->
<table cellpadding="0" cellspacing="0" width="1000" border="0">
<col width="100" />
<col width="900" />
<tr>
	<td class="termsTableBorder termsBorderBottomZero termsBorderRightZero termsPadding termsTdWidth100">사용 여부</td>
	<td class="termsTableBorder termsBorderBottomZero termsPadding">
		<input type="radio" name="private3YN" value="Y" style="border:0px;" <?=$checked['private3YN']['Y']?>  /> 사용함&nbsp;&nbsp;
		<input type="radio" name="private3YN" value="N" style="border:0px;" <?=$checked['private3YN']['N']?>  /> 사용안함
	</td>
</tr>
<tr>
	<td colspan="2"><textarea name="termsEntrust"><?php include $termsFilePath . 'termsEntrust.txt'; ?></textarea></td>
</tr>
</table>

<div style="padding:5px 0 30px 5px;">
	<span class="small" style="line-height: 150%;"> 
		- 쇼핑몰이름은 치환코드{_cfg[‘shopName']}로 제공되어 기본정보 설정에 등록된 “쇼핑몰이름”이 자동으로 표시됩니다.<br />
		- <span class="termsFontWeightBold">등록한 내용은 [회원가입 > 개인정보 취급위탁 항목]</span>에 표시됩니다.
	</span>
</div>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td class="termsPadding">
		<span class="termsFontWeightBold termsFontColorRed">※ 2014년 07월 31일 이전 제작 무료 스킨</span>을 사용하시는 경우 <span class="termsFontWeightBold termsTextUnderline">반드시 스킨패치를 적용</span>해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2064" target="_blank" class="termsFontColorSky termsTextUnderline termsFontWeightBold">[패치 바로가기]</a>
	</td>
</tr>
<tr>
	<td class="termsPadding">
	- 스킨패치 후에는 디자인관리 페이지에서 제공하던 약관/개인정보 관련 텍스트(TXT)파일은 더 이상 사용하지 않으므로 쇼핑몰 정책에 따른 약관 및 개인정보취급 관련 내용을<br /> 
	위의 각 입력 항목에 입력 또는 수정하여 완성해 주시기 바랍니다.
	</td>
</tr>
</table>

<div class="button"><?php echo $termsButtons; ?></div>

<div id="MSG07">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr>
	<td>
		<strong>· 개인정보 취급업무 위탁 내용</strong><br />
		<span class="termsPaddingLeft">- 개인정보 취급업무를 위탁하지 않는 경우 “사용안함”으로 체크를 하시면 됩니다.</span><br />
		<span class="termsPaddingLeft">- 개인정보 취급업무 위탁에 입력된 샘플 내용을 참고하여 실제 쇼핑몰 운영에 적합한 내용으로 수정하여 등록합니다.</span><br />
		<span class="termsPaddingLeft">- 개인정보 수집·이용에 대한 동의와는 별도로 '개인정보취급방침 내용'에서 개인정보취급위탁을 받는 자,개인정보취급위탁을 하는 업무의 내용을 고지하고 동의를 받아야 합니다.</span><br />
		<span class="termsPaddingLeft">- 회원가입 페이지에 나오며, 이용자가 동의를 하지 않아도 가입을 할 수 있습니다. 단, 동의를 하지 않는 경우 이와 관련된 서비스의 이용이 불가능 하다는 내용이 명시되어야 합니다. </span><br />
		<span class="termsPaddingLeft">- 상품 배송, 민원 상담 등 서비스 이행을 위해 반드시 필요한 범위의 위탁 업무의 경우 별도 동의를 받지 않아도 됩니다.</span>
	</td>
</tr>
</table>
</div>
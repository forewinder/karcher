var passwordFinder = function()
{
	var self = this;
	var ajaxFindPasswordUrl = "./_ajax.indb.find_password.php";

	this.returnError = function(code){
		switch (code) {
			case '0001':
				alert('회원 정보가 존재하지 않습니다.');
				break;
			case '0002':
				alert('잘못된 접근입니다. 다시 시도해 주세요.');
				break;
			case '0003':
				alert('유효기간이 만료되었습니다. 다시 시도해 주세요.');
				break;
			case '0004':
				alert('전송이 불가능한 이메일 주소 입니다.');
				break;
			case '0005':
				alert('전송이 불가능한 휴대폰 번호 입니다.');
				break;
			case '0006':
				alert('인증번호를 정확히 입력해 주세요.');
				break;
			case '0007':
				alert('비밀번호 변경이 정상적으로 완료되지 않았습니다.\n\n다시 시도해 주세요.');
				break;
			case '0008':
				alert('비빌번호찾기 휴대폰번호 인증 후 재발급 서비스 사용중 장애가 발생하였습니다.\n\n다른 서비스로 비빌번호찾기를 진행하여 주세요.');
				break;
			case '0009':
				alert('10~16자의 영문대소문자,숫자,특수문자를 조합하여 사용할 수 있습니다.');
				break;
			default:
				alert('기타 오류가 발생하였습니다.\n\n고객센터에 문의하여 주세요.');
				break;
		}
	}

	this.setToken = function(otpType, srch_id, srch_name, srch_mail) {
		$.post(ajaxFindPasswordUrl, {
			type : "setToken",
			srch_id : srch_id,
			srch_name : srch_name,
			srch_mail : srch_mail
		},
		function(responseData) {
			result = new Array();
			result = responseData.split("|");

			var resultCode = result[0];
			var token = result[1];

			if(resultCode == '0000'){
				//OTP 발송
				self.sendOTP(otpType, token, srch_id);
			}
			else {
				self.returnError(resultCode);
			}
		});
	}

	this.sendOTP = function(otpType, token, srch_id) {
		$.post(ajaxFindPasswordUrl, {
			type : "sendOTP",
			otpType : otpType,
			m_id : srch_id,
			token : token
		},
		function(responseData) {
			if(responseData == '0000'){
				if(otpType == 'mail'){
					alert('인증번호가 고객님의 메일주소로 전송 되었습니다.');
				}
				else if(otpType == 'mobile'){
					alert('인증번호가 고객님의 핸드폰 번호로 전송 되었습니다.');
				}
				else {

				}

				$("#find_password_form input[name='otpType']").val(otpType);
				$("#find_password_form input[name='token']").val(token);
				$("#find_password_form input[name='m_id']").val(srch_id);
				$('#find_password_form').attr('action','./find_password_auth.php').submit();
			}
			else {
				self.returnError(responseData);
			}
		});
	}

	this.resendOTP = function (otpType, token, srch_id){
		$.post(ajaxFindPasswordUrl, {
			type : "sendOTP",
			otpType : otpType,
			m_id : srch_id,
			token : token
		},
		function(responseData) {
			if(responseData == '0000'){
				alert('인증번호가 재전송 되었습니다.');
			}
			else {
				self.returnError(responseData);

				window.location.replace('./find_password.php');
			}
		});
	}

	this.compareOTP = function (token, m_id, otp){
		$.post(ajaxFindPasswordUrl, {
			type : "compareOTP",
			m_id : m_id,
			otp : otp,
			token : token
		},
		function(responseData) {
			if(responseData == '0000'){
				$('#find_password_auth_form').attr('action','./change_password.php').submit();
			}
			else {
				self.returnError(responseData);

				if(responseData != '0006'){
					window.location.replace('./find_password.php');
				}
			}
		});
	}

	this.changePwd = function (token, m_id, newPassword){
		$.post(ajaxFindPasswordUrl, {
			type : "change",
			token : token,
			m_id : m_id,
			newPassword : newPassword
		},
		function(responseData) {
			if(responseData == '0000'){
				alert("비밀번호 변경이 완료되었습니다.");
				window.location.replace('../index.php');
			}
			else {
				self.returnError(responseData);
			}
		});
	}
}
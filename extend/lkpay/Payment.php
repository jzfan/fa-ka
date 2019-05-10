<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title id="AgentSiteName">充值页面</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <link href="images/Recharge.css" rel="stylesheet" type="text/css" />
    <script type='text/javascript' src='http://libs.baidu.com/jquery/1.7.2/jquery.min.js'></script>
	<style>
	.changebank{background-image: url(images/banks.png);background-repeat: no-repeat;}
	#interChoise label.selected{background:none;}
	</style>
    <script type="text/javascript">
        $(function () {
            $("#interChoise label").hover(function () {
                $(this).addClass("active");
            }, function () {
                $(this).removeClass("active");
            }).click(function () {
                $("#interChoise label").removeClass("selected");
				$("#interChoise label").removeClass("active");
                $(this).addClass("selected");
            });
            
           
            $("#payBtn").click(function () {
                subMit();
            });
            $("#maskClose").click(function () {
                $("#mask").hide();
                $("#payBtn").val("立即充值").removeAttr("disabled").removeClass("BtnFalse");
            });
        });
		function subMit() {
            var money = $("#ChargeMoney").val();
			var username = $("#txtPayAccounts").val();
            var reg = /^[0-9]*[1-9][0-9]*$/;
			if (username=='') {
                $("#chkusername").text("请输入用户名");
                return;
            }
            if (!reg.test(money)) {
                //$("#chkMoney").text("请输入正确的充值金额");
                //return;
            }
            $("#payBtn").val("正在提交...").addClass("BtnFalse").attr("disabled", "disabled");
            $("#mask").show();
            $("#payform").submit();
        }

    </script>
</head>
<body>
    
<div>
</div>

<div>


</div>
    <div class="header">
        <div class="alignMid">
            <div class="pageName left" style="margin-left:0">
                账户充值</div>
            <div class="clear">
            </div>
        </div>
    </div>
    <div class="main">
        <div class="alignMid">
            <div class="nav">
                <a href="Payment.php" class="active">接口支付</a><a href="PointerCard.php">点卡兑换<i></i></a>
                <div class="clear">
                </div>
            </div>
            <div class="content">
                <div class="conMid">
				    <form name="payform" method="post" action="go.php" target="_blank" id="payform">
                    <table cellpadding="0" cellspacing="0" class="recargeTab" id="recargeTab">
                        <tr>
                            <td class="field">
                                支付方式：
                            </td>
                            <td class="fieldVal" id="interChoise">
                                <label id="alipay" for="inputAli" class="selected active">
                                    <span class="aliPay">
                                        <input name="bankid" value="2" type="radio" checked="checked" id="inputAli"></span><i></i></label>
								<label id="weixin" for="inputwx">
                                    <span class="weixin">
                                        <input name="bankid" value="3" type="radio" id="inputwx"></span><i></i></label>
<div class="clear"></div>
                                <label id="bank-icbc" for="inputicbc">
                                    <span class="chagebank bank-icbc">
                                        <input name="bankid" value="10001" type="radio" id="inputicbc"></span><i></i></label>
                                <label id="bank-nyyh" for="inputnyyh">
                                    <span class="chagebank bank-nyyh">
                                        <input name="bankid" value="10002" type="radio" id="inputnyyh"></span><i></i></label>
                                <label id="bank-zsyh" for="inputzsyh">
                                    <span class="chagebank bank-zsyh">
                                        <input name="bankid" value="10003" type="radio" id="inputzsyh"></span><i></i></label>
								<label id="bank-zgyh" for="inputzgyh">
                                    <span class="chagebank bank-zgyh">
                                        <input name="bankid" value="10004" type="radio" id="inputzgyh"></span><i></i></label>
								<label id="bank-jsyh" for="inputjsyh">
                                    <span class="chagebank bank-jsyh">
                                        <input name="bankid" value="10005" type="radio" id="inputjsyh"></span><i></i></label>
								<label id="bank-msyh" for="inputmsyh">
                                    <span class="chagebank bank-msyh">
                                        <input name="bankid" value="10006" type="radio" id="inputmsyh"></span><i></i></label>
								<label id="bank-zxyh" for="inputzxyh">
                                    <span class="chagebank bank-zxyh">
                                        <input name="bankid" value="10007" type="radio" id="inputzxyh"></span><i></i></label>
								<label id="bank-jtyh" for="inputjtyh">
                                    <span class="chagebank bank-jtyh">
                                        <input name="bankid" value="10008" type="radio" id="inputjtyh"></span><i></i></label>
								<label id="bank-xyyh" for="inputxyyh">
                                    <span class="chagebank bank-xyyh">
                                        <input name="bankid" value="10009" type="radio" id="inputxyyh"></span><i></i></label>
								<label id="bank-gdyh" for="inputgdyh">
                                    <span class="chagebank bank-gdyh">
                                        <input name="bankid" value="10010" type="radio" id="inputgdyh"></span><i></i></label>
								<label id="bank-yzyh" for="inputyzyh">
                                    <span class="chagebank bank-yzyh">
                                        <input name="bankid" value="10012" type="radio" id="inputyzyh"></span><i></i></label>
                            </td>
                        </tr>
						<tr>
                            <td class="field">
                                用户名：
                            </td>
                            <td class="fieldVal" valign="middle">
                                <input type="text" name="txtPayAccounts" class="chargeMoney" id="txtPayAccounts" placeholder="输入您要充值的用户名" style="width:200px"><label class="tips">请填写正确的用户编号，以便能及时为您处理</label><span
                                        id="chkusername" style="color: Red; font-size: 12px; padding-left: 8px;"></span>

                            </td>
                        </tr>
                        <tr>
                            <td class="field">
                                充值金额：
                            </td>
                            <td class="fieldVal" valign="middle">
                                <input type="text" name="chargeMoney" class="chargeMoney" placeholder="充值金额" id="ChargeMoney"><label
                                    class="unit">&nbsp;元&nbsp;&nbsp;</label><label class="tips" id="PayMoney">最小1元最大5000元</label><span
                                        id="chkMoney" style="color: Red; font-size: 12px; padding-left: 8px;"></span>

                            </td>
                        </tr>
                        <tr>
                            <td class="field">&nbsp;
                                
                            </td>
                            <td class="fieldVal" height="70" valign="bottom">
                                <input type="button" value="立即充值" class="payBtn" id="payBtn" />
                            </td>
                        </tr>
                    </table>
					</form>
                    <div class="faqQuestion">
                        <div class="faqTitle">
                            常见充值问题：</div>
                        <div class="questionContent">
                            <div class="question">
                                问：在线加款多久能到账？</div>
                            <div class="answer">
                                答：一般在线支付都是能在支付成功后即时到账的，但也有可能会因网络原因导致延时，大概等待10几分钟左右也能正常到账。
                            </div>
                            <div class="question">
                                问：如果超过10分钟还未到账怎么办？
                            </div>
                            <div class="answer">
                                答：与网站客服联系，核对汇款记录后帮你补款。</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        版权所有 &copy;<label id="Copyright">Copyright 2011 - 2015</label>
    </div>
    </form>
    <div class="mask" id="mask">
        <div class="maskCenter">
            <div class="maskTitle">
                登录平台支付<a href="javascript:;" id="maskClose"></a></div>
            <div class="resultTxt">
                请您在新打开的支付平台页面进行支付，支付完成前请不要关闭该窗口</div>
            <div class="maskBtn">
                <input type="button" value="已完成充值" class="resultBtn" onclick="window.close()" />&nbsp;&nbsp;&nbsp;<input type="button"
                    value="重新充值" class="resultBtn" onclick="location.href=location.href" /></div>
        </div>
    </div>
</body>
</html>
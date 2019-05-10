<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title id="AgentSiteName">充值页面</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <link href="images/Recharge.css" rel="stylesheet" type="text/css" />
    <script type='text/javascript' src='http://libs.baidu.com/jquery/1.7.2/jquery.min.js'></script>
    <script src="images/jQSelect.js" type="text/javascript"></script>
    <script type="text/javascript">
        var aParvalue = { 4: [5, 10, 15, 20, 30, 60, 100, 200 ], 5: [1, 2, 3, 5, 9, 10, 15, 25, 30, 35, 45, 50, 100, 350, 1000 ],6: [5, 6, 10, 15, 20, 30, 50, 100, 200, 300, 500, 1000 ],7: [6, 15, 18, 30, 50, 100 ],8: [5, 15, 30, 40, 100 ], 9: [10, 15, 20, 25, 30, 50, 60, 100, 300, 500 ], 10: [5, 10, 20, 25, 30, 50, 100 ],11: [5, 10, 15, 20, 30, 50, 100 ],12: [5, 10, 20, 30, 50, 100 ],13: [10, 20, 30, 50, 100, 200, 300, 500 ], 14: [10, 20, 30, 50, 100, 200, 300, 500 ], 15: [10, 20, 30, 50, 100, 200, 300, 500 ],16: [10, 15, 30, 50, 100 ],17: [5, 10, 15, 30, 50, 100 ],18: [10, 20, 30, 40, 50, 60, 70, 80, 90, 100 ], 19: [10, 20, 30, 50, 100, 300 ], 23: [1, 2, 3, 5, 9, 10, 15, 25, 30, 35, 45, 50, 100, 300, 350, 1000 ] };
        var oDiscountJson = {"Status":{"Code":"success","Msg":"请求成功！"},"Data":[{"ContactName":"","CompanyName":"","Balance":"0.000"}]};
        var timer = null;
        $(function () {
            
            $("#optionsBox a").click(function () {
                $("#optionsBox a").removeClass("choised");
                $(this).addClass("choised");
                var parIndex = $.trim($(this).attr("data"));
                if (parIndex == "") {
                    $("#gameCardTr").show();
                    WriteParvalue("00");
                    $("#pointNum,#pointPass").removeAttr("placeholder");
                } else {

                    switch (parIndex) {
                        case "13":
                            $("#pointNum").attr("placeholder", "请输入17位数字的序列号");
                            $("#pointPass").attr("placeholder", "请输入18位数字的充值卡密码");
                            break;
                        case "14":
                            $("#pointNum").attr("placeholder", "请输入15位数字的序列号");
                            $("#pointPass").attr("placeholder", "请输入19位数字的充值卡密码");
                            break;
                        case "15":
                            $("#pointNum").attr("placeholder", "请输入19位数字的序列号");
                            $("#pointPass").attr("placeholder", "请输入18位数字的充值卡密码");
                            break;

                    }

                    $("#cardType a").removeClass("accepted");
                    $("#gameCardTr").hide();
                    WriteParvalue(parIndex);
                }
                LightBtn();
            });

            $("#cardType a").click(function () {
                $("#cardType a").removeClass("accepted");
                $(this).addClass("accepted");
                WriteParvalue($.trim($(this).attr("data")));
                LightBtn();
            });

            $("#parvalue").jQSelect({ id: "parvalueId" });
            $(".lInput").keyup(function () {
                LightBtn();
            });
            $(".lInput").on('input', function () {
                LightBtn();
            });
            $(".lInput").blur(function () {
                LightBtn();
            });
            $("#payBtn").click(function () {
                var CardTypeName = "";
                var CardType = $("#optionsBox .choised").attr("data");

                if (CardType != "") {
                    if (CardType == "14") {
                        CardTypeName = "移动卡";
                    } else {
                        if (CardType == "15") {
                            CardTypeName = "联通卡";
                        } else {
                            CardTypeName = "电信卡";
                        }
                    }
                } else {
                    CardType = $("#cardType .accepted").attr("data");
                    CardTypeName = $("#cardType .accepted span").html();
                }
                var Parvalue = $("#parvalueId").val();
                var CardNo = $.trim($("#pointNum").val());
                var CardPassword = $.trim($("#pointPass").val());
				var txtPayAccounts = $.trim($("#txtPayAccounts").val());
				

                $("#payBtn").val("正在充值...").attr("disabled", "disabled").addClass("BtnFalse");
				$("#Resultimg").show();
				$("#Resultmsg").html("正在充值中，请稍后...");
                $("#waitResult").show();

                var datas = "Method=Recharge&ChannelId=" + CardType + "&FaceValue=" + Parvalue + "&cardId=" + CardNo + "&cardPass=" + CardPassword + "&txtPayAccounts=" + txtPayAccounts;
                $.ajax({
                    url: "send/card_getway.php",
                    type: "post",
                    data: datas,
                    dataType: "json",
                    success: function (result) {
                        if (result.Status.Code == "success") {
							$("#payBtn").val("立即充值").removeAttr("disabled").removeClass("BtnFalse").addClass("payBtn");
                            $("#Resultmsg").html(result.Status.Msg);
                        }
						if (result.Status.Code == "error") {
							$("#payBtn").val("立即充值").removeAttr("disabled").removeClass("BtnFalse").addClass("payBtn");
							$("#Resultimg").hide();
                            $("#Resultmsg").html("<font color=red>"+result.Status.Msg+"</font>");
                        }
						if (result.Status.Code == "fail") {
							
							$("#Resultimg").show();
                            $("#Resultmsg").html("<font color='#29c300'>"+result.Status.Msg+"</font>");
							var nowOrderNum = result.Status.Orderid;
							timer = setInterval(function () {OrderResult(nowOrderNum,CardType,CardNo,Parvalue);}, 5000);
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
						$("#Resultimg").hide();
						$("#Resultmsg").html("<font color=red>系统错误</font>");
						$("#payBtn").val("立即充值").removeAttr("disabled").removeClass("BtnFalse").addClass("payBtn");
                    },
                    cache: false
                })
            });
        });
		function OrderResult(orderNum,channelid,cardid,facevalue) {
            var datas1 = "Method=GetRechargeResult&C_OrderId=" + orderNum + "&C_ChannelId=" + channelid + "&C_CardId=" + cardid + "&C_FaceValue=" + facevalue;
            $.ajax({
                url: "send/card_getway.php",
                type: "post",
                data: datas1,
                dataType: "json",
                success: function (result) {
                    if (result.Status.Code == "success") {
							$("#Resultimg").hide();
                            $("#Resultmsg").html("<font color='#29c300'>"+result.Status.Msg+"</font>");
							$("#payBtn").val("立即充值").removeAttr("disabled").removeClass("BtnFalse").addClass("payBtn");
							clearInterval(timer);
                        }
					if (result.Status.Code == "error") {
							$("#Resultimg").hide();
                            $("#Resultmsg").html("<font color=red>"+result.Status.Msg+"</font>");
							$("#payBtn").val("立即充值").removeAttr("disabled").removeClass("BtnFalse").addClass("payBtn");
							clearInterval(timer);
                        }
					if (result.Status.Code == "fail") {
							$("#Resultimg").show();
                            $("#Resultmsg").html("<font color='#29c300'>"+result.Status.Msg+"</font>");
                        }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {

                },
                cache: false
            });
        }

        //WriteParvalue
        function WriteParvalue(index) {
            $("#parvalueTxt").val("选择面值");
            $("#parvalueId").val("");
            $("#parvalue").unbind("click");
            $("#parvalueList").empty();
            $("#MoneyTxt").html("**.**");
            $("#parDiscount").html("*");
            if (index == "00") {
                return;
            }
            var aTarget = aParvalue[index];
            for (var i = 0; i < aTarget.length; i++) {
                var oLi = $("<li id='" + aParvalue[index][i] + "'>" + aParvalue[index][i] + "元</li>");
                $("#parvalueList").append(oLi);
            }
            $("#parvalueList li").click(function () {
                var money = parseFloat(this.id).toFixed(2);
                $("#poundage").html(money);
                $("#parvalueId").val(this.id);
                LightBtn();
                for (var i = 0; i < oDiscountJson.length; i++) {
                    var CardDiscount = oDiscountJson[i].CardDiscount;
                    if (index == oDiscountJson[i].CardType && this.id == oDiscountJson[i].CardMoney) {
                        $("#parDiscount").html(oDiscountJson[i].CardDiscount);
                        var tureMoney = (money * (parseFloat(CardDiscount) / 100)).toFixed(2);
                        $("#MoneyTxt").html(tureMoney);
                        break;
                    } else {
                        if (index == oDiscountJson[i].CardType) {
                            $("#parDiscount").html(oDiscountJson[i].CardDiscount);
                            var tureMoney = (money * (parseFloat(CardDiscount) / 100)).toFixed(2);
                            $("#MoneyTxt").html(tureMoney);
                        }
                    }

                }


            });
            $("#parvalue").jQSelect({ id: "parvalueId" });
        }
        //LightBtn
        function LightBtn() {
            var pointCard = true;
            var cardType = $("#optionsBox .choised").length > 0;
            if (cardType && $("#optionsBox .choised").attr("data") == "") {
                pointCard = $("#cardType .accepted").length > 0 ? true : false;
            }
            var parvalueTest = $("#parvalueId").val() == "" ? false : true;
            var pointNumTest = $.trim($("#pointNum").val()) == "" ? false : true;
            var pointPassTest = $.trim($("#pointPass").val()) == "" ? false : true;

            if (cardType & pointCard & parvalueTest & pointNumTest & pointPassTest) {
                $("#payBtn").removeAttr("disabled").removeClass("BtnFalse");
            } else {
                $("#payBtn").attr("disabled", "disabled").addClass("BtnFalse");
            }
        }
        
    </script>
</head>
<body>
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
                <a href="Payment.php">接口支付</a><a href="PointerCard.php" class="active">点卡兑换</a>
                <div class="clear">
                </div>
            </div>
            <div class="content">
                <div class="conMid">
                    <table cellpadding="0" cellspacing="0" class="recargeTab">
                        <tr>
                            <td class="field">
                                点卡类型：
                            </td>
                            <td class="fieldVal">
                                <div class="optionsBox" id="optionsBox">
                                    <a href="javascript:;" data="14"><span class="chinamobile"></span><i></i></a><a href="javascript:;"
                                        data="15"><span class="chinaunicom"></span><i></i></a><a href="javascript:;" data="13">
                                            <span class="chinatelecom"></span><i></i></a><a href="javascript:;" data=""><span
                                                class="pointCard">各种游戏点卡</span><i></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr class="none" id="gameCardTr">
                            <td class="field">
                                游戏点卡：
                            </td>
                            <td class="fieldVal" valign="middle">
                                <div class="cardType" id="cardType">
                                    <a href="javascript:;" data="6">
                                        <label>
                                        </label>
                                        <span>骏网一卡通</span><i></i></a><a href="javascript:;" data="5">
                                            <label>
                                            </label>
                                            <span>盛大卡支付</span><i></i></a> <a href="javascript:;" data="11">
                                                <label>
                                                </label>
                                                <span>网易一卡通</span><i></i></a> <a href="javascript:;" data="9">
                                                    <label>
                                                    </label>
                                                    <span>征途一卡通</span><i></i></a> <a href="javascript:;" data="7">
                                                        <label>
                                                        </label>
                                                        <span>完美一卡通</span><i></i></a> <a href="javascript:;" data="8">
                                                            <label>
                                                            </label>
                                                            <span>搜狐一卡通</span><i></i></a> <a href="javascript:;" data="10">
                                                                <label>
                                                                </label>
                                                                <span>久游一卡通</span><i></i></a>
                                    
                                    <a href="javascript:;" data="4">
                                        <label>
                                        </label>
                                        <span>腾讯QQ卡</span><i></i></a> <a href="javascript:;" data="12">
                                            <label>
                                            </label>
                                            <span>光宇一卡通</span><i></i></a> <a href="javascript:;" data="18">
                                                <label>
                                                </label>
                                                <span>天下一卡通</span><i></i></a> <a href="javascript:;" data="17">
                                                    <label>
                                                    </label>
                                                    <span>天宏一卡通</span><i></i></a> <a href="javascript:;" data="23">
                                                        <label>
                                                        </label>
                                                        <span>盛付通一卡通</span><i></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="field">
                                面值：
                            </td>
                            <td class="fieldVal" valign="middle">
                                <div class="selectbox" id="parvalue">
                                    <div class="cartes">
                                        <input type="text" class="listTxt" value="选择面值" id="parvalueTxt" onfocus="this.blur()" />
                                        <input type="hidden" class="listVal" value="" id="parvalueId" />
                                    </div>
                                    <div class="downNarrow">
                                    </div>
                                    <div class="lists">
                                        <ul class="list" id="parvalueList">
                                        </ul>
                                    </div>
                                </div>
                                <div class="left" style="line-height: 37px;">
                                    <label class="tips">
                                        &nbsp;&nbsp;&nbsp;&nbsp;请核对面值，若卡面值大于<span class="poundage" id="poundage">**</span>元，充值后您将<span
                                            class="poundage">损失卡内余额</span>（不支持金额分次提交）</label></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="field">
                                点卡卡号：
                            </td>
                            <td class="fieldVal" valign="middle">
                                <input type="text" class="lInput" placeholder="请输入17位数字的序列号" id="pointNum" />
                            </td>
                        </tr>
                        <tr>
                            <td class="field">
                                点卡密码：
                            </td>
                            <td class="fieldVal" valign="middle">
                                <input type="text" class="lInput" placeholder="请输入18位数字的充值卡密码" id="pointPass" />
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
                            <td class="field">&nbsp;
                                
                            </td>
                            <td class="fieldVal" height="70" valign="bottom">
                                <input type="button" value="立即充值" class="payBtn BtnFalse" id="payBtn" disabled="disabled" />
                                <div class="waitResult none" id="waitResult" style="margin-top:10px">
                                    <img src="images/loading2.gif" width="15" height="15" style="vertical-align: middle" id="Resultimg" /><label id="Resultmsg">正在充值中，请稍后...</label></div>
                            </td>
                        </tr>
                    </table>
                    <div class="faqQuestion">
                        <div class="faqTitle">
                            常见充值问题：</div>
                        <div class="questionContent">
                            <div class="question">
                                问：哪里可以买到这类游戏点卡？</div>
                            <div class="answer">
                                答：游戏点卡可在就近售卡处（如报刊亭、营业厅）购买，也可在网络上购买电子卡；
                            </div>
                            <div class="question">
                                问：如果我选了10元面值，确用100元的卡充值了会怎么样？</div>
                            <div class="answer">
                                答：请选择对应面值，否则将有可能导致资金损失。</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        版权所有 &copy;<label id="Copyright">Copyright 2011 - 2015</label>
    </div>
</body>
</html>
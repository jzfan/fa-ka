$(function () {
    resize();
    $(window).resize(resize);

    function resize() {
        if ($(window).width() > 550) {
            $.nyroModalSettings({
                type: "iframe",
                minHeight: 440,
                minWidth: 550,
                titleFromIframe: true,
                modal: true
            });
        } else {
            $.nyroModalSettings({
                type: "iframe",
                minHeight: 500,
                minWidth: 550,
                titleFromIframe: true,
                modal: true
            });
        }
    }

    if (user_popup_message != "") {
        $("#myOnPageContent").html(user_popup_message);
        $("#popup_gg").trigger("click");
    }
    $("#isagree").click(function () {
        $("#agreement").toggle();
    });
    $("[name=isagree]").click(function () {
        $("#agreement").toggle();
    });

    // 获取批发详情
    $("#isdiscount").click(function () {
        var goodid = $("#goodid").val();
        $.post("/ajax/getdiscountdetails", {goodid: goodid}, function (data) {
            console.log(data);
            $("#discountdetail").html(data);
        });
        $("#discountdetail").toggle();
    });

    $("#isemail").click(function () {
        $("#email").toggle();
        if ($(this).is(":checked")) {
            $("[name=sms_price]").val(0);
            $("#email").toggle();
            $("[name=email]").focus();
            is_contact_limit = "email";
            $('[name="is_contact_limit"]').val("email");
        } else {
            is_contact_limit = is_contact_limit_default;
            $('[name="is_contact_limit"]').val(is_contact_limit_default);
        }
        goodDiscount();
        goodschk();
    });
    $("#is_coupon").click(function () {
        if ($(this).is(":checked")) {
            $("#couponcode").show();
            $("[name=couponcode]").focus();
        } else {
            $("#couponcode").hide();
            $("#checkcoupon").hide();
        }
    });
    $("#is_rev_sms").click(function () {
        $("#email").toggle();
        if ($(this).is(":checked")) {
            $("#is_rev_sms_tip").show();
            $("[name=contact]").focus();
            $("[name=sms_price]").val($(this).attr("data-cost"));
            is_contact_limit = "mobile";
            $('[name="is_contact_limit"]').val("mobile");
        } else {
            $("#is_rev_sms_tip").hide();
            $("[name=sms_price]").val(0);
            is_contact_limit = is_contact_limit_default;
            $('[name="is_contact_limit"]').val(is_contact_limit_default);
        }
        goodDiscount();
        goodschk();
        updateContactLimit();
    });
    $("#is_pwdforsearch").click(function () {
        if ($(this).is(":checked")) {
            $("#pwdforsearchinput").show();
            $("[name=pwdforsearch2]").focus();
        } else {
            $("#pwdforsearchinput").hide();
        }
    });
    $("#select_pay li").each(function () {
        $(this).hover(function () {
            $(this).addClass("cursor");
        });
    });
    $("#select_pay li").click(function () {
        var id = $(this)
            .find("input")
            .attr("id");
        $("#" + id).attr("checked", true);
        $("#" + id + "s")
            .show()
            .siblings()
            .hide();
        $($("#" + id).parent())
            .addClass("selected")
            .siblings()
            .removeClass("selected");
        if (id != "card") {
            $("#step_three").hide();
        } else {
            if (is_display != 1) {
                $("#step_three").show();
            }
        }
    });
    $(".paylist ul li").each(function () {
        $(this).hover(
            function () {
                $(this).addClass("yb");
                $(this).addClass("cursor");
            },
            function () {
                $(this).removeClass("yb");
            }
        );
        $(this).click(function () {
            $(".paylist li").removeClass("ybh");
            $(this).addClass("ybh");
            $(this)
                .find("input")
                .attr("checked", true);
            var pname = $(this)
                .find("img")
                .attr("alt");
            $("#select_pay li").each(function () {
                if ($(this).hasClass("selected")) {
                    var pt = $(this)
                        .find("input")
                        .attr("id");
                    if (pt == "card") {
                        $(".bt").hide();
                        $(".bt2").show();
                    } else {
                        $(".bt").show();
                        $(".bt2").hide();
                    }
                    pname = pt == "card" ? pname : "";
                }
            });
            $("#payinfo .pinfo1").hide();
            $("#payinfo .pinfo2").show();
            if (pname != "") {
                $(".pinfo3").hide();
            } else {
                $(".pinfo3").hide();
            }
            $("#payinfo .payname").html("[" + pname + "]");
            getrate();
            get_pay_card_info();
            getCardLength();
        });
    });

    //弹窗
    $.get(
        "/shop/shop/getProtocol",
        {userid: userid},
        function (res) {
            if (typeof res.data == "string") {
                layer.open({
                    title: "购卡协议",
                    content:
                        '<div id="buy-protocol">' +
                        res.data +
                        "</div><style>#buy-protocol img{width:100%}</style>",
                    btn: ["我同意协议"]
                });
            }
        },
        "json"
    );

    $.get(
        "/shop/shop/popNotice",
        {userid: userid},
        function (res) {
            var notice = $("#notice").val();
            if (res.code == 200 && notice != "") {
                layer.open({
                    title: "商家公告",
                    content: notice,
                    btn: ["关闭"]
                });
            }
        },
        "json"
    );
});

function checkCoupon() {
    var couponcode = $.trim($("[name=couponcode]").val());
    if (cateid == 0) {
        cateid = $("#cateid").val();
    }
    $("#checkcoupon").show();
    $.post(
        "/ajax/checkcoupon",
        {
            couponcode: couponcode,
            userid: userid,
            cateid: cateid,
            t: new Date().getTime()
        },
        function (data) {
            if (data) {
                data = eval(data);
                if (data.result == 0) {
                    $("#checkcoupon").html(data.msg);
                } else if (data.result == 1) {
                    $("[name=coupon_ctype]").val(data.ctype);
                    $("[name=coupon_value]").val(data.coupon);
                    $("#checkcoupon").html('<span class="blue">此优惠券可用</span>');
                    goodschk();
                } else {
                    $("#checkcoupon").html("验证失败！");
                }
            }
        },
        "json"
    );
}

function get_pay_card_info() {
    var channelid = 0;
    $(".paylist li").each(function () {
        if (
            $(this)
                .find("input")
                .is(":checked")
        ) {
            channelid = $(this)
                .find("input")
                .val();
        }
    });
    if (channelid != 0 && !isNaN(channelid)) {
        var option = '<option value="">请选择充值卡面额</option>';
        $.post("/ajax/getpaycardinfo", {channelid: channelid}, function (data) {
            $(".cardvalue").each(function () {
                $(this).html(option + data);
            });
        });
    }
}

function select_card_quantity() {
    var quantity = $("[name=cardquantity]").val();
    quantity = quantity - 1;
    $(".card_list_add").html("");
    for (var i = 1; i <= quantity; i++) {
        $(".card_list_add").append($(".card_list:first").clone());
    }
}

function selectcateid() {
    var cateid = $("#cateid").val();
    $("[name=couponcode]").val("");
    $("#loading").show();
    $("#goodid").hide();
    var option = '<option value="">请选择商品</option>';
    if (cateid > 0) {
        $.post("/ajax/getgoodlist", {cateid: cateid}, function (data) {
            if (data == "ok") {
                alert("此分类下没有商品！");
            } else {
                $("#loading").hide();
                $("#goodid").show();
                $("#goodid").html(option + data);
            }
        });
    } else {
        $("#loading").hide();
        $("#goodid").show();
        $("#goodid").html(option);
    }
    getrate();
    $(".pinfo1").show();
    $(".pinfo2").hide();
    $(".pinfo3").hide();
}

function selectgoodid() {
    var goodid = $("#goodid").val();
    if ($("[name=couponcode]").val() != "") {
        checkCoupon();
    }
    $("#price").html(
        '<img src="' + static_url + '/static/app/images/load.gif" />'
    );
    $.post(
        "/ajax/getgoodinfo",
        {goodid: goodid},
        function (data) {
            if (data) {
                $("#price").html(data.price);
                $("#remark").html(data.remark);
                $("#gonggao").html(data.gonggao);
                limit_quantity = data.limit_quantity;
                if (data.remark != "") {
                    $("#buy_border1").css("display", "block");
                }
                if (data.is_coupon == 0) {
                    //取消掉优惠券码
                    $("[name=couponcode]").val("");
                    $("#goodCoupon").hide();
                }
                if (data.is_coupon == 1) {
                    $("#goodCoupon").show();
                }
                if (data.is_pwdforsearch == 0) {
                    $("#pwdforsearch2").hide();
                    $("#pwdforsearch1").hide();
                }
                if (data.is_pwdforsearch == 1) {
                    $("#pwdforsearch2").hide();
                    $("#pwdforsearch1").show();
                }
                if (data.is_pwdforsearch == 2) {
                    $("#pwdforsearch1").hide();
                    $("#pwdforsearch2").show();
                }
                //显示批发价格
                if (data.is_discount == 1) {
                    $("#isdiscount").css("display", "inline");
                    $("#isdiscount_span").css("display", "inline");
                }
                //隐藏批发价格标签
                if (data.is_discount == 0) {
                    $("#isdiscount_span").css("display", "none");
                }
                if (data.limit_quantity > 0) {
                    $("[name=quantity]").val(data.limit_quantity);
                    $("[name=quantity]").attr({
                        title: "本商品最少购买数量为" + limit_quantity + "件！"
                    });
                    $("#limit_quantity_tip").show();
                } else {
                    $("[name=quantity]").val(1);
                    $("[name=quantity]").removeAttr("title");
                    $("#limit_quantity_tip").hide();
                }
                is_contact_limit = data.contact_limit;
                $('[name="is_contact_limit"]').val(data.contact_limit);
                $(".contact_limit_tip").text(data.contact_limit);
                $("[name=danjia]").val(data.price);
                $("#goodInvent").html(data.goodinvent);
                $("[name=is_discount]").val(data.is_discount);
                getrate();
                goodDiscount();
                $(".pinfo1").hide();
                $(".pinfo2").show();
                $(".pinfo3").hide();
                if (data.is_pwdforbuy == 1) {
                    getPwdforbuy();
                }
            }
        },
        "json"
    );
}

function getPwdforbuy() {
    $.nyroModalManual({height: 120, width: 400, content: dis_pwd_content});
}

function verify_pwdforbuy() {
    var pwdforbuy = $.trim($("[name=pwdforbuy]").val());
    if (pwdforbuy == "") {
        alert("请填写验证码！");
        $("[name=pwdforbuy]").focus();
        return false;
    }
    var reg = /^([a-z0-9A-Z]+){6,20}$/;
    if (!reg.test(pwdforbuy)) {
        alert("验证码格式为6-20个长度，数字、大小写字母或组合！");
        $("[name=pwdforbuy]").focus();
        return false;
    }
    $("#verify_pwdforbuy").attr("disabled", true);
    $("#verify_pwdforbuy_msg").show();
    var goodid = $("#goodid").val();
    $.post(
        "/ajax/checkpwdforbuy",
        {pwdforbuy: pwdforbuy, goodid: goodid, t: new Date().getTime()},
        function (data) {
            if (data == "ok") {
                $("#verify_pwdforbuy_msg").hide();
                alert("验证成功，请继续购买！");
                parent.$.nyroModalRemove();
            } else {
                $("#verify_pwdforbuy_msg").hide();
                alert(data);
                $("#verify_pwdforbuy").attr("disabled", false);
                return false;
            }
        }
    );
}

function changequantity() {
    goodDiscount();
    goodschk();
}

function goodDiscount() {
    var is_discount = $("[name=is_discount]").val();
    var quantity = parseInt($.trim($("[name=quantity]").val()));
    var goodid = $("#goodid").val();
    if (is_discount == 1) {
        $.post(
            "/ajax/getdiscount",
            {goodid: goodid, quantity: quantity},
            function (data) {
                if (data > 0) {
                    $("#price").html(data);
                    $("[name=danjia]").val(data);
                    goodschk();
                }
            }
        );
    }
}

function getrate() {
    var goodid = $("[name=goodid]").val();
    var cateid = $("[name=cateid]").val();
    var channelid = 0;
    $("[name=pid]").each(function () {
        if ($(this).is(":checked")) {
            channelid = $(this).val();
        }
    });
    if (goodid == "") {
        goodid = 0;
    }
    if (cateid == "") {
        cateid = 0;
    }
    $.post(
        "/ajax/getrate",
        {userid: userid, cateid: cateid, goodid: goodid, channelid: channelid},
        function (data) {
            $(".rate").html(data);
            goodschk();
        }
    );
}

function updateContactLimit() {
    return;
    $.post("/ajax/updateContactLimit", {is_c_l: is_contact_limit}, function (
        ret
    ) {
        $(".contact_limit_tip").text(ret);
    });
}

function goodschk() {
    var dprice = parseFloat($("#price").text()) || 0;
    var quantity = parseInt($.trim($("[name=quantity]").val()));
    var rate = parseFloat(
        $(".rate")
            .first()
            .text()
    );
    var tprice = parseFloat(((dprice * quantity) / rate) * 100);

    //判断是否需要买家承担费率
    var feePayer = parseInt($('[name=feePayer]').val()) || 1;
    var feePrice = 0;
    if (2 === feePayer) {
        //加上买方需要承担的费率
        var feeRate = parseFloat($('[name=fee_rate]').val()) || 0;
        feePrice = parseFloat(feeRate * tprice);
        var minPrice = parseFloat($('[name=min_fee]').val()) || 0;
        if (minPrice > feePrice) feePrice = minPrice;
        tprice += feePrice;
    }

    var gprice = parseFloat(dprice * quantity);
    var coupon_ctype = $("[name=coupon_ctype]").val();
    var coupon_value = $("[name=coupon_value]").val();
    if (coupon_ctype == 1) {
        tprice = tprice - coupon_value;
    } else if (coupon_ctype == 100) {
        tprice = parseFloat(tprice - (tprice * coupon_value) / 100);
    }
    var sms_price = $("[name=sms_price]").val();
    tprice = tprice + parseFloat(sms_price);
    tprice = $("#card").attr("checked")
        ? Math.ceil(tprice.toFixed(2))
        : tprice.toFixed(2);
    gprice = $("#card").attr("checked")
        ? Math.ceil(gprice.toFixed(2))
        : gprice.toFixed(2);
    if (2 === feePayer) {
        $('#tfee').remove();
        var node = '<i id="tfee">(含手续费: ' + parseFloat(feePrice).toFixed(2) + '元)</i>';
        $('.tprice').parent().append(node);
    } else {
        $('#tfee').remove();
    }
    $(".tprice").html(tprice);
    $(".gprice").html(gprice);
    $("[name=paymoney]").val(tprice);
}

function getCardLength() {
    $(".paylist ul li").each(function () {
        if (
            $(this)
                .find("input")
                .attr("checked")
        ) {
            pid = $(this)
                .find("input")
                .val();
        }
    });
    $("[name=cardNoLength]").val(0);
    $("[name=cardPwdLength]").val(0);
    $.post(
        "/ajax/getcardlength",
        {channelid: pid},
        function (data) {
            if (data) {
                $("[name=cardNoLength]").val(data.cnl);
                $("[name=cardPwdLength]").val(data.cpl);
            }
        },
        "json"
    );
}

$(function () {
    $("#submit").click(submit);
});

function submit() {
    // if ($('[name=isagree]').is(':checked') == false) {
    //     alert('请阅读和同意用户协议，才能继续购买！');
    //     $('[name=isagree]').focus();
    //     return false;
    // }
    var cateid = $("[name=cateid]").val();
    if (cateid == "") {
        alert("请选择商品分类！");
        $("[name=cateid]").focus();
        return false;
    }
    var goodid = $("[name=goodid]").val();
    if (goodid == "") {
        alert("请选择要购买的商品！");
        $("[name=goodid]").focus();
        return false;
    }
    var quantity = $.trim($("[name=quantity]").val());
    if (isNaN(quantity) || quantity <= 0 || quantity == "") {
        alert("购买数量填写错误，最小数量为1！");
        $("[name=quantity]").focus();
        return false;
    }
    var limit_quantity = $('[name="limit_quantity"]').val();
    if (parseInt(quantity) < parseInt(limit_quantity)) {
        alert("购买数量填写错误，最小数量为" + limit_quantity + "件！");
        $("[name=quantity]").focus();
        return false;
    }
    var kucun = $("[name=kucun]").val();
    kucun = kucun == "" ? 0 : parseInt(kucun);
    if (kucun == 0) {
        alert("库存为空，暂无法购买！");
        $("[name=quantity]").focus();
        return false;
    }
    if (kucun > 0 && quantity > kucun) {
        alert("库存不足，请修改购买数量！");
        $("[name=quantity]").focus();
        return false;
    }
    var is_contact_limit = $('[name="is_contact_limit"]').val();
    var contact = $.trim($("[name=contact]").val());
    var email = $.trim($("[name=email]").val());
    if (contact == "" || contact.length < 6) {
        alert("请填写联系方式，联系方式最短为6位哦！");
        $("[name=contact]").focus();
        return false;
    }
    switch (is_contact_limit) {
        case "qq":
            var reg = /^[\d]+$/;
            if (!reg.test(contact)) {
                alert("联系方式必须QQ！");
                $("[name=contact]").focus();
                return false;
            }
            break;
        case "2":
            var reg = /^[a-zA-Z]+$/;
            if (!reg.test(contact)) {
                alert("联系方式必须全部为英文字母！");
                $("[name=contact]").focus();
                return false;
            }
            break;
        case "3":
            var reg = /^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i;
            if (!reg.test(contact)) {
                alert("联系方式必须为数字和字母的组合！");
                $("[name=contact]").focus();
                return false;
            }
            break;
        case "4":
            var reg = /^(([a-z]+)([_])([a-z]+)|([0-9]+)([_])([0-9]+))$/i;
            if (!reg.test(contact)) {
                alert("联系方式必须有数字和下划红或者字母和下划线组合！");
                $("[name=contact]").focus();
                return false;
            }
            break;
        case "5":
            var reg = /[\u4e00-\u9fa5]/;
            if (!reg.test(contact)) {
                alert("联系方式必须是中文！");
                $("[name=contact]").focus();
                return false;
            }
            break;
        case "email":
            var reg = /^([0-9a-zA-Z_-])+@([0-9a-zA-Z_-])+((\.[0-9a-zA-Z_-]{2,3}){1,2})$/;
            if (!reg.test(email)) {
                alert("联系方式必须是邮箱！");
                $("[name=contact]").focus();
                return false;
            }
            break;
        case "mobile":
            var reg = /^(\d){11}$/;
            if (!reg.test(contact)) {
                alert("联系方式必须为您的手机号码！");
                $("[name=contact]").focus();
                return false;
            }
            break;
    }
    if ($("#pwdforsearch1").is(":visible")) {
        var pwdforsearch = $.trim($("[name=pwdforsearch1]").val());
        var reg = /^([0-9A-Za-z]+){6,20}$/;
        if (pwdforsearch == "" || !reg.test(pwdforsearch)) {
            alert("请填写取卡密码！长度为6-20个数字、字母或组合！");
            $("[name=pwdforsearch1]").focus();
            return false;
        }
    }
    if ($("#is_pwdforsearch").is(":checked")) {
        var pwdforsearch = $.trim($("[name=pwdforsearch2]").val());
        var reg = /^([0-9A-Za-z]+){6,20}$/;
        if (pwdforsearch == "" || !reg.test(pwdforsearch)) {
            alert(
                "您选择了使用取卡密码，请填写取卡密码！长度为6-20个数字、字母或组合！"
            );
            $("[name=pwdforsearch2]").focus();
            return false;
        }
    }
    if ($("#isemail").is(":checked")) {
        if ($.trim($("[name=email]").val()) == "") {
            alert("您选择了把卡密信息发送到邮箱，推荐您填写常用的邮箱哦！");
            $("[name=email]").focus();
            return false;
        }
    }
    if ($("#is_coupon").is(":checked")) {
        if ($.trim($("[name=couponcode]").val()) == "") {
            alert("您选择了使用优惠券，但您没有填写优惠券！");
            $("[name=couponcode]").focus();
            return false;
        }
        var coupon_ctype = $("[name=coupon_ctype]").val();
        if (coupon_ctype == 0) {
            alert("您选择了使用优惠券，但所填写的优惠券无效！");
            $("[name=couponcode]").focus();
            return false;
        }
    }
    var select_pid = false;
    $("[name=pid]").each(function () {
        if ($(this).is(":checked")) {
            select_pid = true;
            if ($(this).data("target") == 1) {
                $(this)
                    .parents("form")
                    .removeAttr("target");
            } else {
                $(this)
                    .parents("form")
                    .attr("target", "_blank");
            }
        }
    });
    if (select_pid == false) {
        alert("请选择支付方式！");
        $("[name=pid]").first();
        return false;
    }
    var pty = "";
    $("#select_pay li").each(function () {
        if (
            $(this)
                .find("input")
                .attr("checked")
        ) {
            pty = $(this)
                .find("input")
                .val();
        }
    });
    var cte = "";
    if (pty == "card") {
        var cardNoLength = $("[name=cardNoLength]").val();
        var cardPwdLength = $("[name=cardPwdLength]").val();
        var i = 1;
        $(".cardvalue").each(function () {
            if ($(this).val() == "") {
                cte = cte + "第" + i + "张卡 请选择充值卡面值!\n";
            }
            i++;
        });
        var i = 1;
        $(".cardnum").each(function () {
            if ($(this).val() == "") {
                cte = cte + "第" + i + "张卡 请填写充值卡号!\n";
            } else {
                var cardno = $(this).val();
                if (
                    cardNoLength != "0" &&
                    cardPwdLength != "0" &&
                    cardNoLength != cardno.length
                ) {
                    cte = cte + "第" + i + "张卡 充值卡号长度为" + cardNoLength + "位!\n";
                }
            }
            i++;
        });
        var i = 1;
        $(".cardpwd").each(function () {
            if ($(this).val() == "") {
                cte = cte + "第" + i + "张卡 请填写充值卡密!\n";
            } else {
                var cardpwd = $(this).val();
                if (
                    cardNoLength != "0" &&
                    cardPwdLength != "0" &&
                    cardPwdLength != cardpwd.length
                ) {
                    cte =
                        cte + "第" + i + "张卡 充值卡密长度为" + cardPwdLength + "位!\n";
                }
            }
            i++;
        });
    }
    if (cte != "") {
        alert(cte);
        return false;
    }

    setTimeout(function () {
        $("#payform").submit();
    }, 500);

    return true;
}

function setFeeRate(rate) {
    console.log(rate);
    $('[name=fee_rate]').val(rate);
    this.goodschk();
}

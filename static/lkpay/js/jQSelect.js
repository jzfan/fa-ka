(function ($) {
    $.fn.jQSelect = function (settings) {
        // alert(settings.id);
        var id = settings.id.toString();
        var $div = this;
        var $cartes = $div.find(".cartes");
        var $lists = $div.find(".lists");

        var listTxt = $cartes.find(".listTxt");
        var listVal = $cartes.find(".listVal");

        var items = $lists.find("ul > li");
        var num = 0;
        $div.click(function (event) {
            if (num % 2 != 0) {
                $div.removeClass("hover");
                num = 0;
            } else {
                $div.addClass("hover");
                num = 1;
                //加背景颜色
                var sval = $.trim($div.find("input[type='text']").val());
                var oLi = $div.find("li");
                oLi.each(function () {
                    if ($.trim($(this).text()) == sval) {
                        $(this).addClass("cgray");
                    }
                });
                //绑定点击事件
                items.click(function () {
                    //listVal.val($(this).attr("id"));
                    $("#" + id + "").val($(this).attr("id"));
                    listTxt.val($(this).text());
                    $div.removeClass("hover");
                    oLi.removeClass("cgray false");
                    num = 0;
                    return false;
                }).mouseover(function () {
                    var cgray = $(this).attr("class");
                    if (cgray) {
                        if (cgray.indexOf("cgray") == -1) {
                            $(this).removeClass("cwhite");
                            $(this).addClass("cgray false");
                        }
                    } else {
                        $(this).removeClass("cwhite");
                        $(this).addClass("cgray false");
                    }
                    

                }).mouseout(function () {
                    var cgray = $(this).attr("class");
                    if (cgray.indexOf("false") != -1) {
                        $(this).removeClass("cgray false");
                        $(this).addClass("cwhite");
                    }
                });
                $(document).mouseup(function (event) {
                    var on = ($div.attr("id") == $(event.target).parents(".selectbox3").attr("id"));
                    if (!on) {
                        num = 0;
                        $div.removeClass("hover");
                    };
                    //if ($(event.target).parents($div.attr("id")).length == 0) { $div.removeClass("hover"); }

                    //event.stopImmediatePropagation();
                    // return on;
                });
            }
            event.stopPropagation();
        });
        //$div.toggle(function () { $(this).addClass("hover"); }, function () { $(this).removeClass("hover"); });



    };
})(jQuery);

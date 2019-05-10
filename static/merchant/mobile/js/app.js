function JumpUrl(url){
	url=url ? url : '/usr';
	window.location.href=url;
}

$(function () {
	$('[data-toggle="tooltip"]').tooltip();

	$('.selectAllCheckbox').click(function(){
		if($(this).prop('checked')){
			$('.checkbox').prop('checked',true);
		} else {
			$('.checkbox').prop('checked',false);
		}
	});

	$('.zclipCopy').zclip({
	  path: '/static/common/ZeroClipboard.swf',
	  copy: function(){
	    return $(this).attr('data');
	  },
	  afterCopy: function(){
	    alert(' 复制成功');
	  }
	});
})

function checkFormIsPwCard(){
	var pwdcard=$.trim($('[name=safepwdforispwcard]').val());
	if(pwdcard=='' || pwdcard.length!=6){
		alert('坐标对应的数字只能是6位数！');
		$('[name=safepwdforispwcard]').focus();
		return false;
	}
	return true;
}

function showContent(title,url){
	$('#waModal').modal('show');
    $('#waModal .modal-title').text(title);
    $.get(url,{t:new Date().getTime()},function(data){
        $('#waModal .modal-body').html(data);
    });
}

$('#goodAdd , #goodEdit').submit(function(){
    var goodname=$.trim($('[name=goodname]').val());
	if(goodname==''){
	    alert('商品名称不能为空！');
		$('[name=goodname]').focus();
		return false;
	};

    var sortid=$.trim($('[name=sortid]').val());
	if(sortid=='' || isNaN(sortid)){
	    alert('分类排序不能为空，格式为整数值！');
		$('[name=sortid]').focus();
		return false;
	};

	var is_pwdforbuy=0;
	$('[name=is_pwdforbuy]').each(function(){
		if($(this).is(':checked')){
			is_pwdforbuy=$(this).val();
		}
	});

	if(is_pwdforbuy==1){
		var pwdforbuy=$.trim($('[name=pwdforbuy]').val());
		var reg=/^([a-z0-9A-Z]+){6,20}$/;
		if(pwdforbuy!='' && !reg.test(pwdforbuy)){
			alert('密码长度在6-20位之间，只能包含大小写字母、数字或组合！');
			$('[name=pwdforbuy]').focus();
			return false;
		}
	}
});

var add_discount=function(){
	if($('tr#is_discount_desc table tr').length>=12){
	    alert('最多添加10个优惠区间');
		return false;
	};
    $('tr#add_button').before('<tr><td><img src="/static/usr/default/images/ico_del.png" onclick="del_discount(this)" align="absmiddle" title="移除" /></td><td><div class="input-group"><span class="input-group-addon">大于</span><input type="text" name="dis_quantity[]" class="form-control" value=""><span class="input-group-addon">张</span></div></td><td><div class="input-group"><input type="text" class="form-control" name="dis_price[]" value="" /><span class="input-group-addon">元</span></div></td></tr>');
};

var del_discount=function(id){
    $(id).parent().parent().remove();
};

var hideMsg=function(){
	$('#tipMsg').hide();
}

var page_jump=function(url){
    var page=$('[name=page_options]').val();
	window.location.href=url+page;
};

<?php /*a:3:{s:68:"/www/wwwroot/yueling/application/admin/view/product/product_add.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/header.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/footer.html";i:1567821037;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo env('app.name'); ?></title>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="Bookmark" href="/favicon.ico">
    <link rel="Shortcut Icon" href="/favicon.ico"/>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/static/admin/lib/html5shiv.js"></script>
    <script type="text/javascript" src="/static/admin/lib/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="/static/js/h-ui/css/H-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="/static/js/h-ui.admin/css/H-ui.admin.css"/>
    <link rel="stylesheet" type="text/css" href="/static/js/Hui-iconfont/1.0.8/iconfont.css"/>
    <link rel="stylesheet" type="text/css" href="/static/js/h-ui.admin/skin/default/skin.css" id="skin"/>
    <link rel="stylesheet" type="text/css" href="/static/js/h-ui.admin/css/style.css"/>
    <!--[if IE 6]>
    <script type="text/javascript" src="/static/js/DD_belatedPNG_0.0.8a-min.js"></script>
    <script>DD_belatedPNG.fix('*');</script>


    <![endif]-->
</head>
<style>
    /* 你自己的样式 */
    .page {
        float: right;
        margin: 20px 0px;
    }

    .page li {
        float: left;
    }

    /*.page li a:hover{background:#5a98de;}*/
    .page li a:hover {
        background: #5a98de;
    }

    .page a {
        display: inline-block;
        padding: 2px 10px;
        border: 1px #cccccc solid;
        margin-left: 10px;
    }

    .page .active {
        display: inline-block;
        padding: 2px 10px;
        border: 1px #cccccc solid;
        margin-left: 10px;
        background: #5a98de;
        color: #fff;
    }

    .page .disabled {
        display: inline-block;
        padding: 2px 10px;
        border: 1px #cccccc solid;
        margin-left: 10px;
    }
</style>
<body>


<style>
	.withm{width: 50%}
	.selectwith{width: 30%}
	.otherwith{width: 20%}
	.upload-img{position:relative;}
	.upload-img input{opacity: 0;border: 1px solid red;width: 177px;height: 164px;position: absolute;z-index: 1;}
</style>
<script type="text/javascript" src="/static/ueditor/1.4.3/ueditor.config.js"></script>
<script type="text/javascript" src="/static/ueditor/1.4.3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="/static/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
<article class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-add">
		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2">
				<span class="c-red">*</span>产品图片：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<div class="upload-img">
					<input id="img-upload" type="file" name="img"  accept="image/*">
					<img id="img-show" src="<?php echo htmlentities($thumb); ?>" class="img-polaroid"  width="200" height="130" alt="上传图片" title="上传图片">
					图片大小：750*487
				</div>
			</div>
			<div class="col-4"> </div>
		</div>
		<br>
		<br>
		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2"><span class="c-red">*</span>产品名称：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text withm" value="<?php echo htmlentities($name); ?>"   placeholder="" id="name" name="name"  datatype="*" nullmsg="请填写产品名称">
			</div>
			<div class="col-4"> </div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2">
				<span class="c-red">*</span>短简介：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text"  name="short_name" placeholder="" value="<?php echo htmlentities($short_name); ?>" datatype="*" nullmsg="请填写商品短名称" maxlength="30">
			</div>
			<div class="col-4"> </div>
		</div>


		<div id="sel"></div>
		<div id="sel2"></div>
		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2">
				<span class="c-red">*</span>商品价格：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text selectwith"  name="shop_price" placeholder="" value="<?php echo htmlentities($shop_price); ?>" datatype="*" nullmsg="请填写商品价格">
				<!--<select name="pay_type" datatype="*"  class="input-text otherwith">-->
					<!--&lt;!&ndash;<option value="1" <?php if($pay_type == 1): ?>selected<?php endif; ?>>佣金</option>&ndash;&gt;-->
					<!--&lt;!&ndash;<option value="2" <?php if($pay_type == 2): ?>selected<?php endif; ?>>积分</option>&ndash;&gt;-->
				<!--</select>-->
				积分
			</div>
			<div class="col-4"> </div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2">
				<span class="c-red">*</span>库存数量：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text withm"  name="stock" placeholder="" value="<?php echo htmlentities($stock); ?>" datatype="*" nullmsg="请填写库存数量">
			</div>
			<div class="col-4"> </div>
		</div>
		<!--<div class="row cl">-->
			<!--<label class="form-label col-xs-2 col-sm-2">-->
				<!--<span class="c-red">*</span>运费：</label>-->
			<!--<div class="formControls col-xs-8 col-sm-9">-->
				<!--<input type="text" class="input-text withm"  name="freight" placeholder="" value="<?php echo htmlentities($freight); ?>" datatype="*" nullmsg="请填写运费,为0则包邮">-->
			<!--</div>-->
			<!--<div class="col-4"> </div>-->
		<!--</div>-->
		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2">
				<span class="c-red">*</span>是否上架：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<select name="is_sale" datatype="*"  class="input-text selectwith">
					<option value="1" <?php if($is_sale == 1): ?>selected<?php endif; ?>>上架</option>
					<option value="0" <?php if($is_sale == 0): ?>selected<?php endif; ?>>下架</option>
				</select>
			</div>
			<div class="col-4"> </div>
		</div>
		<!--<div class="row cl">-->
			<!--<label class="form-label col-xs-2 col-sm-2">-->
				<!--<span class="c-red">*</span>支付类型：</label>-->
			<!--<div class="formControls col-xs-8 col-sm-9">-->
				<!--<select name="pay_type" datatype="*"  class="input-text selectwith">-->
					<!--<option value="1" <?php if($pay_type == 1): ?>selected<?php endif; ?>>佣金</option>-->
					<!--<option value="2" <?php if($pay_type == 2): ?>selected<?php endif; ?>>积分</option>-->
				<!--</select>-->
			<!--</div>-->
			<!--<div class="col-4"> </div>-->
		<!--</div>-->

		<div class="row cl">
			<label class="form-label col-xs-2 col-sm-2">
				<span class="c-red">*</span>产品详情：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<script id="editor" type="text/plain" style="width:100%;height:400px;"><?php echo htmlspecialchars_decode($content); ?></script>
			</div>
			<div class="col-4"> </div>
		</div>

		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<input type="hidden" value="<?php echo htmlentities($id); ?>" name="id">
				<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
			</div>
		</div>
	</form>
</article>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/static/js/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/static/js/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="/static/js/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/static/js/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/datatables/1.10.15/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/laypage/1.2/laypage.js"></script>

<script type="text/javascript" src="/static/js/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/static/js/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/static/js/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript">
    function edit(title, url) {
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }

    function del(url, id) {
        layer.confirm('是否确认进行删除操作？', function (index) {
            $.post(url, {id: id}, function (data) {
                if (data.status == 0) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function(){
                        location.replace(location.href)
                    });
                } else {
                    layer.msg(data.msg, {icon: 2, time: 1000});
                }
            }, 'json');
        });
    }
</script>

<script type="text/javascript">
    function new_open(title, url, w, h) {
        layer_show(title, url, w, h);
    }


    /*-删除*/
    function data_del(obj, url, id) {
        layer.confirm('确认要删除吗？', function (index) {
            $.post(url, {id: id}, function (data) {
                if (data.status == 0) {
                    $(obj).parents("tr").remove();
                    $("#count").text($("#count").text() - 1);
                    layer.msg(data.info, {icon: 1, time: 1000});
                } else {
                    layer.msg(data.info, {icon: 2, time: 1000});
                }
            }, 'json');
        });
    }

    //批量删除
    function datadel(url) {
        var id = '';
        $("input[name='del[]']:checked").each(function () {
            id += $(this).val()   + ',';
        });

        if (id == '') {
            layer.msg('必须选择!', {icon: 2, time: 1000});
            return false;
        }
        layer.confirm('确认要删除吗？', function (index) {
            $.post(url, {id: id}, function (data) {
                if (data.status == 0) {
                    $("input[name='del[]']:checked").each(function () {
                        $(this).parents('tr').remove();
                    });
                    //数量赋值
                    $("#count").text(data.count);
                    layer.msg(data.info, {icon: 1, time: 1000});
                } else {
                    layer.msg(data.info, {icon: 2, time: 1000});
                }
            });
        });
    }

    /*显示隐藏切换*/
    function toggle_status(obj,id,status,table,field){
        var news_status = status == 1 ? 0 : 1;
        if(typeof(field) == 'undefined'){
            field = 'is_show';
        }
        $.post("<?php echo url('Base/toggle_status'); ?>",{id:id,status:news_status,table,field:field},function(data){
            if(data == 1){
                layer.msg('操作成功!',{icon:6,time:1000});
                if(news_status == 0){
                    src="/static/Admin/static/h-ui/images/validform/iconpic-right.png";
                }else{
                    src="/static/Admin/static/h-ui/images/validform/iconpic-error.png";
                }
                $(obj).attr('src',src).attr('onclick',"toggle_status(this,"+id+","+news_status+",'"+table+"','"+field+"')");
            }else{
                layer.msg('操作失败了!',{icon:5,time:1000});
            }
        });
    }
    function tog_status(obj,id,status,table,field){
        var news_status = status == 1 ? 0 : 1;
        if(typeof(field) == 'undefined'){
            field = 'is_show';
        }
        $.post("<?php echo url('Base/toggle_status'); ?>",{id:id,status:news_status,table,field:field},function(data){
            if(data == 1){
                layer.msg('操作成功!',{icon:6,time:1000});
                if(news_status == 1){
                    src="/static/Admin/static/h-ui/images/validform/iconpic-error.png";
                }else{
                    src="/static/Admin/static/h-ui/images/validform/iconpic-right.png";
                }
                $(obj).attr('src',src).attr('onclick',"toggle_status(this,"+id+","+news_status+",'"+table+"','"+field+"')");
            }else{
                layer.msg('操作失败了!',{icon:5,time:1000});
            }
        });
    }

    function showimg(imgs){
        layer.open({
            type: 1,
            title: false,
            area: ['450px', '450px'],
            shadeClose: true,
            skin: 'layui-layer-nobg', //没有背景色
            content: "<img src='"+imgs+"' style='width: 450px;height: 450px;;'>"
        });
    }

    function showother(info,name){
        layer.open({
            type: 1,
            title: false,
            area: ['400px', '400px'],
            shadeClose: true,
            skin: 'layui-layer-nobg', //没有背景色
            content: "<div style='width: 360px;height: 90%;background-color: #ffffff;padding: 20px'>"+name+"：<br>　　"+info+"</div>"
        });
    }

    $(function(){
        $("#form-add").validate({
            rules:{
                username:{
                    required:true,
                    maxlength:16
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    success:function(data){
                        console.log(data);
                        if(data.status == 'y'){
                            layer.msg("操作成功",{icon:1,time:1000,shade: [0.3, '#000']},function(){
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.location.reload();
                                parent.layer.close(index);
                            });
                        }else{
                            layer.msg(data.info,{icon:2,time:1000,shade: [0.3, '#000']});
                        }
                    },
                    error:function(error){
                        layer.msg('系统发生了错误',{icon:2,time:3000,shade: [0.5, '#FF0000']});
                    }
                });

            }
        });
    });
</script>
</body>
</html>



<script type="text/javascript">
	function gradeChange(type){
		$.post("/product/catlist",{cat_id:type},function(data){
			if(data.info.length > 0){
				var str = '';
				for(var i = 0; i< data.info.length; i++){
					str += '<option value="'+data.info[i]['cat_id']+'">'+data.info[i]['cat_name']+'</option> ';
				}
				var sem =
						'<div class="row cl"> ' +
						'<label class="form-label col-xs-2 col-sm-2"> ' +
						'<span class="c-red">*</span>级别：</label> ' +
						'<div class="formControls col-xs-8 col-sm-9"> ' +
						'<select name="jibie" datatype="*"  class="input-text selectwith" onchange="gradeChange2(this.value)"> ' +
						'<option value="">=选择级别分类=</option> ' + str+
						'</select>' +
						'</div> ' +
						'<div class="col-4"> </div> ' +
						'</div>';
				$("#sel").html(sem);
			}

		});
	}
	function gradeChange2(type){
		$.post("/product/catlist",{cat_id:type},function(data){
			if(data.info.length > 0){
				var str = '';
				for(var i = 0; i< data.info.length; i++){
					str += '<option value="'+data.info[i]['cat_id']+'">'+data.info[i]['cat_name']+'</option> ';
				}
				var sem =
						'<div class="row cl"> ' +
						'<label class="form-label col-xs-2 col-sm-2"> ' +
						'<span class="c-red">*</span>分类：</label> ' +
						'<div class="formControls col-xs-8 col-sm-9"> ' +
						'<select name="cat_id" datatype="*"  class="input-text selectwith" > ' +
						'<option value="">=选择产品分类=</option> ' + str+
						'</select>' +
						'</div> ' +
						'<div class="col-4"> </div> ' +
						'</div>';
				$("#sel2").html(sem);
			}

		});
	}

	$(function(){
		//选择框
		$('.skin-minimal input').iCheck({
			checkboxClass: 'icheckbox-blue',
			radioClass: 'iradio-blue',
			increaseArea: '20%'
		});
//内容框name设置
		var ue = UE.getEditor('editor',{'textarea':'content'});

		$("#form-member-add").Validform({
			tiptype:1,
			ajaxPost:true,
			callback:function(data){
				setTimeout(function(){
					$.Hidemsg();
					if(data.status == 'y'){
						var index = parent.layer.getFrameIndex(window.name);
						parent.location.reload();
						parent.layer.close(index);
					}
				},1000);
			}
		});
	});
		$('#img-upload').change(function(e){
			var input = $("#img-upload");
			var file = input[0].files[0];//获取input上传的文件
			if(!file.name){
				alert("未选择图片");
			}else{
				//高版本浏览器对文件上传路径进行了安全处理，无法直接通过获取input的value进行访问，故转化为获取图片的url进行安全访问
				var url = window.URL.createObjectURL(file);//将上传的文件转化为url
				$("#img-show").attr('src', url);//更新img的src属性
			};
		});
</script>
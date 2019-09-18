<?php /*a:3:{s:60:"/www/wwwroot/yueling/application/admin/view/system/kefu.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/header.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/footer.html";i:1567821037;}*/ ?>
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


<style type="text/css">
.portfolio-area li .portfoliobox{height: 200px;}
</style>
<link href="/static/Admin/lib/lightbox2/2.8.1/css/lightbox.css" rel="stylesheet" type="text/css" >
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 图片管理 <span class="c-gray en">&gt;</span> 图片展示 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="cl pd-5 bg-1 bk-gray mt-20"> 
		<span class="l">

			<a href="javascript:;" onclick="member_add('添加图片','/system/kefu_add?type=index')" class="btn btn-primary radius">
			<i class="Hui-iconfont">&#xe625;</i> 添加类型</a>

			<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius">
			<i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> 
		</span> 
		<!--<span class="r">共有支付：<strong id="count"><?php echo count($list); ?></strong> 位</span> </div>-->
		<span class="r"></span> </div>
	<div class="portfolio-content">
		<ul class="cl portfolio-area">
			<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($v['type'] == 3): ?>
					<li class="item" >
						<div class="portfoliobox">
							<a style="text-decoration:none" class="ml-5" onClick="member_del(this,<?php echo htmlentities($v['id']); ?>)" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
							<span class="label label-success radius">银行卡</span><br><br>
							<span>开户名：<p  style="font-size: 12px"><?php echo htmlentities($v['name']); ?></p></span>
							<span>卡　号：<p  style="font-size: 12px"><?php echo htmlentities($v['yh']); ?></p></span>
							<span>开户行：<p  style="font-size: 12px"><?php echo htmlentities($v['kh']); ?></p></span>
							<input class="checkbox" name="del[]" type="checkbox" value="<?php echo htmlentities($v['id']); ?>">

						</div>
					</li>
				<?php else: if($v['type'] == 4): ?>
				<li class="item" >
					<div class="portfoliobox">
						<a style="text-decoration:none" class="ml-5" onClick="member_del(this,<?php echo htmlentities($v['id']); ?>)" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
						<span class="label label-success radius">微信客服</span><br><br>
						<span>账号：<p  style="font-size: 12px"><?php echo htmlentities($v['title']); ?></p></span>
						<input class="checkbox" name="del[]" type="checkbox" value="<?php echo htmlentities($v['id']); ?>">
						<!--<div class="picbox">-->
							<!--<a href="<?php echo htmlentities($v['thumb']); ?>" data-lightbox="gallery" data-title="">-->
								<!--<img src="<?php echo htmlentities($v['thumb']); ?>" style="height: 150px;">-->
							<!--</a>-->
						<!--</div>-->
					</div>
				</li>
				<?php else: if($v['type'] == 5): ?>
					<li class="item" >
						<div class="portfoliobox" >
							<a style="text-decoration:none" class="ml-5" onClick="member_del(this,<?php echo htmlentities($v['id']); ?>)" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
							<span class="label label-success radius">QQ客服</span><br><br>
							<span>账号：<p  style="font-size: 12px"><?php echo htmlentities($v['title']); ?></p></span>
							<input class="checkbox" name="del[]" type="checkbox" value="<?php echo htmlentities($v['id']); ?>">
							<!--<div class="picbox"><a href="<?php echo htmlentities($v['thumb']); ?>" data-lightbox="gallery" data-title="">-->
								<!--<img src="<?php echo htmlentities($v['thumb']); ?>"  style="height: 150px;"></a></div>-->
						<!--</div>-->
					</li>
					<?php else: ?>
					<li class="item">
						<div class="portfoliobox">
							<a style="text-decoration:none" class="ml-5" onClick="member_del(this,<?php echo htmlentities($v['id']); ?>)" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
							<span class="label label-success radius"><?php if($v['type']==1){echo '微信';}elseif($v['type']==2){echo '支付宝';}elseif($v['type']==3){echo '银行卡';}elseif($v['type']==4){echo "QQ群客服";}elseif($v['type']==6){echo "微信公众号";}elseif($v['type']==5){echo "充值中心";} ?></span>
							<span><?php echo htmlentities($v['title']); ?></span>
							<input class="checkbox" name="del[]" type="checkbox" value="<?php echo htmlentities($v['id']); ?>">
							<div class="picbox"><a href="<?php echo htmlentities($v['thumb']); ?>" data-lightbox="gallery" data-title="">
								<img src="<?php echo htmlentities($v['thumb']); ?>"></a></div>
						</div>
					</li>
					<?php endif; ?>
				<?php endif; ?>

				<?php endif; ?>

			<?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</div>
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


<script type="text/javascript" src="/static/Admin/lib/lightbox2/2.8.1/js/lightbox-plus-jquery.min.js"></script> 
<script type="text/javascript">
$(function(){
	$.Huihover(".portfolio-area li");
});
/*添加-相册*/
function member_add(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.post("/system/kefu_del",{id:id},function(data){
			if(data.status == 'y'){
				layer.msg(data.info,{icon:1,time:1000});
				$(obj).parents("li").remove();
				$("#count").text(data.count);
			}else{
				layer.msg('删除失败',{icon:2,time:1000});
			}
		},'json');
	});
}
/* 批量删除 */
function datadel(){
 var id='';	
 $("input[name='del[]']:checked").each(function(){
 	id += $(this).val()+',';
 });

 if(id == ''){
 	layer.msg('必须选择一个哦!',{icon:2,time:1000});
 	return false;
 }
 layer.confirm('确认要删除吗？',function(index){
 	$.post("/system/kefu_del",{id:id},function(data){
			if(data.status == 'y'){
				$("input[name='del[]']:checked").each(function(){
					//删除选中的
 					$(this).parents('li').remove();
 					//children的长度
					// for(var i=0;i<data.children.length;i++){
					// 	$("#tr"+data.children[i]).remove();
					// }
 				});
 				//数量赋值
				$("#count").text(data.count);
				layer.msg(data.info,{icon:1,time:1000});
			}else{
				layer.msg(data.info,{icon:2,time:1000});
			}
	});
 });
}
</script>
</body>
</html>
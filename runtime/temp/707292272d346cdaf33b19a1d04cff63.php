<?php /*a:3:{s:67:"/www/wwwroot/yueling/application/admin/view/order/receipt_list.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/header.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/footer.html";i:1567821037;}*/ ?>
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


<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 交易订单 <span class="c-gray en">&gt;</span> 发单列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<form action="" method="get">
		<div class="text-c">
			<span class="select-box inline">
				<select id="types" name="type" class="select">
					<option value="0">=订单类型=</option>
					<?php foreach($info as $f): ?>
						<option value="<?php echo htmlentities($f['type']); ?>" <?php if(input('get.type') == $f['type']): ?>selected<?php endif; ?> ><?php echo htmlentities($f['name']); ?></option>
					<?php endforeach; ?>
				</select>
            </span>
			日期范围：
			<input type="text" onfocus="WdatePicker({lang:'zh-tw'})" value="<?php echo input('get.start'); ?>" name="start" id="datemin" class="input-text Wdate" style="width:120px;" readonly> -
			<input type="text" onfocus="WdatePicker({lang:'zh-tw'})" value="<?php echo input('get.end'); ?>" name="end" id="datemin" class="input-text Wdate" style="width:120px;" readonly>
			<input type="text" class="input-text" style="width:250px" placeholder="用户id|订单号" value="<?php echo input('get.word'); ?>" name="word">
			<button type="submit" class="btn btn-success radius"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
		</div>
	</form>

	<div class="cl pd-5 bg-1 bk-gray mt-20">
		<!--<span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a href="javascript:;" onclick="product_add('添加产品','<?php echo url('product_add'); ?>')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加产品</a></span> -->
	<span class="r">共有数据：<strong id="count"><?php echo htmlentities($count); ?></strong> 条</span> </div>
	<div class="mt-20">
	<table class="table table-border table-bordered table-hover table-bg table-sort">
		<thead>
			<tr class="text-c">
				<!--<th width="80">ID</th>-->
				<th width="80">订单号</th>
				<th width="50">用户id</th>
				<th width="50">介绍</th>
				<th width="50">语音</th>
				<th width="100">图片介绍<br>[点击放大]</th>
				<th width="50">服务类型 </th>
				<th width="80">服务费用</th>
				<th width="120">服务时间</th>
				<th width="120">地点</th>
				<th width="50">接单确认距离</th>
				<th width="130">状态</th>
				<th width="80">约定服务</th>
				<th width="100">添加时间</th>
				<th width="30">编辑</th>
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
				<tr class="text-c" id="tr<?php echo htmlentities($v['id']); ?>">
					<td><?php echo htmlentities($v['order_on']); ?></td>
					<td><?php echo htmlentities($v['user_id']); ?></td>
					<td><?php echo htmlentities($v['introduce']); ?></td>
					<td>
						<!--<audio controls height="50" width="50">-->
							<!--<source src="<?php echo htmlentities($v['voice']); ?>" type="audio/ogg">-->
							<!--<embed height="50" width="50" src="<?php echo htmlentities($v['voice']); ?>">-->
						<!--</audio>-->
						<div onclick="play('<?php echo htmlentities($v['voice']); ?>')">
							<img src="/void/image_file.jpg" style="width: 25px;height: 25px;">
						</div>
					</td>
					<td>
						<?php foreach($v['img'] as $key=> $item): ?>
							<img src="<?php echo htmlentities($item); ?>" style="width: 25px;height: 25px;" onclick="showimg('<?php echo htmlentities($item); ?>')">
						<?php endforeach; ?>

					</td>
					<td><?php echo htmlentities($v['service_type']); ?></td>
					<td>
						<span style="float: left;">诚意金:</span><span style="float: left;"><?php echo floatval($v['earnestmoney']); ?></span><br>
						<span style="float: left;">线下服务:</span><span style="float: left;"><?php echo floatval($v['unline_service']); ?>/<?php echo unittype($v['unline_unit']); ?></span><br>
						<span style="float: left;">电话服务:</span><span style="float: left;"><?php echo floatval($v['phone_service']); ?>/<?php echo unittype($v['phone_unit']); ?></span><br>
						<span style="float: left;">远程服务:</span><span style="float: left;"><?php echo floatval($v['longrange_service']); ?>/<?php echo unittype($v['longrange_unit']); ?></span>
					</td>

					<td><?php echo htmlentities($v['start_time']); ?>
					</td>
					<td><?php echo htmlentities($v['address']); ?></td>
					<td><?php echo htmlentities($v['jwd']); ?></td>
					<td>
						<?php if($v['status'] == 6): ?>
						状态：<span style="color: red"><?php echo receipttype($v['status']); ?></span><br>
						用户id：<?php echo htmlentities($v['appeal']); ?><br>
						反馈：<?php echo htmlentities($v['reason']); else: ?>
						<?php echo receipttype($v['status']); ?>
						<?php endif; ?>


					</td>
					<td>
						<?php if($v['s_type'] != 0): ?>
						<span style="float: left;"><?php echo htmlentities($v['last_service_type']); ?>:</span><span style="float: left;"><?php echo floatval($v['last_service_price']); ?>/<?php echo unittype($v['last_service_unit']); ?></span><br>
						<span style="float: left;">服务次数:</span><span style="float: left;"><?php echo htmlentities($v['service_time']); ?></span><br>
						<span style="float: left;">成交金额:</span><span style="float: left;"><?php echo floatval($v['pay_money']); ?></span><br>
						<span style="float: left;">接单用户:</span><span style="float: left;"><?php echo htmlentities($v['accept_id']); ?></span><br>
						<span style="float: left;">接单状态:</span><span style="float: left;"><?php echo receipttype($v['accept_status']); ?></span><br>
							<?php if(floatval($v['accept_money']) != 0): ?>
								<span style="float: left;">接单收益:</span><span style="float: left;"><?php echo floatval($v['accept_money']); ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td><?php echo htmlentities($v['add_time']); ?></td>
					<td>
						<?php if($v['status'] == 6): ?>
						<a class="btn btn-success radius" onclick="dealorder(<?php echo htmlentities($v['id']); ?>)"> 待处理</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
	<div class="page text-r mt-10">
		<?php echo htmlentities($page); ?>
	</div>
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


<script type="text/javascript" src="/mar/BenzAMRRecorder.js"></script>
<script type="text/javascript">
function play(voie){
	var amr = new BenzAMRRecorder();
	amr.initWithUrl(voie).then(function() {
		amr.play();
	});
	amr.onEnded(function() {
		layer.msg('播放完毕',{icon:1,time:3000});
	})
}

/*产品-添加*/
function member_add(title,url,w,h){
    layer_show(title,url,w,h);
}
function product_add(title,url,w,h){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}


function dealorder(id){
	layer.confirm('是否直接完成订单？',function(index){
		$.post("<?php echo url('dealorder'); ?>",{id:id},function(data){
			if(data.status == 'y'){
				layer.msg(data.info,{icon:1,time:1000,shade: [0.3, '#000']},function(){
					parent.location.reload();
				});
			}else{
				layer.msg(data.info,{icon:2,time:1000});
			}
		},'json');
	});
}

//批量删除
function datadel(){
 var id='';
 $("input[name='del[]']:checked").each(function(){
 	id += $(this).val()+',';
 });

 if(id == ''){
 	layer.msg('还没选择!',{icon:2,time:1000});
 	return false;
 }
 layer.confirm('确认要删除吗？',function(index){
 	$.post("<?php echo url('product_del'); ?>",{id:id},function(data){
		if(data.status == 'y'){
			//循环当前选择的框
			$("input[name='del[]']:checked").each(function(){
				//删除当前选择的框
				$(this).parents('tr').remove();
			});
			layer.msg(data.info,{icon:1,time:1000});
			$("#count").text(data.count);
		}else{
			layer.msg(data.info,{icon:2,time:1000});
		}
	});
 });
}
/*产品-相册*/
function article_edit(title,url,id,w,h){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}
</script>
</body>
</html>
<?php /*a:3:{s:66:"/www/wwwroot/yueling/application/admin/view/user/user_company.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/header.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/footer.html";i:1567821037;}*/ ?>
<script src="../../../../../../vue/h5/src/router/routers.js"></script><!DOCTYPE HTML>
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



<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户管理 <span class="c-gray en">&gt;</span> 认证列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    <form action="" method="get">
        <div class="text-c">
            <span class="select-box inline">
            <select name="status" class="select">
                <option value="">=状态=</option>
                <?php foreach($info as $f): ?>
						<option value="<?php echo htmlentities($f['type']); ?>" <?php if(input('get.status') == $f['type']): ?>selected<?php endif; ?> ><?php echo htmlentities($f['name']); ?></option>
                <?php endforeach; ?>
            </select>
            </span>
            &nbsp;日期范围：
            <input type="text" onfocus="WdatePicker()" readonly name="start" value="<?php echo input('get.start'); ?>" class="input-text Wdate" style="width:120px;">
            -
            <input type="text" onfocus="WdatePicker()" readonly name="end" value="<?php echo input('get.start'); ?>" class="input-text Wdate" style="width:120px;">
            <input type="text" class="input-text" style="width:250px" placeholder="用户ID"
                   value="<?php echo input('word'); ?>" name="word">
            <button type="submit" class="btn btn-success radius"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
        </div>
    </form>
    <div class="cl pd-5 bg-1 bk-gray mt-20">

        <span class="r">共有数据：<strong id="count"><?php echo htmlentities($list->count()); ?></strong> 条</span> </div>
    <div class="mt-20">

        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="50">用户ID</th>
                <th width="80">用户名</th>
                <th width="80">手机号</th>
                <th width="100">营业执照<br>[点击放大]</th>
                <th width="100">法人身份证<br>[点击放大]</th>
                <th width="100">公司照片<br>[点击放大]</th>
                <th width="100">申请时间</th>
                <th width="60">状态</th>
                <th width="120">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <tr class="text-c" id="tr<?php echo htmlentities($v['user_id']); ?>">
                <td><?php echo htmlentities($v['user_id']); ?></td>
                <td><?php echo htmlentities($v['username']); ?></td>
                <td><?php echo htmlentities($v['phone']); ?></td>
                <td style="padding: 0"><img src="<?php echo htmlentities($v['license']); ?>" style="width: 55px;height: 70px;" onclick="showimgmm('<?php echo htmlentities($v['license']); ?>')"></td>
                <td style="padding: 0"><img src="<?php echo htmlentities($v['legalperson']); ?>" style="width: 55px;height: 70px;" onclick="showimgmm('<?php echo htmlentities($v['legalperson']); ?>')"></td>
                <td style="padding: 0"><img src="<?php echo htmlentities($v['company']); ?>" style="width: 55px;height: 70px;" onclick="showimgmm('<?php echo htmlentities($v['company']); ?>')"></td>
                <td><?php echo htmlentities($v['created']); ?></td>
                <td>
                    <?php switch($v['status']): case "0": ?> <span class="label label-default radius">未审核</span> <?php break; case "1": ?> <span class="label label-primary radius">已认证</span> <?php break; case "2": ?> <span class="label label-default radius">已驳回</span> <?php break; ?>
                    <?php endswitch; ?>
                </td>
                <td class="td-manage">
                    <?php switch($v['status']): case "0": ?>
                    <a style="text-decoration:none" class="btn btn-primary ml-5" onclick="member_sta(this,<?php echo htmlentities($v['user_id']); ?>,1)"  href="javascript:void(0);" title="通过"><i class="Hui-iconfont">&#xe615;</i></a>
                    <a style="text-decoration:none" class="btn btn-warning ml-5" onclick="member_sta(this,<?php echo htmlentities($v['user_id']); ?>,2)" title="驳回"><i class="Hui-iconfont">&#xe60b;</i></a>

                     <?php break; ?>
                    <?php endswitch; ?>
                    <a style="text-decoration:none" class="btn btn-danger ml-5" onclick="member_del(this,<?php echo htmlentities($v['user_id']); ?>)" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>

                </td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>

        <div class="page">
            <?php echo htmlspecialchars_decode($list->render()); ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    /*放大图片*/

    function member_img(title,url,w,h){
        layer_show(title,url,w,h);
    }

    /*产品-删除*/
    function member_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.post("/user/user_company_del",{id:id},function(data){
                if(data.status == 'y'){
                    layer.msg(data.info,{icon:1,time:3000});
                    $(obj).parents("tr").remove();
                    $("#count").text(data.count);
                }else{
                    layer.msg(data.info,{icon:2,time:3000});
                }
            },'json');
        });
    }


    function member_sta(obj,id,status){
        layer.confirm('确认要修改吗？',function(index){
            $.post("<?php echo url('user_company_sta'); ?>",{id:id,status:status},function(data){
                layer.msg(data.info,{icon:1,time:1000});
                window.location.reload();
            },'json');
        });
    }

    function show_address(info){
        layer.msg(info,{icon:2,time:1000});
    }

    function showimgmm(imgs){
        layer.open({
            type: 1,
            title: false,
            area: ['450px', '600px'],
            shadeClose: true,
            skin: 'layui-layer-nobg', //没有背景色
            content: "<img src='"+imgs+"' style='width: 450px;height: 600px;'>"
        });
    }
</script>
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


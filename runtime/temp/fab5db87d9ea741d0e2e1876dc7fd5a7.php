<?php /*a:3:{s:62:"/www/wwwroot/yueling/application/admin/view/user/user_add.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/header.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/footer.html";i:1567821037;}*/ ?>
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



<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="/select/dist/formSelects-v4.css"/>

<article class="page-container">
    <form action="" method="post" class="form form-horizontal" id="form-add">

        <div class="row cl">
            <label class="form-label col-xs-2 col-sm-2"><span class="c-red">*</span>昵称：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities((isset($row['name']) && ($row['name'] !== '')?$row['name']:'')); ?>" placeholder="" id="name" name="name">
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-2 col-sm-2">
                <span class="c-red">*</span>手机号：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" name="mobile" placeholder="" value="<?php echo htmlentities((isset($row['mobile']) && ($row['mobile'] !== '')?$row['mobile']:'')); ?>" datatype="*" nullmsg="请填写手机号">
            </div>
            <div class="col-4"> </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-2 col-sm-2">
                用户密码：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" name="password"  value="" <?php if(isset($row['id']) && $row['id'] > 0): ?>placeholder="填写为修改"<?php else: ?>datatype="*" placeholder="password" nullmsg="请输入交易密码"<?php endif; ?>>
            </div>
            <div class="col-4"> </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-2 col-sm-2">
                交易密码：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" name="trade"  value="" <?php if(isset($row['id']) && $row['id'] > 0): ?>placeholder="填写为修改"<?php else: ?>datatype="*" placeholder="password" nullmsg="请输入交易密码"<?php endif; ?>>
            </div>
            <div class="col-4"> </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-2 col-sm-2">区域代理：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="layui-form">
                    <select name="agent_id" xm-select="select13" xm-select-search="" xm-select-radio="">
                        <option value="">=选择代理区域=</option>
                        <?php foreach($agent as $v): ?>
                        <option value="<?php echo htmlentities($v['id']); ?>" <?php echo $row['agent_id']==$v['id'] ? 'selected' : ''; ?>><?php echo str_repeat('►', $v['level']); ?>&nbsp;<?php echo htmlentities($v['cityname']); switch($v['level']): case "0": ?> <span class="label label-default radius">----取消代理</span> <?php break; case "1": ?> <span class="label label-default radius">----省代</span> <?php break; case "2": ?> <span class="label label-primary radius">----市代</span> <?php break; case "3": ?> <span class="label label-default radius">----县/区代</span> <?php break; ?>
                        <?php endswitch; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-4"> </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-2 col-sm-2"><span class="c-red">*</span>登陆密码：</label>
            <div class="formControls col-xs-8 col-sm-9 selectwith">
                <input type="password" class="input-text" name="passwords" value="" placeholder="管理员登陆密码">
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <input type="hidden" value="<?php echo htmlentities((isset($row['id']) && ($row['id'] !== '')?$row['id']:'')); ?>" name="id">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
            </div>
        </div>
    </form>
</article>
<script src="/static/js/jquery/1.9.1/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="/select/dist/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    formSelects.render('selectId');
</script>

<link rel="stylesheet" type="text/css" href="/select/dist/formSelects-v4.css"/>

<!-- 引入jquery依赖 -->
<script src="/static/js/jquery/1.9.1/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<!-- 引入组件 -->
<script src="/select/dist/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>


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

$(function(){
    var count = 0;
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
                    if(data.status == 'y'){
                        layer.msg("操作成功",{icon:1,time:6000,shade: [0.3, '#000']},function(){
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.location.reload();
                            parent.layer.close(index);
                        });
                    }else{
                        layer.msg(data.info,{icon:2,time:6000,shade: [0.3, '#000']});
                    }
                    count = 0;
                },
                error:function(error){
                      layer.msg('系统发生了错误',{icon:2,time:6000,shade: [0.5, '#FF0000']});
                }
            });

        }
    });
});
</script> 

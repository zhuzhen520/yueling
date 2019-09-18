<?php /*a:1:{s:60:"/www/wwwroot/yueling/application/admin/view/index/login.html";i:1567821037;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?php echo env('app.name'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/login.css"/>
    <script type="text/javascript" src="/static/js/jquery/1.9.1/jquery.js"></script>
    <script type="text/javascript" src="/static/js/h-ui/js/H-ui.min.js"></script>
    <script type="text/javascript" src="/static/js/layer/2.4/layer.js"></script>

</head>

<body>
<div class="bg1"></div>
<div class="gyl">
    <?php echo env('app.name'); ?><p style="color: azure">温馨提示：请不要在浏览器保存账号密码</p>
</div>

<div class="bg">
    <div class="wel">登录</div>
    <div class="user">
        <div class="yonghu" style="">用户名</div>
        <input type="text" name="username" value="<?php echo htmlentities((isset($data['username']) && ($data['username'] !== '')?$data['username']: '')); ?>" placeholder="请输入用户名"/>
    </div>
    <div class="password">
        <div class="yonghu">密&nbsp;&nbsp;&nbsp;码</div>
        <input class="" type="password" name="password" value="<?php echo htmlentities((isset($data['password']) && ($data['password'] !== '')?$data['password']: '')); ?>" placeholder="请输入密码"/>
    </div>
    <div class="vili" style="margin-top: 50px; margin-left: 40px">
        <input class="" style="width: 50%;" type="text" name="code" value="" placeholder="输入验证码"/>
        &nbsp;
        <img id="code" style="width: 50%; height: 90%;margin-top: -7px;" src="<?php echo captcha_src(); ?>" onclick=" $(this).attr('src', $(this).attr('src') + '?' + Math.random());">
    </div>
    <input class="btn" type="button" onclick="login()" name="登录" value="登录"/>
</div>
</body>
</html>
<script type="text/javascript">
    $(document).keyup(function (event) {
        if (event.keyCode === 13) {
            login();
        }
    });

    function login() {
        layer.msg('正在登陆中...', {icon: 16, shade: [0.3, '#000']});
        var username = $("input[name='username']").val();
        var password = $("input[name='password']").val();
        var code = $("input[name='code']").val();
        if (!(code && password && username)) {
            layer.msg('请填写正确的信息',{icon:2,time:1000,shade: [0.3, '#000']});
            return;
        }

        $.post("<?php echo url('login'); ?>", {username : username, password: password, code : code}, function(data) {
            if (data.status == 0) {
                layer.msg(data.msg, {icon: 16, time: 1000, shade: [0.3, '#000']}, function () {
                    window.location.href = data.url;
                });
            } else {
                layer.msg(data.msg, {icon: 2, time: 1300, shade: [0.3, '#000']}, function () {
                    $("#code").attr('src', $("#code").attr('src') + '?' + Math.random());
                    $("input[name='code']").val('');
                    $("input[name='password']").val('')
                });
            }

        }, 'json')
    }
</script>
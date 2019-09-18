<?php /*a:2:{s:62:"/www/wwwroot/yueling/application/admin/view/system/config.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/footer.html";i:1567821037;}*/ ?>
﻿<!--_meta 作为公共模版分离出去-->
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="Bookmark" href="/favicon.ico" >
<link rel="Shortcut Icon" href="/favicon.ico" />
<!--[if lt IE 9]>
<script type="text/javascript" src="/static/other/lib/html5shiv.js"></script>
<script type="text/javascript" src="/static/other/lib/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="/static/other/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/static/other/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/static/other/lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/static/other/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/static/other/static/h-ui.admin/css/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="/static/other/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<!--/meta 作为公共模版分离出去-->
<style>
	.withm{width: 50%}
	.selectwith{width: 30%}
	.upload-img{position:relative;}
	.upload-img input{opacity: 0;border: 1px solid red;width: 177px;height: 164px;position: absolute;z-index: 1;}
</style>
<title>基本设置</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页
	<span class="c-gray en">&gt;</span>
	系统管理
	<span class="c-gray en">&gt;</span>
	基本设置
	<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a>
</nav>
<div class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-add">
		<div id="tab-system" class="HuiTab">
			<div class="tabBar cl">
				<span>基本设置</span>
				<!--<span>短信签名</span>-->
				<span>提现</span>
				<!--<span>团队奖励</span>-->
				<span>发单管理</span>
				<span>会员相关</span>
				<span>代理商</span>
				<span>资讯背景</span>
			</div>
			<div class="tabCon">
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">基本设置</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">
						<span class="c-red">*</span>
						版本：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="base[version]" id="version" placeholder="请填写数字或小数" value="<?php echo htmlentities($base['version']); ?>" class="input-text">
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">
						<span class="c-red">*</span>
						苹果安装包下载地址：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="base[apk]" id="apk" placeholder="苹果安装包下载地址" value="<?php echo htmlentities($base['apk']); ?>" class="input-text">
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">
						<span class="c-red">*</span>
						安卓安装包下载地址：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="base[apk1]" id="apk1" placeholder="安卓安装包下载地址" value="<?php echo htmlentities($base['apk1']); ?>" class="input-text">
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">
						<span class="c-red">*</span>
						身份认证收费：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" style="width: 8%;" name="user[identmoney]" id="apk1" placeholder="身份认证收费" value="<?php echo htmlentities($user['identmoney']); ?>" class="input-text">元
					</div>
				</div>
			</div>

			<!--<div class="tabCon">-->
				<!--<div class="row cl">-->
					<!--<div class="formControls col-5">-->
						<!--<label class="form-label col-4"><span class="label label-primary radius">短信签名</span></label>-->
					<!--</div>-->
				<!--</div>-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">短信签名：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="base[msgname]" id="msgname" placeholder="短信签名" value="<?php echo htmlentities($base['msgname']); ?>" class="input-text">-->
					<!--</div>-->
				<!--</div>-->
			<!--</div>-->


			<div class="tabCon">
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">提现倍数限制：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[multiple]" style="width: 200px;" id="usdt" placeholder="提现倍数(请输入数字)" value="<?php echo htmlentities($user['multiple']); ?>" class="input-text">的倍数
					</div>
				</div>
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">余额</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">提现手续费：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="base[usdt]" style="width: 100px;" id="usdt" placeholder="提现手续费" value="<?php echo htmlentities($base['usdt']); ?>" class="input-text">%
					</div>
				</div>

				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">豆豆</span></label>
					</div>
				</div>

				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">兑换余额：</label>
					<div class="formControls col-xs-8 col-sm-9">
						最少拥有
						<input type="text" name="user[minbean]" style="width: 200px;" id="usdt" placeholder="豆豆数量(请输入数字)" value="<?php echo htmlentities($user['minbean']); ?>" class="input-text">
						豆才可兑换余额
					</div>
				</div>

			</div>

			<!--<div class="tabCon">-->
				<!--<div class="row cl">-->
					<!--<div class="formControls col-5">-->
						<!--<label class="form-label col-4"><span class="label label-primary radius">团队奖励</span></label>-->
					<!--</div>-->
				<!--</div>-->

				<!--&lt;!&ndash;<div class="row cl">&ndash;&gt;-->
					<!--&lt;!&ndash;<label class="form-label col-xs-4 col-sm-2">店长人数：</label>&ndash;&gt;-->
					<!--&lt;!&ndash;<div class="formControls col-xs-8 col-sm-9">&ndash;&gt;-->
						<!--&lt;!&ndash;<input type="text" name="team[shopkeeper]" style="width: 100px;" id="shopkeeper" placeholder="个体团可设置店长数量" value="<?php echo htmlentities($team['shopkeeper']); ?>" class="input-text">人/总店长只有一人,多出人数全是副店长&ndash;&gt;-->
					<!--&lt;!&ndash;</div>&ndash;&gt;-->
				<!--&lt;!&ndash;</div>&ndash;&gt;-->
				<!--&lt;!&ndash;<div class="row cl">&ndash;&gt;-->
					<!--&lt;!&ndash;<label class="form-label col-xs-4 col-sm-2">店总人数：</label>&ndash;&gt;-->
					<!--&lt;!&ndash;<div class="formControls col-xs-8 col-sm-9">&ndash;&gt;-->
						<!--&lt;!&ndash;<input type="text" name="team[shopnum]" style="width: 100px;" id="shopnum" placeholder="团队可容纳人数(函店长)" value="<?php echo htmlentities($team['shopnum']); ?>" class="input-text">人&ndash;&gt;-->
					<!--&lt;!&ndash;</div>&ndash;&gt;-->
				<!--&lt;!&ndash;</div>&ndash;&gt;-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">直推奖励：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="team[zhiper]" style="width: 100px;" id="shopnum" placeholder="直推奖励" value="<?php echo htmlentities($team['zhiper']); ?>" class="input-text">%-->
					<!--</div>-->
				<!--</div>-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">店长奖励：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="team[aper]" style="width: 100px;" id="aper" placeholder="店长奖励" value="<?php echo htmlentities($team['aper']); ?>" class="input-text">%-->
					<!--</div>-->
				<!--</div>-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">副店长奖励：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="team[bper]" style="width: 100px;" id="bper" placeholder="店长奖励" value="<?php echo htmlentities($team['bper']); ?>" class="input-text">%-->
					<!--</div>-->
				<!--</div>-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">上级店长奖励：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="team[upaper]" style="width: 100px;" id="upaper" placeholder="上级店长奖励" value="<?php echo htmlentities($team['upaper']); ?>" class="input-text">%-->
					<!--</div>-->
				<!--</div>-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">上级副店长奖励：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="team[upbper]" style="width: 100px;" id="upbper" placeholder="上级副店长奖励" value="<?php echo htmlentities($team['upbper']); ?>" class="input-text">%-->
					<!--</div>-->
				<!--</div>-->
			<!--</div>-->

			<div class="tabCon">
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">发单管理</span></label>
					</div>
				</div>
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">最底发单：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="base[fadannum]" style="width: 200px;" id="usdt" placeholder="发单最底金额" value="<?php echo htmlentities($base['fadannum']); ?>" class="input-text">-->
					<!--</div>-->
				<!--</div>-->
				<!--<div class="row cl">-->
					<!--<label class="form-label col-xs-4 col-sm-2">用户保证金：</label>-->
					<!--<div class="formControls col-xs-8 col-sm-9">-->
						<!--<input type="text" name="base[bzjnum]" style="width: 200px;" id="usdt" placeholder="用户保证金" value="<?php echo htmlentities($base['bzjnum']); ?>" class="input-text">  元-->
					<!--</div>-->
				<!--</div>-->
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">非会员限制：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="base[nomessage]" style="width: 200px;" id="usdt" placeholder="非会员限制回复数量" value="<?php echo htmlentities($base['nomessage']); ?>" class="input-text"> 句
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">取消扣除：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="base[fzper]" style="width: 200px;" id="usdt" placeholder="取消扣除保证金" value="<?php echo htmlentities($base['fzper']); ?>" class="input-text">  %
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">直接约诚意金：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[cyj]" style="width: 200px;" id="usdt" placeholder="取消扣除保证金" value="<?php echo htmlentities($user['cyj']); ?>" class="input-text">
					</div>
				</div>

			</div>
			<div class="tabCon">
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">会员奖励</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">会员价格：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[vipmoney]" style="width: 200px;" id="usdt" placeholder="会员收费价格(请输入数字)" value="<?php echo htmlentities($user['vipmoney']); ?>" class="input-text"> 元
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">返推广豆：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[bean]" style="width: 200px;" id="usdt" placeholder="购买会员奖励推广豆(请输入数字)" value="<?php echo htmlentities($user['bean']); ?>" class="input-text"> 豆
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">直推会员奖励：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[zhibean]" style="width: 200px;" id="usdt" placeholder="直推奖励推广豆(请输入数字)" value="<?php echo htmlentities($user['zhibean']); ?>" class="input-text"> 豆
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">间推会员奖励：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[jianbean]" style="width: 200px;" id="usdt" placeholder="直推奖励推广豆(请输入数字)" value="<?php echo htmlentities($user['jianbean']); ?>" class="input-text"> 豆
					</div>
				</div>
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">会员制度</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">注：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<span style="color: red">会员发展/订单收益均只对公司额定会员用户有效!</span>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">公司额定：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[reward_num]" style="width: 100px;" id="usdt" placeholder="有奖会员数量 (请输入数字)" value="<?php echo htmlentities($user['reward_num']); ?>" class="input-text"> 会员奖励
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">会员发展：</label>
					<div class="formControls col-xs-8 col-sm-9">
						直接发展大于等于
						<input type="text" name="user[zhinum]" style="width: 100px;" id="usdt" placeholder="直推发展人数(请输入数字)" value="<?php echo htmlentities($user['zhinum']); ?>" class="input-text">
						人,
						并且直推+间接发展大于等于
						<input type="text" name="user[jiannum]" style="width: 100px;" id="usdt" placeholder="直推+间推发展人数(请输入数字)" value="<?php echo htmlentities($user['jiannum']); ?>" class="input-text">
						人，
						将来
						<input type="text" name="user[year]" style="width: 100px;" id="usdt" placeholder="会员年限(请输入数字)" value="<?php echo htmlentities($user['year']); ?>" class="input-text">

						年期间免费使用软件
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">平台税收：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="user[revenue]" style="width: 100px;" id="usdt" placeholder="平台税收 (请输入数字)" value="<?php echo htmlentities($user['revenue']); ?>" class="input-text"> %
						注：后期商家正式上线运营后，官方收取接单收益佣金
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">订单收益：</label>
					<div class="formControls col-xs-8 col-sm-9">
						会员直推下级大于等于
						<input type="text" name="user[zhiuser]" style="width: 100px;" id="usdt" placeholder="会员直推下级人数 (请输入数字)" value="<?php echo htmlentities($user['zhiuser']); ?>" class="input-text">
						人,均成为会员后，<br>除了返佣提成及享受平台软件接发单服务外，
						还可享受其直推会员所接单交易后，商家平台利润中的
						<input type="text" name="user[zhiuserpro]" style="width: 100px;" id="usdt" placeholder="会员利润 (请输入数字)" value="<?php echo htmlentities($user['zhiuserpro']); ?>" class="input-text"> %
						作为提成
					</div>
				</div>


			</div>


			<div class="tabCon">
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">代理商数量</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">省代数量：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="team[province_num]" style="width: 100px;" id="usdt" placeholder="省代代理商数量" value="<?php echo htmlentities($team['province_num']); ?>" class="input-text">个
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">市代数量：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="team[city_num]" style="width: 100px;" id="usdt" placeholder="县代代理商数量" value="<?php echo htmlentities($team['city_num']); ?>" class="input-text">个
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">县/区代数量：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" name="team[area_num]" style="width: 100px;" id="usdt" placeholder="区代代理商数量" value="<?php echo htmlentities($team['area_num']); ?>" class="input-text">个
					</div>
				</div>
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">代理商收益(%)</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">省代收益：</label>
					<div class="formControls col-xs-8 col-sm-9">
						平台收益的
						<input type="text" name="team[province_pro]" style="width: 100px;" id="usdt" placeholder="省代代理商收益" value="<?php echo htmlentities($team['province_pro']); ?>" class="input-text">%
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">县代收益：</label>
					<div class="formControls col-xs-8 col-sm-9">
						平台收益的
						<input type="text" name="team[city_pro]" style="width: 100px;" id="usdt" placeholder="县代代理商收益" value="<?php echo htmlentities($team['city_pro']); ?>" class="input-text">%
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">区代收益：</label>
					<div class="formControls col-xs-8 col-sm-9">
						平台收益的
						<input type="text" name="team[area_pro]" style="width: 100px;" id="usdt" placeholder="县代代理商收益" value="<?php echo htmlentities($team['area_pro']); ?>" class="input-text">%
					</div>
				</div>


			</div>
			<div class="tabCon">
				<div class="row cl">
					<div class="formControls col-5">
						<label class="form-label col-4"><span class="label label-primary radius">资讯背景</span></label>
					</div>
				</div>
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-2">图片：</label>
					<div class="formControls col-xs-8 col-sm-9">

							<div class="col-sm-10">
								<a  id="test1" style="width: 750px;height: 280px;" >
									<input type="hidden" name="base[backimg]"  id="backimg"  value="<?php echo htmlentities($base['backimg']); ?>">
									<img src="<?php if(empty($base['backimg'])): ?>/uploads/img/uploads.jpg<?php else: ?><?php echo htmlentities($base['backimg']); ?><?php endif; ?>" name="base[backimg]" id="bki" style="width: 750px;height: 280px;"  >
								</a>
							</div>
 					</div>
				</div>


			</div>
		</div>


		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button onClick="article_save_submit();" class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
 			</div>
		</div>
	</form>
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


<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/static/other/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/static/other/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/static/other/static/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="/static/other/static/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/static/other/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/static/other/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/static/other/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/static/other/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script src="/static/layui/layui.js"></script>
<script type="text/javascript">

$(function(){
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
	$("#tab-system").Huitab({
		index:0
	});
});
function checkboxOnclick(checkbox){

	if ( checkbox.checked == true){
		$('#opendown').val(0);
	}else{
		$('#opendown').val(1);
	}

}
</script>

<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>
<script type="text/javascript">
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
			$('#backimg').val(url);
		};
	});
</script>
<script>
	layui.use('upload', function(){
		var upload = layui.upload;
		//执行实例
		var uploadInst = upload.render({
			elem: '#test1' //绑定元素
			,url: "<?php echo url('/Upload/upload'); ?>" //上传接口
			,done: function(res){
				$("#bki").attr("src",res.info);
				$("#backimg").val(res.info);
			}
			,error: function(){
				//请求异常回调
			}
		});
	});
</script>

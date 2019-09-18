<?php /*a:2:{s:61:"/www/wwwroot/yueling/application/admin/view/main/welcome.html";i:1567821037;s:62:"/www/wwwroot/yueling/application/admin/view/public/header.html";i:1567821037;}*/ ?>
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


<div class="pd-20" style="padding-top:20px;">
    <p class="f-20 text-success">欢迎使用后台管理系统！</p>
    <p>登录次数：<?php echo htmlentities($admin['login_num']); ?> </p>
    <p>上次登录IP：<?php echo htmlentities($admin['login_ip']); ?> 最近登录时间：<?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($admin['updated'])? strtotime($admin['updated']) : $admin['updated'])); ?></p>
    <!--<div class="page-container">-->
    <!--<div id="doubleline" style="min-width:700px;height:300px"></div>-->
    <!--</div>-->
    <!--<br>-->
    <div style="width: 100%; height: 310px; background-color: #dddddd; padding: 0">
        <!--折线图-->
        <div class="page-container" style="width: 49%;height: 300px;float: left; padding: 0;">
            <div id="container" style="min-width:550px;height:300px"></div>
        </div>
        <!--时间轴拆线图-->
        <div class="page-container" style="width: 49%;height: 300px;float: right; padding: 0;">
            <!--<div id="timeonline" style="min-width:550px;height:300px"></div>-->
            <div id="doubleline" style="min-width:550px;height:300px"></div>
        </div>
    </div>

    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr>
            <th colspan="6" scope="col">信息统计</th>
        </tr>
        <tr class="text-c">
            <th>统计</th>
            <th>余额</th>
            <th>豆豆</th>
            <th>提现手续费</th>
            <th>积分商城成交量</th>
            <th>会员数量</th>
        </tr>
        </thead>
        <tbody>
        <tr class="text-c">
            <td>总数</td>
            <td><?php echo htmlentities($row['money']); ?></td>
            <td><?php echo htmlentities($row['td_money']); ?></td>
            <td><?php echo htmlentities($row['tx_money']); ?></td>
            <td><?php echo htmlentities($row['shop_num']); ?></td>
            <td><?php echo htmlentities($row['user']); ?></td>
         </tr>
        </tbody>
    </table>

    <br>


</div>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/static/huiadmin/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/static/huiadmin/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/static/huiadmin/static/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="/static/huiadmin/static/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/static/huiadmin/lib/hcharts/Highcharts/5.0.6/js/highcharts.js"></script>
<script type="text/javascript" src="/static/huiadmin/lib/hcharts/Highcharts/5.0.6/js/modules/exporting.js"></script>
<script type="text/javascript">
    //折线图
    $(function () {
        $.ajax({
            url:"<?php echo url('infoData'); ?>",
            type:'POST',
            dataType:'json',
            data:{},
            success:function(data){
                Highcharts.chart('container', {
                    title: {
                        text: data.info.line_name,
                        x: -20 //center
                    },
                    xAxis: {
                        categories: data.info.x_axis
                    },
                    yAxis: {
                        title: {
                            text: data.info.y_name
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    //线条
                    series: [{
                        name: data.info.info1.name,
                        data: data.info.info1.num
                    },{
                        name: data.info.info2.name,
                        data: data.info.info2.num
                    },
                    // {
                    //     name: data.info.info3.name,
                    //     data: data.info.info3.num
                    // },{
                    //     name: data.info.info4.name,
                    //     data: data.info.info4.num
                    // }
                    ]
                });
            }
        });
        //时间轴拆线图
        $.ajax({
            url:"<?php echo url('statisticInfo'); ?>",
            type:'POST',
            dataType:'json',
            data:{},
            success:function(data){
                $('#doubleline').highcharts({
                    chart: {
                        type: 'area'
                    },
                    title: {
                        text:  data.info.line_name,
                    },
                    xAxis: {
                        categories:  data.info.x_axis
                    },

                    yAxis: {
                        title: {
                            text:data.info.y_name
                        },
                        labels: {
                            formatter: function() {
                                return this.value;
                            }
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y:,.0f}</b>'
                    },
                    plotOptions: {
                        area: {
                            marker: {
                                enabled: false,
                                symbol: 'circle',
                                radius: 2,
                                states: {
                                    hover: {
                                        enabled: true
                                    }
                                }
                            }
                        }
                    },
                    //线条
                    series: [
                        {
                            name: data.info.info1.name,
                            data: data.info.info1.num
                        },
                        {
                            name: data.info.info2.name,
                            data: data.info.info2.num
                        }
                    ]
                });
            }
        });



    });
</script>

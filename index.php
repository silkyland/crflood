<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>crflood</title>

    <!-- Bootstrap -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- font awesome -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <link href="bower_components/highcharts/css/highcharts.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<?php
$json = file_get_contents('http://www.crflood.com/data-service/data-level-now.php?s=stg01');
$obj = json_decode($json);
?>
<div class="container">
    <div class="row" style="margin-top: 40px">
        <div class="col-md-10">
            <h1><?php echo $obj->name ?></h1>

        </div>
        <div class="col-md-1 text-right">
            <i class="fa fa-4x
            <?php
            if ($obj->battery[0] == 0) {
                echo 'fa-battery-0 text-danger';
            } elseif ($obj->battery[0] <= 25) {
                echo 'fa-battery-1 text-warning';
            } elseif ($obj->battery[0] <= 50) {
                echo 'fa-battery-2 text-success';
            } elseif ($obj->battery[0] <= 75) {
                echo 'fa-battery-3 text-success';
            } else {
                echo 'fa-battery-4 text-success';
            }

            ?>" aria-hidden="true"></i>
        </div>
        <div class="col-md-1">
            <h3 class="
            <?php
            if ($obj->battery[0] == 0) {
                echo 'text-danger';
            } elseif ($obj->battery[0] <= 25) {
                echo 'text-warning';
            } elseif ($obj->battery[0] <= 50) {
                echo 'text-success';
            } elseif ($obj->battery[0] <= 75) {
                echo 'text-success';
            } else {
                echo 'text-success';
            } ?>
            ">
                <?php echo $obj->battery[0], '%' ?>
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <small>
                <i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $obj->stime ?>
            </small>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>
                <small><?php echo
                        $obj->location . ', ' .
                        $obj->sb_name . ' ตำบล' .
                        $obj->tb_name . ' อำเภอ' .
                        $obj->amp_name
                    ?></small>
                <a target="_blank" class="text-danger"
                   href="https://www.google.co.th/maps/place/<?php echo $obj->geo_location->lat . ',' . $obj->geo_location->long ?>">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                </a>
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="badge">
                        <?php echo $obj->temperature[0] . ' °C' ?></span>
                    <i class="fa fa-thermometer-0 text-danger" aria-hidden="true"></i> อุณหภูมิ
                </li>
                <li class="list-group-item">
                    <span class="badge">
                        <?php echo $obj->humidity[0] ?></span>
                    <i class="fa fa-snowflake-o text-info" aria-hidden="true"></i> ความชื่น
                </li>
                <li class="list-group-item">
                    <span class="badge">
                        <?php echo $obj->rain[0] ?></span>
                    <i class="fa fa-cloud text-info" aria-hidden="true"></i> ฝน
                </li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="badge">
                        <?php echo $obj->water_sealevel[0] ?></span>
                    <i class="fa fa-area-chart text-warning" aria-hidden="true"></i> ระดับน้ำทะเล
                </li>
                <li class="list-group-item">
                    <span class="badge">
                        <?php echo $obj->tropical_level[0] ?></span>
                    <i class="fa fa-tachometer" aria-hidden="true"></i> ระดับความร้อน
                </li>
                <li class="list-group-item">
                    <span class="badge">
                        <?php echo $obj->bank_level[0] ?></span>
                    <i class="fa fa-mixcloud text-info" aria-hidden="true"></i> ระดับการกักเก็บ
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="myChartWarningLevel"></div>
        </div>
        <div class="col-md-6">
            <div id="myChartCriticalLevel"></div>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="bower_components/highcharts/js/highcharts.js"></script>
<script src="bower_components/highcharts/js/highcharts-more.js"></script>
<script src="bower_components/highcharts/js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; max-width: 400px; height: 300px; margin: 0 auto"></div>

<script>

    $(function () {

        Highcharts.chart('myChartWarningLevel', {

                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },

                title: {
                    text: 'ระดับเตือนภัย'
                },

                pane: {
                    startAngle: -150,
                    endAngle: 150,
                    background: [{
                        backgroundColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, '#FFF'],
                                [1, '#333']
                            ]
                        },
                        borderWidth: 0,
                        outerRadius: '109%'
                    }, {
                        backgroundColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, '#333'],
                                [1, '#FFF']
                            ]
                        },
                        borderWidth: 1,
                        outerRadius: '107%'
                    }, {
                        // default background
                    }, {
                        backgroundColor: '#DDD',
                        borderWidth: 0,
                        outerRadius: '105%',
                        innerRadius: '103%'
                    }]
                },

                // the value axis
                yAxis: {
                    min: 0,
                    max: 1000,

                    minorTickInterval: 'auto',
                    minorTickWidth: 1,
                    minorTickLength: 10,
                    minorTickPosition: 'inside',
                    minorTickColor: '#666',

                    tickPixelInterval: 30,
                    tickWidth: 2,
                    tickPosition: 'inside',
                    tickLength: 10,
                    tickColor: '#666',
                    labels: {
                        step: 2,
                        rotation: 'auto'
                    },
                    title: {
                        text: 'หน่วย'
                    },
                    plotBands: [{
                        from: 0,
                        to: 600,
                        color: '#55BF3B' // green
                    }, {
                        from: 600,
                        to: 700,
                        color: '#DDDF0D' // yellow
                    }, {
                        from: 700,
                        to: 1000,
                        color: '#DF5353' // red
                    }]
                },

                series: [{
                    name: 'ระดับ',
                    data: [<?php echo $obj->warning_level[0] ?>],
                    tooltip: {
                        valueSuffix: ' หน่วย'
                    }
                }]

            },
            // Add some life
            function (chart) {
                if (!chart.renderer.forExport) {
                    setInterval(function () {
                        var point = chart.series[0].points[0],
                            newVal;

                        newVal = Math.round(<?php echo $obj->warning_level[0] ?>);
                        point.update(newVal);

                    }, 3000);
                }
            });
    });

    $(function () {

        Highcharts.chart('myChartCriticalLevel', {

                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },

                title: {
                    text: 'ระดับวิกฤต'
                },

                pane: {
                    startAngle: -150,
                    endAngle: 150,
                    background: [{
                        backgroundColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, '#FFF'],
                                [1, '#333']
                            ]
                        },
                        borderWidth: 0,
                        outerRadius: '109%'
                    }, {
                        backgroundColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, '#333'],
                                [1, '#FFF']
                            ]
                        },
                        borderWidth: 1,
                        outerRadius: '107%'
                    }, {
                        // default background
                    }, {
                        backgroundColor: '#DDD',
                        borderWidth: 0,
                        outerRadius: '105%',
                        innerRadius: '103%'
                    }]
                },

                // the value axis
                yAxis: {
                    min: 0,
                    max: 1000,

                    minorTickInterval: 'auto',
                    minorTickWidth: 1,
                    minorTickLength: 10,
                    minorTickPosition: 'inside',
                    minorTickColor: '#666',

                    tickPixelInterval: 30,
                    tickWidth: 2,
                    tickPosition: 'inside',
                    tickLength: 10,
                    tickColor: '#666',
                    labels: {
                        step: 2,
                        rotation: 'auto'
                    },
                    title: {
                        text: 'หน่วย'
                    },
                    plotBands: [{
                        from: 0,
                        to: 120,
                        color: '#55BF3B' // green
                    }, {
                        from: 120,
                        to: 160,
                        color: '#DDDF0D' // yellow
                    }, {
                        from: 160,
                        to: 200,
                        color: '#DF5353' // red
                    }]
                },

                series: [{
                    name: 'ระดับ',
                    data: [<?php echo $obj->warning_level[0] ?>],
                    tooltip: {
                        valueSuffix: ' หน่วย'
                    }
                }]

            },
            // Add some life
            function (chart) {
                if (!chart.renderer.forExport) {
                    setInterval(function () {
                        var point = chart.series[0].points[0],
                            newVal;

                        newVal = Math.round(<?php echo $obj->critical_level[0] ?>);
                        point.update(newVal);

                    }, 3000);
                }
            });
    });

</script>
</body>
</html>
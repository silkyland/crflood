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
            <canvas id="myChartWarningLevel"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="myChartCriticalLevel"></canvas>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="bower_components/chart.js/dist/Chart.min.js"></script>

<script>
    var count = 1;
    var runDateTime;
    var ctx = document.getElementById("myChartWarningLevel");
    var myChartWarningLevel = new Chart.Line(ctx, {
        type: 'bar',
        data: {
            labels: [
                <?php echo
                '"' . date('H:i:s') . '",'; ?>],
            datasets: [{
                label: 'ระดับเตือนภัย',
                data: [
                    <?php echo
                    $obj->warning_level[0] . ',';?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    var ctx1 = document.getElementById("myChartCriticalLevel");
    var myChartCriticalLevel = new Chart.Line(ctx1, {
        type: 'bar',
        data: {
            labels: [
                <?php echo
                '"' . date('H:i:s') . '",'; ?>],
            datasets: [{
                label: 'ระดับวิกฤต',
                data: [
                    <?php echo
                    $obj->critical_level[0] . ',';?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    $(function () {
        var nowDateTime = new Date("<?php echo date("m/d/Y H:i:s") ?>");
        var d = nowDateTime.getTime();
        var mkHour, mkMinute, mkSecond;
        setInterval(function () {
            d = parseInt(d) + 1000;
            var nowDateTime = new Date(d);
            mkHour = new String(nowDateTime.getHours());
            if (mkHour.length == 1) {
                mkHour = "0" + mkHour;
            }
            mkMinute = new String(nowDateTime.getMinutes());
            if (mkMinute.length == 1) {
                mkMinute = "0" + mkMinute;
            }
            mkSecond = new String(nowDateTime.getSeconds());
            if (mkSecond.length == 1) {
                mkSecond = "0" + mkSecond;
            }
            runDateTime = mkHour + ":" + mkMinute + ":" + mkSecond;
        }, 1000);

    });

    $(function () {
        updateDataChart();
    });

    function updateDataChart() {
        setTimeout(function () {
            if (count < 6) {
                myChartWarningLevel.data.datasets[0].data[count] = <?php echo $obj->warning_level[0] ?>;
                myChartWarningLevel.data.labels[count] = runDateTime;
                myChartWarningLevel.update();

                myChartCriticalLevel.data.datasets[0].data[count] = <?php echo $obj->critical_level[0] ?>;
                myChartCriticalLevel.data.labels[count] = runDateTime;
                myChartCriticalLevel.update();

                count++;
                updateDataChart();
            } else {
                myChartWarningLevel.data.datasets[0].data[0] = myChartWarningLevel.data.datasets[0].data[1];
                myChartWarningLevel.data.datasets[0].data[1] = myChartWarningLevel.data.datasets[0].data[2];
                myChartWarningLevel.data.datasets[0].data[2] = myChartWarningLevel.data.datasets[0].data[3];
                myChartWarningLevel.data.datasets[0].data[3] = myChartWarningLevel.data.datasets[0].data[4];
                myChartWarningLevel.data.datasets[0].data[4] = myChartWarningLevel.data.datasets[0].data[5];
                myChartWarningLevel.data.datasets[0].data[5] = <?php echo $obj->critical_level[0] ?>;
                myChartWarningLevel.data.labels[0] = myChartWarningLevel.data.labels[1];
                myChartWarningLevel.data.labels[1] = myChartWarningLevel.data.labels[2];
                myChartWarningLevel.data.labels[2] = myChartWarningLevel.data.labels[3];
                myChartWarningLevel.data.labels[3] = myChartWarningLevel.data.labels[4];
                myChartWarningLevel.data.labels[4] = myChartWarningLevel.data.labels[5];
                myChartWarningLevel.data.labels[5] = runDateTime;

                myChartCriticalLevel.data.datasets[0].data[0] = myChartCriticalLevel.data.datasets[0].data[1];
                myChartCriticalLevel.data.datasets[0].data[1] = myChartCriticalLevel.data.datasets[0].data[2];
                myChartCriticalLevel.data.datasets[0].data[2] = myChartCriticalLevel.data.datasets[0].data[3];
                myChartCriticalLevel.data.datasets[0].data[3] = myChartCriticalLevel.data.datasets[0].data[4];
                myChartCriticalLevel.data.datasets[0].data[4] = myChartCriticalLevel.data.datasets[0].data[5];
                myChartCriticalLevel.data.datasets[0].data[5] = <?php echo $obj->critical_level[0] ?>;
                myChartCriticalLevel.data.labels[0] = myChartCriticalLevel.data.labels[1];
                myChartCriticalLevel.data.labels[1] = myChartCriticalLevel.data.labels[2];
                myChartCriticalLevel.data.labels[2] = myChartCriticalLevel.data.labels[3];
                myChartCriticalLevel.data.labels[3] = myChartCriticalLevel.data.labels[4];
                myChartCriticalLevel.data.labels[4] = myChartCriticalLevel.data.labels[5];
                myChartCriticalLevel.data.labels[5] = runDateTime;

                myChartWarningLevel.update();
                myChartCriticalLevel.update();
                updateDataChart();
            }
        }, 2000);
    }
    ;

</script>
</body>
</html>
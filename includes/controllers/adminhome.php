<?php
/**
* admin / home
* az admin controllerbe van includolva
*/
$interval = $this->request->input('interval','week');
$base = $this->request->input('base', time());
$receptLabels = $this->buildLabels($interval, $base);
$receptDatas = $this->model->getReceptDatas($interval,$base);
$visitDatas = $this->model->getVisitDatas($interval,$base);
$loginDatas = $this->model->getLoginDatas($interval,$base);
?>
<div class="col-md-6">
    <div class="adminBox adminBox0">
        <h2>Látogatottság és login statisztika</h2>
        <canvas id="myChart1" width="400" height="300"></canvas>
        <p>
        <?php $this->echoIcons('visit',$interval,$base); ?>
        </p>
    </div>    
</div>
<div class="col-md-6">
    <div class="adminBox adminBox5">
        <h2>Új recept statisztika</h2>
        <canvas id="myChart2" width="400" height="300"></canvas>
        <p>
            <?php $this->echoIcons('recept',$interval,$base); ?>
        </p>
    </div>    
</div>
<script>
    window.setTimeout(function() {
            var data = {
                labels: <?php echo JSON_encode($receptLabels); ?>,
                datasets: [{data: <?php echo JSON_encode($visitDatas); ?>,
                           label:'látogatás', 
                           backgroundColor:'red', borderColor:'red'},
                           {data: <?php echo JSON_encode($loginDatas); ?>, 
                            label:'bejelentkezés',
                            backgroundColor:'green', borderColor:'green'},
                ]
            };   
            var ctx = document.getElementById('myChart1').getContext('2d');
            const config1 = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: ''
                    }
                    }
                },
            };
            const myChart1 = new Chart(ctx, config1);

            var data = {
                labels: <?php echo JSON_encode($receptLabels); ?>,
                datasets: [{data: <?php echo JSON_encode($receptDatas); ?>,
                           label:'Recept', 
                           backgroundColor:'black', borderColor:'black'}]
            };   
            var ctx = document.getElementById('myChart2').getContext('2d');
            const config2 = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: ''
                    }
                    }
                },
            };
            const myChart2 = new Chart(ctx, config2);
    },1000);
</script>    
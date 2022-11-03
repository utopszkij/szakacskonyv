<?php
/**
* admin / home
* az admin controllerbe van includolva
*/
$interval = $this->request->input('interval','week');
$base = $this->request->input('base',time());
$receptLabels = $this->buildLabels($interval, $base);
// adat lekérés az adatbázisból
$receptDatas = $this->model->getReceptDatas($interval,$base);

?>
<div class="col-md-12">
    <div class="adminBox adminBox5">
        <h2>Új recept statisztika</h2>
        <canvas id="myChart2" width="400" height="300"></canvas>
        <p>
        <?php $this->echoIcons('recept',$interval, $base); ?>
        </p>
    </div>    
</div>
<script>
    window.setTimeout(function() {
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
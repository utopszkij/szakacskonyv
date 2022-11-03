<?php
/**
* admin / menu
* az admin controllerbe van includolva
*/
$interval = $this->request->input('interval','week');
$base = $this->request->input('base',time());
$menuLabels = $this->buildLabels($interval, $base);
// adat lekérés az adatbázisból
$menuDatas = $this->model->getMenuDatas($interval, $base);

?>

<div class="col-md-12">
    <div class="adminBox adminBox0">
        <h2>Új napi menü statisztika</h2>
        <canvas id="myChart2" width="400" height="300"></canvas>
        <p>
        <?php $this->echoIcons('menu',$interval, $base); ?>
        </p>
    </div>    
</div>
<script>
    window.setTimeout(function() {
            var data = {
                labels: <?php echo JSON_encode($menuLabels); ?>,
                datasets: [{data: <?php echo JSON_encode($menuDatas); ?>,
                           label:'menu', 
                           backgroundColor:'blue', borderColor:'blue'}]
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
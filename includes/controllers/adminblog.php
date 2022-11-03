<?php
/**
* admin / blog grafikone
* az admin controllerbe van includolva
*/

$interval = $this->request->input('interval','week');
$base = $this->request->input('base',time());
$labels = $this->buildLabels($interval, $base);
$blogDatas = $this->model->getBlogDatas($interval,$base);

?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
        <h2>Új cikk statisztika</h2>
        <canvas id="myChart2" width="400" height="300"></canvas>
        <p>
            <?php $this->echoIcons('blog',$interval, $base); ?>
        </p>
    </div>    
</div>
<script>
    window.setTimeout(function() {
            var data = {
                labels: <?php echo JSON_encode($labels); ?>,
                datasets: [{data: <?php echo JSON_encode($blogDatas); ?>,
                           label:'Cikk', 
                           backgroundColor:'red', borderColor:'red'}]
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
<?php
/**
* admin / home
* az admin controllerbe van includolva
*/
$interval = $this->request->input('interval','week');
// adat lekérés az adatbázisból
$receptLabels = [];
if ($interval == 'week') {
    for ($i = 7; $i >= 0; $i--) {
        $receptLabels[] = date('m.d', time() - $i*24*60*60);
    }    
}    
if ($interval == 'month') {
    for ($i = 30; $i >= 0; $i--) {
        $receptLabels[] = date('m.d', time() - $i*24*60*60);
    }    
}    
if ($interval == 'year') {
    for ($i = 365; $i >= 0; $i--) {
        $receptLabels[] = date('m.d', time() - $i*24*60*60);
    }    
}    
$receptDatas = $this->model->getReceptDatas($interval);
$visitDatas = $this->model->getVisitDatas($interval);
$loginDatas = $this->model->getLoginDatas($interval);
?>
<div class="col-md-6">
    <div class="adminBox adminBox0">
        <h2>Látogatottság és login statisztika</h2>
        <canvas id="myChart1" width="400" height="300"></canvas>
        <p>
            <a href="index.php?task=admin&act=adminvisit&interval=week" 
                class="<?php if ($interval == 'week') echo 'current'; ?>">
                Hét</a>&nbsp; &nbsp;
            <a href="index.php?task=admin&act=adminvisit&interval=month" 
                class="<?php if ($interval == 'month') echo 'current'; ?>">
                Hónap</a>&nbsp; &nbsp;
            <a href="index.php?task=admin&act=adminvisit&interval=year" 
                class="<?php if ($interval == 'year') echo 'current'; ?>">
                Év</a>
        </p>
    </div>    
</div>
<div class="col-md-6">
    <div class="adminBox adminBox5">
        <h2>Új recept statisztika</h2>
        <canvas id="myChart2" width="400" height="300"></canvas>
        <p>
            <a href="index.php?task=admin&act=adminrecept&interval=week" 
                class="<?php if ($interval == 'week') echo 'current'; ?>">
                Hét</a>&nbsp; &nbsp;
            <a href="index.php?task=admin&act=adminrecept&interval=month" 
                class="<?php if ($interval == 'month') echo 'current'; ?>">
                Hónap</a>&nbsp; &nbsp;
            <a href="index.php?task=admin&act=adminrecept&interval=year" 
                class="<?php if ($interval == 'year') echo 'current'; ?>">
                Év</a>
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
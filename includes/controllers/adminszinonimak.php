<?php
/**
* admin / szinonimák
* az admin controllerbe van includolva
*/
include_once 'includes/controllers/szinonima.php';
$controller = new Szinonima();
$controller->browserURL = 'index.php/task/admin/act/adminszinonimak';
$controller->browserTask = 'admin/act/adminszinonimak';
?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
    <?php
        $controller->szinonimak();
    ?>
    </div>    
</div>

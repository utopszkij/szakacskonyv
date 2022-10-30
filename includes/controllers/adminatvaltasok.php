<?php
/**
* admin / átváltások
* az admin controllerbe van includolva
*/
include_once 'includes/controllers/atvaltasok.php';
$controller = new Atvaltasok();
$controller->browserURL = 'index.php/task/admin/act/adminatvaltasok';
$controller->browserTask = 'admin/act/adminmatvaltasok';
?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
    <?php
        $controller->atvaltasok();
    ?>
    </div>    
</div>

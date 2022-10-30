<?php
/**
* admin / cimkék
* az admin controllerbe van includolva
*/
include_once 'includes/controllers/cimkek.php';
$controller = new Cimkek();
$controller->browserURL = 'index.php/task/admin/act/admincimkek';
$controller->browserTask = 'admin/act/admincimkek';
?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
    <?php
        $controller->cimkek();
    ?>
    </div>    
</div>

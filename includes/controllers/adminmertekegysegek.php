<?php
/**
* admin / mértékegységek
* az admin controllerbe van includolva
*/
include_once 'includes/controllers/mertekegyseg.php';
$controller = new Mertekegyseg();
$controller->browserURL = 'index.php/task/admin/act/adminmertekegysegek';
$controller->browserTask = 'admin/act/adminmertekegysegek';
?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
    <?php
        $controller->mertekegysegek();
    ?>
    </div>    
</div>

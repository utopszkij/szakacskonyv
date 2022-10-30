<?php
/**
* admin / userek
* az admin controllerbe van includolva
*/
include_once 'includes/controllers/user.php';
$controller = new User();
$controller->browserURL = 'index.php/task/admin/act/adminuserek';
$controller->browserTask = 'admin/act/adminuserek';
?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
    <?php
        $controller->userek();
    ?>
    </div>    
</div>

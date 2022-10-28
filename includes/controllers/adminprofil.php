<?php
/**
* admin / userek
* az admin controllerbe van includolva
*/
?>
<div class="col-md-12">
    <div class="adminBox adminBox0">
        <iframe width="100%" height="2000" id="idIframe"
        src="index.php/task/useredit/id/<?php echo $this->session->input('loged') ?>"
        onload="iframeLoaded()"></iframe>
    </div>    
</div>

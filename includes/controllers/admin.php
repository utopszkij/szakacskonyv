<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once 'includes/models/adminmodel.php';

class Admin extends Controller {

	function __construct() {
        parent::__construct();
        $this->name = "admin";
        $this->model = new AdminModel();
        // $this->browserURL = 'index.php?task=cimkek';
        // $this->addURL = 'index.php?task=cimkeadd';
        // $this->editURL = 'index.php?task=cimkeedit';
        // $this->browserTask = 'cimkek';
	}

    protected function echoIcons($adminTask, $interval, $base) {  
        $adminTask = 'admin'.$adminTask;
        ?>           
            <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=week" 
                class="<?php if ($interval == 'week') echo 'current'; ?>">
                Hét</a>&nbsp; &nbsp;
            <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=month" 
                class="<?php if ($interval == 'month') echo 'current'; ?>">
                Hónap</a>&nbsp; &nbsp;
            <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=year" 
                class="<?php if ($interval == 'year') echo 'current'; ?>">
                Év</a>
            &nbsp;&nbsp;
            <?php if ($interval == 'week') : ?>
                <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=week&base=<?php echo ($base - (7*24*60*60)) ?>">
                &lt;</a>&nbsp
                <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=week&base=<?php echo ($base + (7*24*60*60)) ?>"> 
                &gt;</a>
            <?php endif ?>
            <?php if ($interval == 'month') : ?>
                <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=month&base=<?php echo ($base - (30*24*60*60)) ?>">
                &lt;</a>&nbsp
                <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=month&base=<?php echo ($base + (30*24*60*60)) ?>"> 
                &gt;</a>
            <?php endif ?>
            <?php if ($interval == 'year') : ?>
                <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=year&base=<?php echo ($base - (365*60*60)) ?>">
                &lt;</a>&nbsp
                <a href="index.php?task=admin&act=<?php echo $adminTask; ?>&interval=year&base=<?php echo ($base + (365*24*60*60)) ?>"> 
                &gt;</a>
            <?php endif ?>
        <?php    
    }        
	
    protected function buildLabels(string $interval, int $base): array {
        // adat lekérés az adatbázisból
        $labels = [];
        if ($interval == 'week') {
            for ($i = 7; $i >= 0; $i--) {
                $labels[] = date('Y.m.d', $base - $i*24*60*60);
            }    
        }    
        if ($interval == 'month') {
            for ($i = 30; $i >= 0; $i--) {
                $labels[] = date('Y.m.d', $base - $i*24*60*60);
            }    
        }    
        if ($interval == 'year') {
            for ($i = 365; $i >= 0; $i--) {
                $labels[] = date('Y.m.d', $base - $i*24*60*60);
            }    
        }    
        return $labels;
    }
    
    public function admin() {
        $act = $this->request->input('act','adminhome');
        // adat lekérések (test)
        $total = $this->model->getTotals();
        ?>
        <script src="vendor/chart.js/dist/chart.js"></script>
        <script>
            function navbarTogglerAdminTogle() {
                var d = document.getElementById('navbarTogglerAdmin');
                if (d.style.display == 'block') {
                    d.style.display='none';
                    d.className = '';
                } else {
                    d.style.display='block';
                    d.className = '';
                }
            }
            function iframeLoaded() {
                var iFrameID = document.getElementById('idIframe');
                if(iFrameID) {
                        // here you can make the height, I delete it first, then I make it again
                        iFrameID.height = "";
                        iFrameID.height = (iFrameID.contentWindow.document.body.scrollHeight + 200) + "px";
                }   
            }

        </script>    
        <div id="admin">
            <div class="row">
                <div class="col-md-3">
                    <div id="adminLeftBar">
                            <div class="container-fluid">
                                <button type="button" class=" d-sm-block d-md-none"
                                    onclick="navbarTogglerAdminTogle()">
                                    <span class="fas fa-bars"></span>
                                </button>
                                <div id="navbarTogglerAdmin" class="d-none d-md-block">
                                    <ul>
                                        <?php if ($this->session->input('loged',0) > 0) : ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="index.php/task/admin/act/adminprofil">
                                            <img class="avatar" src="<?php echo 'images/users/'.$this->session->input('logedAvatar','images/users/noavatar.png')?>" />
                                            &nbsp;<?php echo $this->session->input('logedName'); ?> profil
                                            </a></li>
                                        <?php endif; ?>    
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/adminuserek">
                                            <em class="fas fa-users"></em> Felhasználók</a></li>
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/admincimkek">
                                            <em class="fas fa-tags"></em> Cimkék</a></li>
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/adminmertekegysegek">
                                            <em class="fas fa-balance-scale"></em> Mértékegységek</a></li>
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/adminatvaltasok">
                                            <em class="fas fa-retweet"></em> Átváltások</a></li>
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/adminszinonimak">
                                            <em class="fas fa-exchange-alt"></em> Szinonimák</a></li>
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/adminreceptek">
                                            <em class="fas fa-utensils"></em> Receptek</a></li>
                                        <li class="nav-item"><a class="nav-link" href="index.php/task/admin/act/adminblogs">
                                            <em class="fas fa-feather-alt"></em> Cikkek</a></li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>        
                </div>    
                <div class="col-md-9">
                    <div id="adminBody">
                        <div id="adminNavBar">
                            <a href="index.php?task=admin">Home</a>&nbsp;
                            <a href="index.php/task/impresszum" 
                                style="color:silver">Impresszum</a>&nbsp;
                        </div>
                        <div class="row">
                        <div class="col-md-3">
                                <div class="adminBox adminBox1">
                                <a href="index.php/task/admin/act/adminrecept">
                                        <p><strong>
                                            <em class="fas fa-utensils" style="font-size:32px"></em>
                                            &nbsp;Recept</strong></p>
                                        <h3><?php echo $total->recept; ?></h3>
                                    </a>
                                </div>
                            </div>    
                            <div class="col-md-3">
                                <div class="adminBox adminBox2">
                                <a href="index.php/task/admin/act/adminmenu">
                                        <p><strong>
                                            <em class="fas fa-calendar" style="font-size:32px"></em>
                                            &nbsp;Napi menü</strong></p>
                                        <h3><?php echo $total->menu; ?></h3>
                                    </a>
                                </div>
                            </div>    
                            <div class="col-md-3">
                                <div class="adminBox adminBox3">
                                <a href="index.php/task/admin/act/adminblog">
                                        <p><strong>
                                            <em class="fas fa-feather" style="font-size:32px"></em>
                                            &nbsp;Cikk</strong></p2>
                                        <h3><?php echo $total->blog; ?></h3>
                                    </a>
                                </div>
                            </div>    
                            <div class="col-md-3">
                                <div class="adminBox adminBox4">
                                <a href="index.php/task/admin/act/adminregist">
                                        <p><strong>
                                            <em class="fas fa-user-check" style="font-size:32px"></em>
                                            &nbsp;Regisztrált felhasználó</strong></p>
                                        <h2><?php echo $total->user; ?></h2>
                                    </a>
                                </div>
                            </div>    
                        </div>
                        <div class="row">
                           <?php include 'includes/controllers/'.$act.'.php'; ?>
                        </div>    
                    </div>
                </div>
            </div>
        </div>    
        <?php
    }
    

}
?>
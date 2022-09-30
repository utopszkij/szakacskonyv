<?php

include_once (__DIR__.'/../models/atvaltasmodel.php');

class Atvaltasok extends Controller {

    function __construct() {
        parent::__construct();
        $this->model = new AtvaltasModel();
    }

    /**
     * az előforduló hozzávaló nevek lapozható listája
     * jelzi, hogy (felthetőleg) van vele munka vagy nincs
     * GET param: "page"
     * @return array [{"nev":"", "mecount":#, "szorzocount":#},... ]
     */
    public function atvaltasok() {
		$limit = round((int)$_SESSION['screen_height'] / 80);
        $page = $this->request->input('page',1,INTEGER);
        $offset = ($page - 1) * $limit;
        $records = $this->model->getNevek($offset,$limit);
        $total = $this->model->getTotalNevek();
        $pages = [];
        $p = 1;
        while ((($p - 1) * $limit) < $total) {
            $pages[] = $p;
            $p++;
        }
        view('atvaltasok',[
            "records" => $records,
            "page"=> $page,
            "loged" => $this->loged,
            "logedName" => $this->logedName,
            "admin" => $this->logedAdmin,
            "total" => $total,
            "pages" => $pages,
            "task" => 'atvaltasok'
        ]);
    }

    /*
    * atvaltas felvivő, modositó képernyő
    * GET param "nev"
    */
    public function atvaltas() {
        $nev = $this->request->input('nev');
        $atvaltoObject = $this->model->getObject($nev);

        view('atvaltaskep',[
            "flowKey" => $this->newFlowKey(),
            "atvaltas" => $atvaltoObject,
            "loged" => $this->loged,
            "logedName" => $this->logedName,
            "admin" => $this->logedAdmin,
            "disabled" => ($this->loged <= 0)
        ]);

    }

    /**
     * képernyőről POST -ban érkező adatok tárolása
     */
    public function atvaltassave() {
        if (!$this->checkFlowKey('index.php?task=atvaltasok')) {
            echo 'flowKey híba. Túl hosszú várakozás miatt lejárt a munkamenet'; exit();
        }
        if ($this->logedAdmin) {
            $szme = $this->request->input('szme');
            $nev = $this->request->input('nev');
            $obj = $this->model->getObject($nev);
            $obj->szme = $szme;
            foreach($obj->szorzok as $fn => $fv) {
                $obj->szorzok[$fn] = $this->request->input('sz'.$fn,1,NUMBER);
            }
            $obj->szorzok[$szme] = 1;
            $this->model->saveObject($obj);
        }
        echo '<script>
        location="index.php?task=atvaltasok";
        </script>
        ';
    }

}

?>
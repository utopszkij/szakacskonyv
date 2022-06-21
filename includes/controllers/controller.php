<?php

define('NOSQLINJECTION','NOSQLINJECTION');
define('HTML','HTML');
define('NUMBER','NUMBER');
define('INTEGER','INTEGER');
define('NOFILTER','NOFILTER');

class Request {

    /**
     * adat olvasás GET vagy POST -ból
     * @param string $name
     * @param mixed $default
     * @param string $filter
     * @returm mixed
     */
    public function input(string $name, 
                        $default = '', 
                        string $filter = NOSQLINJECTION) {
        global $mysqli;                    
        $result = $default;
        if (isset($_GET[$name])) {
            $result = $_GET[$name];
        }
        if (isset($_POST[$name])) {
            $result = $_POST[$name];
        }
        $result = urldecode($result);
        switch ($filter) {
            case NOSQLINJECTION:
                $result = strip_tags($result);
                $result = $mysqli->real_escape_string($result);
                break;
            case NUMBER:
                $result = (float)$result;
                break;    
            case INTEGER:
                $result = (int)$result;
                break;    
        }    
        return $result;
    }

    /**
     * adat irása a request -be
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, $value) {
        $_GET[$name] = $value;
        $_POST[$name] = $value;
    }

    /**
     * ellenörzés, $name létezik a GET -ben vagy POST -ban?
     * @param string $name
     * @return bool
     */
    public function isset(string $name): bool  {
        $result = false;
        if (isset($_GET[$name])) {
            $result = true;
        }
        if (isset($_POST[$name])) {
            $result = true;
        }
        return $result;
    }
}


class Session {

    /**
     * adat olvasás a SESIION -ból
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function input(string $name, $default = '') {
        $result = $default;
        if (isset($_SESSION[$name])) {
            $result = $_SESSION[$name];
        }
        return $result;
    }

    /**
     * adat írás a SESSION -ba
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, $value) {
        $_SESSION[$name] = $value;
    }
    /**
     * ellenörzés, $name létezik a SESSION -ban?
     * @param string $name
     * @return bool
     */
    public function isset(string $name): bool  {
        return isset($_SESSION[$name]);
    }

    /**
     * elem törlése
     * @param string $name
     */
    public function delete(string$name) {
        unset($_SESSION[$name]);
    }
}

/**
 * controller abstract model
 * a __construct rendszerint átdefiniálandó ($this->model, $this->name definiálása)
 * a validator($record), accessRight($action,$record) szinte mindig átdefiniálandó
 * az items, new, edit, save, delete pedig egyedi nevü rutinokban hívandó pl:
 *    public function valamik() {
 *          $this->items()
 *    }
 * igényelt model methodusok: emptyRecord(), save($record), 
 *      getById($id), delteById($id), getItems($page,$limit,$filter,$order), 
 *      getTotal($filter)
 * igényel viewerek {name}browser, {name}form
  */
class Controller {
    protected $request;
    protected $session;
    protected $loged = 0;
    protected $logedName = 'Látogató';
    protected $logedAdmin = false;
    protected $logedGroup = '';
    protected $logedAvatar = '';
    protected $model;
    protected $name;
    protected $browserURL;
    protected $addURL;
    protected $editURL;
    protected $browserTask;

    function __construct() {
        $this->request = new Request();
        $this->session = new Session();
        $this->loged = $this->session->input('loged',0,INTEGER);
        $this->logedName = $this->session->input('logedName','Látogató');
        $this->logedAdmin = isAdmin();
        $this->logedGroup = $this->session->input('logedGroup');
        $this->logedAvatar = $this->session->input('logedGroup');
        // $this->model = new Model();
        // $this->name = 'xxx';
        // $this->browserURL = '...';
        // $this->formURL = '...';
        // $this->browserTask = '...';
    }

    /**
     * loged user hozzáférés ellenörzése
     * @param string $action  'new'|'edit'|'delete'
     * @param RecordObject $record
     * @return bool
     */    
    protected function accessRight(string $action, $record): bool {
        return true;
    }

    /**
     * rekord ellenörzés
     * @param RecordObject $record
     * @return string üres ha minden OK, egyébként hibaüzenet
     */    
    protected function validator($record): string {
        return '';
    }

    /**
     * browser
     */
    protected function items($order = 1) {
        // paraméter olvasása get vagy sessionból
        $page = $this->session->input($this->name.'page',1);
        $page = $this->request->input('page',$page);
        $limit = $this->session->input($this->name.'limit',20);
        $limit = $this->request->input('limit',$limit);
        $filter = $this->session->input($this->name.'filter','');
        $filter = $this->request->input('filter',$filter);
        $order = $this->session->input($this->name.'order',1);
        $order = $this->request->input('order',$order);
        // paraméter tárolás sessionba
        $this->session->set($this->name.'page',$page);
        $this->session->set($this->name.'limit',$limit);
        $this->session->set($this->name.'filter',$filter);
        $this->session->set($this->name.'order',$order);
        // rekordok és total olvasása az adatbázisból
        $items = $this->model->getItems($page,$limit,$filter,$order);
        $total = $this->model->getTotal($filter);
        // paginátor adat képzés
        $pages = [];
        $p = 1;
        while ((($p - 1) * $limit) < $total) {
            $pages[] = $p;
            $p++;
        }
        // megjelenítés
        view($this->name.'browser',[
            "items" => $items,
            "page" => $page,
            "total" => $total,
            "pages" => $pages,
            "task" => $this->browserTask,
            "filter" => $filter,
            "loged" => $this->loged,
            "logedName" => $this->loged,
            "logedAdmin" => $this->logedAdmin,
            "previous" => SITEURL,
            "errorMsg" => $this->session->input('errorMsg',''),
            "successMsg" => $this->session->input('successMsg','')
        ]);
        $this->session->delete('errorMsg');
        $this->session->delete('successMsg');
    }

    /**
     * Új item felvivő képernyő
     */
    protected function new() {
        $item = $this->model->emptyRecord();
        if (!$this->accessRight('new',$item)) {
            $this->session->set('errorMsg','Hozzáférés nem engedélyezett');
            echo '<script>
            location="'.$this->browserURL.'";
            </script>
            ';
        }
        if ($this->session->isset('oldRecord')) {
            $item = $this->session->input('oldRecord');
        }
        view($this->name.'form',[
            "record" => $item,
            "loged" => $this->loged,
            "logedName" => $this->loged,
            "logedAdmin" => $this->logedAdmin,
            "previous" => $this->browserURL,
            "errorMsg" => $this->session->input('errorMsg','')
        ]);
        $this->session->delete('errorMsg');
    }

    /**
     * meglévő item edit/show képernyő
     */
    protected function edit() {
        $id = $this->request->input('id',0);
        $record = $this->model->getById($id);
        if (!$this->accessRight('edit',$record)) {
            $this->session->set('errorMsg','Hozzáférés nem engedélyezett');
            echo '<script>
            location="'.$this->browserURL.'";
            </script>
            ';
        }
        if ($this->session->isset('oldRecord')) {
            $record = $this->session->input('oldRecord');
        }
        view($this->name.'form',[
            "record" => $record,
            "logedAdmin" => $this->logedAdmin,
            "loged" => $this->loged,
            "previous" => $this->browserURL,
            "errorMsg" => $this->session->input('errorMsg',''),
        ]);
        $this->session->delete('errorMsg');
    }

    /**
     * edit vagy new form tárolása
     */
    protected function save($record) {
        $this->session->set('oldRecord',$record);
        if ($record->id == 0) {
            if (!$this->accessRight('new',$record)) {
                $this->session->set('errorMsg','Hozzáférés nem engedélyezett');
                echo '<script>
                location="'.$this->browserURL.'";
                </script>
                ';
            }
        } else {
            if (!$this->accessRight('edit',$record)) {
                $this->session->set('errorMsg','Hozzáférés nem engedélyezett');
                echo '<script>
                location="'.$this->browserURL.'";
                </script>
                ';
            }
        }   
        $error = $this->validator($record);
        if ($error != '') {
            $this->session->set('errorMsg',$error);
            if ($record->id == 0) {
                echo '<script>
                location="'.$this->addURL.'";
                </script>
                ';
            } else {
                echo '<script>
                location="'.$this->editURL.'";
                </script>
                ';
            }    
        } else {
            $this->session->delete('oldRecord');
            $this->model->save($record);
            if ($this->model->errorMsg == '') {
                $this->session->delete('errorMsg');
                $this->session->set('successMsg','Adat tárolva');
                echo '<script>
                    location="'.$this->browserURL.'";
                </script>
                ';
            } else {
                echo $this->model->errorMsg; exit();
            }
        }
    }

    /**
     * meglévő item törlése
     */
    protected function delete() {
        $id = $this->request->input('id',0);
        $item = $this->model->getById($id);
        if (!$this->accessRight('delete',$item)) {
            $this->session->set('errorMsg','Hozzáférés nem engedélyezett');
            echo '<script>
            location="'.$this->browserURL.'";
            </script>
            ';
        }
        $this->model->delById($id);
        if ($this->model->errorMsg == '') {
            $this->session->set('successMsg','Adat törölve');
            echo '<script>
            location="'.$this->browserURL.'";
            </script>
            ';
        } else {
            echo $this->model->errorMsg; exit();
        }
    }
}
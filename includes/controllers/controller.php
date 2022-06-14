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
}

class Controller {
    protected $request;
    protected $session;
    protected $loged = 0;
    protected $logedName = 'Látogató';
    
    function __construct() {
        $this->request = new Request();
        $this->session = new Session();
        $this->loged = $this->session->input('loged',0,INTEGER);
        $this->logedName = $this->session->input('logedName','Látogató');
    }

}
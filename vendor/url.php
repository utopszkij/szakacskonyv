<?php
    class URL {

        /**
         * aktuális url
         */
        public static function current():string {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
            "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return urldecode($url);
        }

        /**
         * elöző url
         * FIGYELEM!!!! csak akkor ad jó eredményt ha az adott sessionban, 
         * előtte EGYSZER volt URL::save() hívás!
         * @param int $count default 1
         */
        public static function previous(int $count = 1):string {
            if (!isset($_SESSION['urls'])) {
                $_SESSION['urls'] = [];
            } else {
                $count++; // ilyenkor már bent van az urls stack -ben a current
            }
            $i = count($_SESSION['urls']);
            if ($i >= $count) {
                $result = $_SESSION['urls'][$i - $count];
            } else {
                $result = SITEURL;
            }
            return $result;
        }

        /**
         * aktuális url tárolása a session urls stack -be
         * az url stack mérete max 10, a previous url -re visszatérést kezeli
         */
        public static function save() {
            if (!isset($_SESSION['urls'])) {
                $_SESSION['urls'] = [];
            }
            $i = count($_SESSION['urls']);
            if ($i > 10) {
                array_splice($_SESSION['urls'],0,1);
                $i = count($_SESSION['urls']);
            }

            $url = URL::current();
            if ($i > 0) {
                // van már url a stackben
                if (($url !== $_SESSION['urls'][$i - 1]) | 
                     (strlen($url) != strlen($_SESSION['urls'][$i - 1]))) {
                        // nem a previous -ra megy 
                        $_SESSION['urls'][] = $url;
                }    
            } else {
                // még üres a stack    
                $_SESSION['urls'][] = $url;
            }
            return;
        }
    }
?>
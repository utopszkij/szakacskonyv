<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    // logikai atszamitoObject
    class AtszamitoObject {
        public $nev = '';
        public $szme = '';
        public $szorzok = []; //["me" => 0.0,...]
    }

    class AtvaltasModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable = 'atvaltasok';
            $this->errorMsg = ''; 
        }

        /**
         * kiolvassa a $record->nev - hez az szmítási (alap) mértékegységet
         * @param Record $record Hozzavalok rekord
         * @return string
         */
        public function getSzme(Record $record): string {
            $result = $record->me;
            $db = new Query('atvaltasok');
            $rec = $db->where('nev','=',$record->nev)
            ->where('me','=',$record->me)
            ->where('szorzo','<>',1.0)
            ->first();
            if (isset($rec->szme)) {
                $result = $rec->szme;
            } 
            $this->errorMsg = $db->error;
            return $result;
        }

        /**
         * átszámolja a megadott rekordot alap mértékegységre
         * @param Record $record Hozzavalok rekord
         */
        public function receptAtszamito(Record $record) {
            $db = new Query('atvaltasok');
            $db->exec('UPDATE hozzavalok h, atvaltasok a
            SET h.szmennyiseg = h.mennyiseg * a.szorzo     
            WHERE h.recept_id = '.$record->recept_id.' AND h.nev = "'.$record->nev.'" AND
                a.nev = h.nev AND a.me = h.me AND a.szme = h.szme AND
                a.nev IS NOT NULL
            ');
            $this->errorMsg = $db->error;
        }        

        /**
         * logikai átszámító objekt olvasása az adatbázisból
         * @param string $nev
         * @retun AtszamitoObject
         */
        public function getObject(string $nev): AtszamitoObject {
            $result = new AtszamitoObject();
            $result->nev = $nev;
            $result->szme = '?';

            // összes szükséges
            $db = new Query('hozzavalok');
            $db->setSql('
            SELECT distinct h.nev, h.me
            FROM hozzavalok h
            where h.me <> "" AND h.nev = "'.$nev.'"
            order by h.me');
            $recs = $db->all();
            foreach ($recs as $rec) {
                $result->szorzok[$rec->me] = 0;
            }

            // meglévők
            $db = new Query('atvaltasok');
            $recs = $db->where('nev','=',$nev)->all();
            foreach ($recs as $rec) {
                $result->szorzok[$rec->me] = Round($rec->szorzo*10) / 10;
                $result->szme = $rec->szme;
            }

            return $result;
        }

        /**
         * logikai átszámitó objekt tárolása az adatbázisba
         * @param AtszamitoObject $obj
         */
        public function saveObject(AtszamitoObject $obj) {
            // név-hez tartozó esetleg meglévő felesleges rekordok törlése
            $db = new Query('atvaltasok');
            $db->where('nev','=',$obj->nev)
                ->where('szme','<>',$obj->szme)
                ->delete();
            $db->where('nev','=',$obj->nev)
                ->where('me','=',$obj->szme)
                ->delete();
            // az objektben lévők tárolása (insert vagy update) 0 és 1 szorzókat nem tárolja
            foreach ($obj->szorzok as $fn => $fv) {
                if (($fn != '') & ($fn != '?') &
                    ($fv != 0) & ($fv != 1) & ($fn != $obj->szme)) {
                    $new = new Record();
                    $new->nev = $obj->nev;
                    $new->szme = $obj->szme;
                    $new->me = $fn;
                    $new->szorzo = $fv;
                    $db = new Query('atvaltasok');
                    $old = $db->where('nev','=',$obj->nev)
                            ->where('me','=',$fn)
                            ->first();
                    if (isset($old->szme)) {
                        $db->where('nev','=',$obj->nev)
                            ->where('me','=',$fn)
                            ->update($new);
                    }   else {
                        $db->insert($new);
                    } 
                }
            }

            // érintett recept hozzávalók modositása
            $db->exec('
            update hozzavalok h
            set h.szme = "'.$obj->szme.'" 
            where h.nev = "'.$obj->nev.'" 
            ');
            $db->exec('
            update hozzavalok h, atvaltasok a
            set h.szmennyiseg = h.mennyiseg * a.szorzo 
            where h.nev = a.nev AND h.me = a.me AND h.nev = "'.$obj->nev.'" 
            ');
            $db->exec('
            update hozzavalok h
            set h.szmennyiseg = h.mennyiseg  
            where h.me = h.szme AND h.nev = "'.$obj->nev.'" 
            ');
            
            
        }

        /**
         * összes hozzávaló név lapozható listája
         * @param int $offset
         * @param int $limit
         */
        public function getNevek(int $offset = 0, int $limit = 20): array {
            $db = new Query('hozzavalok');
            $db->setSql('
            SELECT h.nev, count(distinct h.me) mecount, count(distinct a.me) szmecount
            FROM hozzavalok h
            INNER JOIN receptek r ON r.id = h.recept_id
            LEFT OUTER JOIN atvaltasok a ON a.nev = h.nev 
            where h.me <> "" AND r.id IS NOT NULL
            group by h.nev
            having count(distinct h.me) > 1
            order by h.nev
            limit '.$offset.','.$limit);
            $result = $db->all();
            return $result;

        } 

        public function getTotalNevek(): int {
            $db = new Query('hozzavalok');
            $db->setSql('
            SELECT h.nev, count(distinct h.me) mecount, count(distinct a.szme) szmecount
            FROM hozzavalok h
            LEFT OUTER JOIN atvaltasok a ON a.nev = h.nev 
            where h.me <> "" 
            group by nev
            having count(distinct h.me) > 1
            order by nev');
            $recs = $db->all();
            return count($recs);
        }
    }		


?>
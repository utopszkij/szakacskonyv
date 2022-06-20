<?php
    namespace RATWEB;

    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class Model  {
        public $table = '';
        public $errorMsg = '';
        
        function __construct() {
            $this->errorMsg = '';
            $this->table = '';
        }

        /**
         * $table setter
         */
        public function setTable(string $table) {
            $this->table = $table;
        }

        /**
         * $table getter
         */
        public function getTable() {
            return $this->table;
        }

        /**
         * sql valu kialakitása
         * @param mixed $v
         * @return mixed
         */
        public function sqlValue($v) {
            $q = new Query($this->table);
            return $q->sqlValue($v);
        }
        
        /**
         * rekord elérés ID alapján
         * @param int $id
         * @return Record ha nem talál akkor {}
         */
        public function getById(int $id): Record {
            $this->errorMsg = '';
            $q = new Query($this->table);
            $result =  $q->where('id','=',$id)->first();
            $this->errorMsg = $q->error;
            return $result;
        }

        /**
         * rekord törlés id alapján
         * @param int $id
         * @return bool  tru ha sikeres
         */
        public function delById(int $id): bool {
            $this->errorMsg = '';
            $q = new Query($this->table);
            $this->errorMsg = $q->error;
            $q->where('id','=',$id)->delete();
            return ($this->errorMsg == '');
        }

        /**
         * rekord(ok) lekérése
         * @param string $fieldName
         * @param mixed $value
         * @return array
         */
        public function getBy(string $fieldName, $value): array {
            $this->errorMsg = '';
            $q = new Query($this->table);
            $result = $q->where($fieldName,'=',$value)->all();
            $this->errorMsg = $q->error;
            return $result;
        }

        /**
         * rekord tárolása (id==0 insert, id > 0y update)
         * @param Record $record
         * @return int record ID
         */
        public function save(Record $record): int {
            $this->errorMsg = '';
            $q = new Query($this->table);
            if ($record->id == 0) {
                $result =  $q->insert($record);

            } else {
                $q->where('id','=',$record->id)->update($record);
                $result = $record->id;
            }
            $this->errorMsg = $q->error;
            return $result;
        }

        /**
         * utolsó müvelet hibaüzenete ('' ha nem volt hiba)
         */
        public function lastError() {
            return $this->errorMsg;
        }

    }
?>
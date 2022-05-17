<?php
declare(strict_types=1);
namespace RATWEB\DB;

/**
* MYSQL database interface
* (laravel szerű)
* szükséged DEFINE: HOST, USER, PSW, DBNAME
*/

/**
* Minden rekordot ebből kell származtatni
*/
class Record {
}

/**
* sql mező és tábla neveket `név` alakba konvertálja
* @param string $s
* @return string
*/
function sqlName(string $s): string {
	if (strpos($s,'.') > 0) {
		$result = str_replace('.','.`',$s).'`';
	} else {
		$result = '`'.$s.'`';
	}
	return $result;
} 

/**
* sql where kezelő objektum 
*/
class Where {
	protected array $ors = []; // [[[fieldName, relation, value],...],..]
	
	/**
	* bőviti a már meglévő feltételeket AND fieldName rel value -val
	* a value érték magadásánál használd az sqlValue vagy sqlName fv.-t !
	* @param string $fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string|number  érték|mezőnév|alias.mezőnév|'(lista)'
	* @return void
	*/
	function and(string $fieldName, string $rel,  $value) {
		if (count($this->ors) == 0) {
			$this->ors[] = [[$fieldName, $rel, $value]];		
		} else {
			$i = count($this->ors) - 1;
			$this->ors[$i][] = [$fieldName, $rel, $value];		
		}
	}
	
	/**
	* bőviti a már meglévő feltételeket OR (fieldName rel value) -val
	* a value érték magadásánál használd az sqlValue vagy sqlName fv.-t !
	* @param string $fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string|number  érték|mezőnév|alias.mezőnév|'(lista)'
	* @return void
	*/
	function or(string $fieldName, string $rel, $value) {
		$this->ors[] = [[$fieldName, $rel, $value]];		
	}
	
	/**
	* 'WHERE .....' stringet ad vissza
	* @return string
	*/
	public function getSql(): string {
		$result = '';
		if (count($this->ors) > 0) {
			$result = 'WHERE ';
			$w = '';
			foreach ($this->ors as $or) {
				$result .= $w.'(';
				$w2 = '';
				foreach ($or as $and) {
					$result .= $w2.sqlName($and[0]).' '.$and[1].' '.Query::sqlValue($and[2]);
					$w2 = ' AND ';				
				}
				$result .= ')';
				$w = ' OR '; 
			}
		}		
		return $result;
	}	
}

/**
* sql union kezelő objektum
*/ 
class Union {
	protected array $selects = []; // [[name,alias],..] ha üres akkor *
	protected string $alias = '';
	public string $tableName = '';
	protected Query $subSelect;
	public Where $where;
	protected array $joins = []; // [[from,alias,field,rel,value],....]
	
	/**
	* constructor
	* @param string|Query tábla név vagy subselect
	* @param string alias (elhagyható)
	*/
	function __construct($from, string $alias='') {
		if (is_string($from)) {
			$this->tableName = $from;
		} else {
			$this->subSelect = $from;
		}
		$this->alias = $alias;
		$this->where = new Where();
	}

	/**
	* teljes sql stringet ad vissza
	* @return string
	*/
	public function getSql(): string {

		$result = 'SELECT ';
		if (count($this->selects) > 0) {
			$w = '';
			foreach ($this->selects as $select) {
				if ($select[1] != '') {
					$result .= $w.$select[0].' AS '.$select[1]; 
				} else {
					$result .= $w.$select[0]; 
				}			
				$w = ',';
			}
			$result .= "\n";
		} else {
			$result .= '*'."\n";		
		}

		$result .= 'FROM ';
		if ($this->tableName != '') {
			$result .= sqlName($this->tableName);
		} else {
			$result .= '('.$this->subSelect->getSql().')';
		}		
		if ($this->alias != '') {
			$result .= ' AS '.$this->alias."\n";
		} else {
			$result .= "\n";
		}
		foreach ($this->joins as $join) {
			$result .= $join[0].' JOIN '.sqlName($join[1]).' AS '.$join[2].
			' ON '.sqlName($join[3]).' '.$join[4].' '.$join[5]."\n";
		}		
		$result .= $this->where->getSql()."\n";
		return $result;
	}

	/**
	* select definiciót add hozzá 
	* @param array [mezőnév, ...] vagy [[mezőnév,alias],....]
	* @return Union $this
	*/
	public function select(array $selects):Union {
		$this->selects = $selects;
		for ($i =0; $i < count($this->selects); $i++) {
			if (!is_array($this->selects[$i])) {
				$select = $this->selects[$i];
				$this->selects[$i] = [$select,''];
			}		
		}
		return $this;
	} 

	/**
	* where definiciót add hozzá 
	* bőviti a már meglévő feltételeket AND fieldName rel value -val
	* a value érték magadásánál használd az sqlValue vagy sqlName fv.-t !
	* @param string $fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string|number  érték|mezőnév|alias.mezőnév|'(lista)'
	* @return Union $this
	*/
	public function where(string $fieldName, string $rel, $value):Union {
		$this->where->and($fieldName, $rel, $value);
		return $this;
	} 

	/**
	* where definiciót add hozzá 
	* bőviti a már meglévő feltételeket OR fieldName rel value -val
	* a value érték magadásánál használd az sqlValue vagy sqlName fv.-t !
	* @param string $fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string|number  érték|mezőnév|alias.mezőnév|'(lista)'
	* @return Union $this
	*/
	public function orWhere(string $fieldName, string $rel, $value):Union {
		$this->where->or($fieldName, $rel, $value);
		return $this;
	} 

	/**
	* join -t ad hozzá
	* az ON érték megadásnál használd a sqlName vagy sqlValue fv-t!
	* @param string 'LEFT OUTER'|'RIFGHT OUTER'|'INNER'
	* @param string|Query from tábla vagy subselect
	* @param string alias
	* @param string ON fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string érték|alias.mező|(lista)
	* @return Union $this
	*/
	public function join(string $joinType, 
									$from, 
									string $alias, 
									string $onField, 
									string $onRel, 
									$onValue):Union {
		$this->joins[] = [$joinType, $from, $alias, $onField, $onRel, $onValue];										
		return $this;										
	} 
}

global $mysqli;
$mysqli = false;

/**
* sql query kezelő objektum 
*/
class Query {
	public string $error = '';
	public int $errno = 0;	
	
	protected array $unions;
	protected array $groupBy;
	protected string $order = '';
	protected string $orderDir = 'ASC';
	protected int $offset = 0;
	protected int $limit = 0;
	protected int $cursor = -1;
	protected string $status = '';
	protected $mysqli;
	protected $res = false; // MYSQL result
	protected string $sql = '';
	
	/**
	* constructor
	* @param string|Query tábla név vagy subselect
	* @param string alias (elhagyható)
	*/
	function __construct($from, string $alias='') {
		global $mysqli;
		$this->unions[] = new Union($from, $alias);
		$this->groupBy = [];
		if ($mysqli == false) {
			$mysqli = new \mysqli(HOST, USER, PSW, DBNAME);
			$mysqli->set_charset('utf8');
			$this->mysqli = $mysqli;	
			$this->exec('SET character_set_results=utf8');
   	   $this->exec('SET character_set_connection=utf8');
      	$this->exec('SET character_set_client=utf8');		
		} else {
			$this->mysqli = $mysqli;
		}	
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
	}
	
	/**
	* sql mező értékeket szükség szerint "érték" alakra konvertálja
	* sql injection elleni védelem
	* @param string|number|bool $s
	* @return string|number|bool
	*/
	public static function sqlValue($s) {
		global $mysqli;
		if ($mysqli === false) {
			$mysqli = new \mysqli(HOST, USER, PSW, DBNAME);
			$mysqli->set_charset('utf8');
			$this->mysqli = $mysqli;	
			$this->exec('SET character_set_results=utf8');
   	   		$this->exec('SET character_set_connection=utf8');
      		$this->exec('SET character_set_client=utf8');		
		}
		if ($s === '') {
			$result = '""';
		} else if (is_numeric($s)) {
			$result = $s;
		} else if (is_bool($s)) {
			$result = $s;
		} else {
			$result = '"'.$mysqli->real_escape_string($s).'"';	
		}
		return $result;
	} 
	
	/**
	* sql mező és tábla neveket `név` alakba konvertálja
	* @param string $s
	* @return string
	*/
	public static function sqlName(string $s): string {
		if (strpos($s,'.') > 0) {
			$result = str_replace('.','.`',$s).'`';
		} else {
			$result = '`'.$s.'`';
		}
		return $result;
	} 


	/**
	* az utoljára végrehajtott all() funkció eredménysorainak száma
	* @return int
	*/
	public function count(): int {
		if (!$this->res) {
			$this->res = $this->mysqli->query($this->getSql());
			$this->error = mysqli_error($this->mysqli);
			$this->errno = mysqli_errno($this->mysqli);
			$this->cursor = -1;
		}
		$result = $this->res->num_rows;
		return  $result;	
	}

	/**
	* rekordok lekérése tömbbe
	* @return array of Record
	*/
	public function all():array  {
		$result = [];
		if ($this->sql == '') {
			$this->res = $this->mysqli->query($this->getSql());
		} else {
			$this->res = $this->mysqli->query($this->sql);
		}
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
		if ($this->error != '') {
			$this->cursor = -1;
			return $result;
		}
		
		if ($this->res->num_rows == 0) {
			$this->error = 'not found';
			$this->errno = 404;
			$this->cursor = -1;
			return $result;
		}
		$this->sql = '';	
		$this->cursor = -1;
		if (($this->errno == 0) & (isset($this->res->num_rows))) {
			for ($i = 0;  $i < $this->res->num_rows; $i++) {
				$this->res->data_seek($i);
				$result[] = $this->res->fetch_object('\RATWEB\DB\Record');			
			}		
		}		
		return $result;
	} 

	/**
	* a feltételkenek megfelelő egyetlen (vagy első) rekord lekérése
	* @return Record
	*/
	public function first(): Record {
		$result = new Record();
		$this->limit = 1;
		if ($this->sql == '') {
			$this->res = $this->mysqli->query($this->getSql());
		} else {
			$this->res = $this->mysqli->query($this->sql);
		}	
		if ($this->res->num_rows == 0) {
			$this->error = 'not found';
			$this->errno = 404;
			$this->cursor = -1;
			return $result;
		}
		$this->sql = '';	
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
		$this->cursor = -1;
		if (($this->errno == 0) & ($this->res->num_rows == 1)) {
				$this->res->data_seek(0);
				$result = $this->res->fetch_object('\RATWEB\DB\Record');			
		}
		return $result;				
	}

	/**
	* feltételeknek megfelelő rekordok soros elérése
	* ha a végére ért akkor errno = 1 error= 'end of record set';
	* @return Record
	*/
	public function fetch(): Record {
		$result = new Record();
		if (!$this->res) {
			if ($this->sql == '') {
				$this->res = $this->mysqli->query($this->getSql());
			} else {
				$this->res = $this->mysqli->query($this->sql);
			}	
			if ($this->res->num_rows == 0) {
				$this->error = 'not found';
				$this->errno = 404;
				$this->cursor = -1;
				return $result;
			}
			$this->error = mysqli_error($this->mysqli);
			$this->errno = mysqli_errno($this->mysqli);
			$this->cursor = -1;
		}
		if ($this->res) {
			$this->cursor++;
			if ($this->cursor < $this->res->num_rows) {
				$this->res->data_seek($this->cursor);
				$result = $this->res->fetch_object('\RATWEB\DB\Record');			
			} else {
				$this->error = 'end of record set';		
				$this->errno = 1;		
			}
		} else {
			$this->error = 'not record set';		
			$this->errno = 999;		
		}	
		return $result;
	}

	/**
	* sql string végrehajtása
	* @param string sql 
	* @return bool sikeres vagy nem?
	*/
	public function exec(string $sql):bool {
		$this->mysqli->query($sql);
		$this->cursor = -1;
		$this->res = false;
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
		return ($this->errno == 0);
	}

	/**
	* Új rekord felvitele
	* @param Record
	* @return int új AUTO_INCREMENT mező érték
	*/
	public function insert(Record $record): int {
		$result = 0;
		$this->res = false;
		$this->cursor = -1;
		$sql = 'INSERT INTO '.sqlName($this->unions[0]->tableName)."\n";
		$sql .= '(';
		$w = '';
		foreach ($record as $fn => $fv) {
			$sql .= $w.sqlName($fn);
			$w = ','; 		
		}
		$sql .= ")\n";
		$sql .= 'VALUES (';
		$w = '';
		foreach ($record as $fn => $fv) {
			$sql .= $w.$this->sqlValue($fv);
			$w = ','; 		
		}
		$sql .= ")\n";
		$this->mysqli->query($sql);
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
		if (($this->errno == 0) & (isset($this->mysqli->insert_id))) {
			$result = mysqli_insert_id($this->mysqli);
		}	
		return $result;	
	}

	/**
	* a megadott feltételknek megfelelő rekordok modosítása
	* @param Record módosítandó mezőket/értékeket tartalmazza
	* @retun bool sikeres vagy nem?
	*/
	public function update(Record $record) {
		$result = 0;
		$this->res = false;
		$this->cursor = -1;
		$sql = 'UPDATE '.sqlName($this->unions[0]->tableName)."\n";
		$sql .= 'SET ';
		$w = '';
		foreach ($record as $fn => $fv) {
			$sql .= $w.sqlName($fn).' = '.$this->sqlvalue($fv)."\n";
			$w = ','; 		
		}
		$sql .= "\n".$this->unions[0]->where->getSql()."\n";
		$this->mysqli->query($sql);
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
		if ($this->errno == 0) {
			$info = mysqli_info($this->mysqli);
			if (strpos(' '.$info,'Rows matched: 0') > 0) {
				$this->error = 'not found';
				$this->errno = 404;		
			} 		
		}
		return ($this->errno == 0);	
	}

	/**
	* a megadott feltételknek megfelelő rekordok törlése
	* @retun bool sikeres vagy nem?
	*/
	public function delete() {
		$result = false;
		$this->res = false;
		$this->cursor = -1;
		$sql = 'DELETE FROM '.sqlName($this->unions[0]->tableName)."\n";
		$sql .= $this->unions[0]->where->getSql()."\n";
		$this->mysqli->query($sql);
		$this->error = mysqli_error($this->mysqli);
		$this->errno = mysqli_errno($this->mysqli);
		return ($this->errno == 0);	
	}

	/**
	* select definiciót add hozzá 
	* @param array [mezőnév, ...] vagy [[mezőnév,alias],....]
	* @return Query $this
	*/
	public function select(array $selects):Query {
		$i = count($this->unions) - 1;
		$this->unions[$i]->select($selects);
		return $this;
	} 

	/**
	* bőviti a már meglévő feltételek utolsó zárojeles blokkját 
	* AND fieldName rel value -val
	* a value érték magadásánál használd az sqlValue vagy sqlName fv.-t !
	* példa:
	*   ->where('a','>',1)
	*   ->where('b','=',2)
	*   ->orWhere('a','=',0)
	*   ->where('b','=',0)
	*   ->orWhere('c','=',11)
	*   SQL: WHERE (a > 1 AND b = 2) OR (a =0 AND B = 0) OR (c=11)   
	* @param string $fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string|number  érték|mezőnév|alias.mezőnév|'(lista)'
	* @return Query $this
	*/
	public function where(string $fieldName, string $rel, $value):Query {
		$i = count($this->unions) - 1;
		$this->unions[$i]->where($fieldName, $rel, $value);
		return $this;
	} 

	/**
	* új zárójeles blokkot képez fieldName rel value tratlommal,
	* OR -al kapcsolva a már meglévő zárojeles blokkhoz.
	* a value érték magadásánál használd az sqlValue vagy sqlName fv.-t !
	* példa:
	*   ->where('a','>',1)
	*   ->where('b','=',2)
	*   ->orWhere('a','=',0)
	*   ->where('b','=',0)
	*   ->orWhere('c','=',11)
	*   SQL: WHERE (a > 1 AND b = 2) OR (a =0 AND B = 0) OR (c=11)   
	* @param string $fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string|number  érték|mezőnév|alias.mezőnév|'(lista)'
	* @return Query $this
	*/
	public function orWhere(string $fieldName, string $rel, $value):Query {
		$i = count($this->unions) - 1;
		$this->unions[$i]->orWhere($fieldName, $rel, $value);
		return $this;
	} 

	/**
	* orderBy definiciót add hozzá 
	* @param string mezőnévvagy alias.mezőnév
	* @return Query $this
	*/
	public function orderBy(string $order):Query {
		$this->order = $order;
		return $this;
	} 

	/**
	* order dir definiciót add hozzá 
	* @param string 'ASC'|'DESC'
	* @return Query $this
	*/
	public function orderDir(string $orderDir):Query {
		$this->orderDir = $orderDir;
		return $this;
	} 

	/**
	* offset definiciót add hozzá 
	* @param int offset
	* @return Query $this
	*/
	public function offset(int $offset):Query {
		$this->offset = $offset;
		return $this;
	} 
	
	/**
	* group by definiciót ad hozzá
	* @param array order by (az elemekben szükség szerint `...`)
	* @return Query $this
	*/
	public function groupBy(array $groupBy): Query {
		$this->groupBy = $groupBy;
		return $this;	
	}

	/**
	* limit definiciót add hozzá 
	* @param int limit
	* @return Query $this
	*/
	public function limit(int $limit):Query {
		$this->limit = $limit;
		return $this;
	} 

	/*
	* új uniont ad hozzá
	* @param Query
	* @return Query $this
	*/
	public function addUnion(Query $union):Query {
		$this->unions[] = $union;
		return $this;
	} 

	/**
	* join -t ad hozzá
	* az ON érték megadásnál használd a sqlName vagy sqlValue fv-t!
	* @param string 'LEFT OUTER'|'RIFGHT OUTER'|'INNER'
	* @param string|Query from tábla vagy subselect
	* @param string alias
	* @param string ON fieldName
	* @param string '<'|'<='|'='|'<>'|'>='|'>'|in
	* @param string érték|alias.mező|(lista)
	* @return Query $this
	*/
	public function join(string $joinType, 
									$from, 
									string $alias, 
									string $onField, 
									string $onRel, 
									$onValue):Query {
		$i = count($this->unions) - 1;
		$this->unions[$i]->join($joinType, $from, $alias, $onField, $onRel, $onValue);
		return $this;
	} 

	/**
	* teljes sql stringet ad vissza
	* @return string
	*/	
	public function getSql(): string {
		if ($this->sql != '') {
			return $this->sql;		
		}
		$result = '';
		$w = '';
		foreach ($this->unions as $union) {
			$result .= $w.$union->getSql();
			$w = "\nUNION ALL\n";		
		}
		if (count($this->groupBy) > 0) {
			$result .= 'GROUP BY '.implode(',', $this->groupBy)."\n";		
		}
		if ($this->order != '') {
			$result .= 'ORDER BY '.sqlName($this->order);
			if (($this->order != '') & ($this->orderDir != '')) {
				$result .= ' '.$this->orderDir;
			}
			$result .= "\n";
		}	
		if ($this->limit > 0) {
			$result .= 'LIMIT '.$this->offset.','.$this->limit."\n"; 		
		}
		return $result;
	}
	
	public function setSql($s) {
		$this->sql = $s;	
	}
}


?>
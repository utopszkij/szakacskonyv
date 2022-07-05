# RATWEB keretrendszer program dokumentáció


## 1. Általános áttekintés
A RATWEB egy általános célra készült PHP-MYSQL-VUE keretrendszer. A laravel-nél kisebb erőforrás igényű, egyszerűbben telepíthető, ennek ellenére annak sok szolgáltatását nyújtja, szintaxisa több ponton hasonló ahoz. Így aki a laravel ismeri az könnyen megbarátkozik ezzel is, de a laravel ismerete nélkül is boldogulni fogsz vele. A keretnedszer Model – Viwer – Controller struktúra szerintépül fel (MVC). A mysql elérés egy egyedi, laravel re emlékeztető interfészen keresztül történik. A web  oldalak megjelenítéséhez VUE js  és bootstrap -ot használ.

class -ok dokumentációja: docroot/doc/html/index.html 

### 1.1. Adatbázis interfész
Az index.php gondoskodik a betöltéséről (vendor/database/db.php)
Alap objektuma:  **Query(„tableName”)**. 

Az interface feltételezei, hogy a rekordokban van egy integer **"id"** ami a **primary key** és **auto increment**.
A használatot példákon keresztül szemléltetjük.

### 1.2. Lekérdezések

#### 1.2.1.  Egy táblát érintő adatbázis lekérdezés
```
$db = new Query("tableName");
$record = $db->where("name",”=”,”valaki”)
	->where("state",">",1)
	->first();
// result = {"colName":colValue, ....} vagy {}

$state = 7;
$records = $db->select(["name",["state","s"], ["sum(adat)","sadat"]])
            ->where("name","<>","")
            ->orWhere("state","=",$state)
            ->groupBy(["id","name","state"])
            ->orderBy("name")
            ->offset(12)
            ->limit(20)
            ->all();
// result = [{"name":"...", "s":"...", "sadat":szám},...] vagy []

$count = $db->where("name","=","Gipsz")
        ->count();
// result : integer rekordok száma

```
#### 1.2.2.  Join -al összekapcsolt táblákat érintő adatbázis lekérdezés
```
$db = new Query("table1","t1");
$records = $db->select(["t1.id","t2.name"])
        ->join("LEFT OUTER","table2","t2","t2.id","=","t1.id")
        ->where("t1.id",">=",11)
        ->all();

```

#### 1.2.3.  Union -al összekapcsolt lekérdezés
```
$db1 = new Query("table1");
$db1->select(["id","name"])

$db2 = new Query("table2");
$records = $db2->select(["id","name"])
            ->where("id","<",20)
            ->union($db1)
            ->orderBy("nev")
            ->all();  

```

### 1.3. Adatbázis manipulációk

#### 1.3.1 Record felvitel
```
$record = new Record();
$record->id = 0;
$record->name = "nev1";
$db = new Query("tabla1");
$newId = $db->insert($record);  // id autoinc primary key)
```

#### 1.3.2 Record(ok) módosítása
```
$record = new Record();
$record->name = "nev1-modositva";
$db = new Query("tabla1");
$bool = $db->where("state","=",3)
    ->update($record);  
```

#### 1.3.3 Record(ok) törlése
```
$db = new Query("tabla1");
$bool = $db->where("state","=",3)
    ->delete();  
```

### 1.4. SQL script futtatás
```
$db = new Query("tabla1");
$db->exec("sql script");  
```

### 1.5. Hibakezelés

Minden hívás után ellenörizhető a **$db->error** public property. Ha ez "" akkor nem volt hiba, ellenkező esetben a hibaüzenetet tartalmazza.

## 2. MVC struktúra

Adat modellek: includes/models/{modelName}.php

Viewerek: includes/views/{viewName}.html

Controllerek: includes/controllers/{conrollerName}.php

### 2.1. Controller
```
<?php
use \RATWEB\DB\Query;
use \RATWEB\DB\Record;

include_once __DIR__.'/../models/valamimodel.php';
class Valami extends Controller {

	function __construct() {
        parent::__construct();
        $this->name = "valami";
        $this->model = new ValamiModel();
        $this->browserURL = 'index.php?task=valamik';
        $this->addURL = 'index.php?task=valamiadd';
        $this->editURL = 'index.php?task=valamiedit';
        $this->browserTask = 'valamik';
	}
	
    /**
     * rekord ellenörzés
     * @param Record $record
     * @return string üres vagy hibaüzenet
     */
    protected function validator($record):string {
        $result = '';
        .....
        return $result;
    }
    
    /**
     * bejelentkezett user jogosult erre?
     * @param string $action new|edit|delete|show
     * @return bool
     */
    protected function  accessRight(string $action, $record):bool {
        $result = false;
        ....
        return $result;
    }

    public function task1() {
        ....
    }

    ....
}
?>
```
lásd a vendor/controller.php -t is!

### 2.2. Model
```
<?php
    use \RATWEB\Model;
    use \RATWEB\DB\Query;
    use \RATWEB\DB\Record;

    class ValamiModel extends Model  {

        function __construct() {
            parent::__construct();
            $this->setTable = 'valami';
            $this->errorMsg = ''; 
        }

        // ha böngésző is van akkor kell  definiálni
        ```
        $items = $this->model->getItems($page,$limit,$filter,$order);

        $total = $this->model->getTotal($filter); 
        ```
        public metodusokat is

    }		

?>
```
Lásd a vendor/model.php -t is!

### 2.3. Viewer
A viewer hívása a controllerben:
```
    vuew("viewName",
        ["par1":value1, ....],
        "appname")
```
A harmadik paraméter elmaradhat, ez esetben "app" az alapértelmezése.

A html outputon egy 
```
<div id="appName">...</div> 
```
fogja tartalmazni a html templatet, és generálva lesz
a vuejs hívás a megadott paramétereket átadva.

A viewer fájlok formája:
```
<div>
.... vue elemeket is tartalmazhat, 
include viewName sorokat is tartalmazhat
.....
</div>
<script>
    var methods = {
        afterMount() {
        },
        .....
    }
</script>
```
Lásd a vendor/view.php -t is!

### 2.3.1 paginátor

A template -be a 
```
include paginator
``` 
sort kell elhelyezni és a controllerben a viewer-nek át kell adni 
** total ** összes rekord szám
** pages ** [1,2,3...] a teljes megjelenítéshet szükséges lapok felsorolása
** limit ** az egy lapon megjelenített sorok száma
** page ** az éppen megjelenített lap száma
** task ** a browser hívás task neve

## 3. Taskok

A taskok hívása az URL -be írt "task=” paraméterrel történik.
Két módszer támogatott:

a.  task=controllerName.taskName

b.  task=taskName

A „b” eljárás használata esetén a komponenseket az index.php -ban
```
include_once('vendor/fw.php');
importComponent(„componentName”); 
....
```
módon importálni kell, és a task neveknek az összes komponenst tekintetve egyedieknek kell lenniük.
A taskok a controllerek public methodusai.

### 3.1 SEO barát URL -ek
Ha a .htaccess kezelés és a RewriteEngine a szerveren engedélyezett akkor SEO barát URL -ek is
használhatóak:
```
http[s]://domain/path/task/taskName/parName1/parValue1/parName2/parValue2.....
például:
https://example.hu/task/recept.show/id/13
```

## 4. Megjelenés (css)
A documentRoot/style.css -el lehet a stilust kialakitani.
A task outputja
```
<div id="page">...</div>
```
-ben jelenik meg.











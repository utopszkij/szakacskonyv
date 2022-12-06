# Szakácskönyv
Recepteket és napi menüket lehet kezelni.
			
Ezek alapján a program adott időszak összesített anyagszükségleteit tudja meghatározni. 
Ebből bevásárló listát lehet a program segitségével készíteni.
			
## Tulajdonságok

- Recepthez hozzávalók, elkészítési leírás és kép vihető fel,
- egy recepthez max 30 hozzávaló adható meg,
- a program támogatja a mindmegette.hu -ról és a receptneked.hu -ról történő adatátvételt,
- a receptek módosíthatóak, törölhetőek,
- ha a recepthez képet nem adunk meg akkor a program a recept neve 
alapján megpróbál a net-en képet keresni,
- a receptek kinyomtathatóak,			
- napi menübe naponta max. 4 fogás vihető fel, megadható hány főre főzünk aznap,
- a napi menük módosíthatóak, törölhetőek,			
- a számított hozzávaló összesítés (bevásárló lista), nyomtatás előtt módosítható
(pl. törölhető amiből "van a spájzban").		
- a receptekhez hozáászólásokat lehet csatolni, a hozzászólások képeket is tartalmazhatnak	
- az összesítések optimális müködése érdekében a program egy szinonima szótárat és mértékegység
átváltó táblázatot használ. Ezek tartalmát csak a rendszer adminisztrátorok módosíthatják.
- responsive megjelenés
			
A program konfigurálható egyfelhasználós vagy többfelhasználós módba.
			
Több felhasználós módban mindenki csak a sajátmaga által felvitt napi menüket 
látja és ezeket kezelheti, az összesítés is ezek alapján készül. A recepteknél 
látja, használhatja a mások által felvitteket is, de modosítani, törölni csak a 
sajátmaga által felvitteket tudja. Illetve a rendszergazdák és moderátorok módosíthatják törölhetik a mások által felvitteket is. A hozzászólásokat mindenki láthatja, módosítani, tötölni 
csak a felvivő és rendszer adminisztrátorok, moderátorok tudnak.
A receptek, hozzászólások és képek tartalmáért, a kizárólag
az azokat felvivő felhasználó a felelős, a program szerzője és üzemeltetője
ezekkel kapcsolatban semmilyen felelősséget nem vállal.

### A programot mindenki csak saját felelősségére használhatja.
						
## Lecensz

GNU v3

## Müködő demo:

[https://szakacskonyv.nfx.hu](http://szakacskonyv.nfx.hu)
[https://befalom.hu](https://befalom.hu)

## képernyő képek

![napi menük](https://github.com/utopszkij/szakacskonyv/blob/main/images/kezdolap.png?raw=true)

![napi menü](https://github.com/utopszkij/szakacskonyv/blob/main/images/napimenu.png?raw=true)

![receptek](https://github.com/utopszkij/szakacskonyv/blob/main/images/receptek.png?raw=true)

![recept](https://github.com/utopszkij/szakacskonyv/blob/main/images/recept.png?raw=true)

![összesítés](https://github.com/utopszkij/szakacskonyv/blob/main/images/osszesites.png?raw=true)

![bevásárló lista](https://github.com/utopszkij/szakacskonyv/blob/main/images/bevlista.png?raw=true)


## Információk informatikusok számára      

A vendor könyvtár tartalmazza a felhasznált harmadik féltől származó fájlokat. 
Nem szeretem az ilyen fájlok más szerverről (pl. cdn) történő behívását, mert
ez esetben a fájlok fejlesztői álltal eszközölt változtatások könnyen a program
összeomlásához vezethetnek. Ugyanezen okból a források npm -el 
történő letöltését is ellenzem. Viszont így a rendszergazda felaladata a
harmadik féltől származó elemek változásainak nyomonkövetése, és szükség esetén
a (tesztelés, szükséges javítások után) a vendor könyvtrában történő cseréje.

## Szükséges sw környezet
### futtatáshoz
- web szerver   .htacces és rewrite támogatással
- php 7+ (mysqli kiegészítéssel)
- mysql 5+
### fejlesztéshez
- phpunit (unit test futtatáshoz)
- doxygen (php dokumentáció előállításhoz)
- nodejs (js unittesthez)
- php és js szintaxist támogató forrás szerkesztő vagy IDE

## Telepítés

- adatbázis létrehozása (utf8, magyar rendezéssel),
- config.php elkészítése a a config-example.php alapján,
- a views/impressum, policy, fájlok szükség szerinti módosítása
- fájlok és könyvtárak feltöltése a szerverre,
- az images könyvtár legyen irható a web szerver számára, a többi csak olvasható legyen,
- adatbázis kezdeti feltöltése a vendor/database/dbinit.sql segitségével,
- többfelhasználós üzemmód esetén; a program "Regisztrálás" menüpontjában hozzuk létre a
  a system adminisztrátor fiokot (a config.php -ban beállított bejelentkezési névvel).

Könyvtár szerkezet a futtató web szerveren:
```
[document_root]
  [images]
     kép fájlok
  [includes]
    [controllers]
      kontrollerek php fájlok
    [models]
      adat modellek php fájlok
    [views]
      viewer templates  spec. html fájlok. vue elemeket tartalmaznak
    [extras]
      task -tól függő extra includok  
    egyéb inlude fájlok
  [vendor]
    keretrendszer fájlok és harmadik féltől származó fájlok (több alkönyvtárat is tartalmaz)
  [styles]
    css fájlok  
  index.php  - fő program
  config.php - konfigurációs adatok
  files.txt  - a telepített fájlok felsorolása (az upgrade folyamat használja)

```  
index.php paraméterek nélküli hívása esetén a "naptar.php" -ben lévő "home" task futtatásával indul a program.

index.php?task=upgrade1&version=vx.x&branch=xxxx hívással a github megadott branch -et használva  
is tesztelhető/használható az upgrade folyamat.


## unit test

Telepiteni kell a phpunit és a nodejs rendszert.

[https://phpunit.de/](https://phpunit.de/)

[https://nodejs.org/en/](https://nodejs.org/en/)

Létre kell hozni egy test adatbázist, az éles adatbázissal azonos strukturával.

Létre kell hozni egy config_test.php fájlt az éles config.php alapján, a test adatbázishoz beállítva.

Ezután linux terminálban:
```
cd docroot
phpunit tests
./viewtest.sh
```
## software documentáció

[http://szakacskonyv.nfx.hu/doc/swdoc.html](http://szakacskonyv.nfx.hu/doc/swdoc.html)

## A sw. dokumentáció előállítása
telepiteni kell a doxygen dokumentáció krátort.

[https://doxygen.nl/](doxygen)  Köszönet a sw. fejlesztőinek.

A telepitési könyvtáraknak megfelelően módosítani kell documentor.sh fájlt.

Ezután linux terminálban:

```
cd docroot
./documentor.sh
```
## verzió v2.2.0
2022.12.06
- Új dizájn
### *************************************

## verzió v2.1.7
2022.12.02
- biztonsá./files.shgi rések elenörzése, javítása
### *************************************

## verzió v2.1.6
2022.12.01
- összesítés funkció hibajavítás
- dizájn fejlesztés
- befalom.hu domain -re költözés
### *************************************

## verzió v2.1.5
2022.11.23
- adat átvétel a sutnijo.hu és toprecept.hu oldalakról
- fő,menü javítása
### *************************************

## verzió v2.1.4
2022.11.15
- technikai jellegű javítás a látogatottság statisztikában
- facebook megosztás gomb a recpt képernyőre
### *************************************

## verzió v2.1.3
2022.11.06
- speciális karakterek megjelenési hájának a javítása (cikk, recept leírás, kommentek)
- recept leírásokban, cikkekben, kommentekben :)  :(  :D  :|  ;)  ;(  hangulatjelek használhatóak
- "Vélemények" menüpont a láblécben
### *************************************

## verzió v2.1.2
2022.11.03
- kép fájl feltöltési hiba javítása
- főmenü login/regist/logout módosítása
- admin felületen a grafikonok "x" tengely tartomány lapozható
### *************************************

## verzió v2.1.1
2022.10.30
- Új admin felület
- dizájn javítások
### *************************************

## verzió v2.1.0
2022.10.27
- Új admin felület dizájn javítások
- cikk keresés hiba javítás
### *************************************

## verzió v2.0.5
2022.10.25
- receptek is like-olhatóak
- recept like bajnokság
- friss hír a kezdő lapon 
### *************************************

## verzió v2.0.4
2022.10.21
- dizájn fejlesztés, 
- recept böngésző kép betöltés gyorsítása
- recept böngésző "új receptek" összecsukható/kinyitható
- recept törlés gomb a recept megjelenitő képernyőn
### ************************************

## verzió v2.0.3
2022.10.30
- delete hiba javítása, 
- Cikekbe és recept leírásokba youtube,vimeo és tiktok videó illeszthető be
- Cikkk editoron lehetőség van a html kód modosítására./files.s
### ************************************

## verzió v2.0.2
2022.10.30
- Regisztrálási hiba javítása, 
- Cikekbe és recept leírásokba youtube és vimeo videó illeszthető be
### ************************************

## verzió v2.0.1
2022.10.30
- Mobil megjelenés javítása, 
- "Böngésző refresh esetenként felvitt adatott dupláz" hiba javítása
### ************************************

## verzió v2.0.0
2022.10.28
- Cikkek (blog) rendszer commenttel és like -al.
### ************************************
## verzió v1.6.3
2022.09.21
- Recept átvétel hiba javítás
)
### ************************************
## verzió v1.6.2
2022.09.21
- Hiba javítások,
)
### ************************************
## verzió v1.6.1
2022.09.17
- Hiba javítások,
- Világos/sötét mód
)
### ************************************
## verzió v1.6
2022.09.17
- új receptek kiemelése,
- kedvenc receptek kezelése
- user avatar alapértelmezése
- net-ről keresett recept képek mentése az image könyvtárba (az új verzó telepítése után ez átmenetileg lassulást okoz, de utána gyorsabb lesz a megjelenítés
)
### ************************************
## verzió v1.5.8
2022.09.10
- csempe dizájn fejlesztése
- megosztás gombok
### ************************************
## verzió v1.5.7
2022.09.05
- csempe dizájn a recept lista helyett
### ************************************
## verzió v1.5.6
2022.08.13
- dizájn fejlesztés, apróbb javítások
### ************************************
## verzió v1.5.5
2022.07.28
- dizájn fejlesztés
- apróbb javítások
- támogatási lehetőség
### *************************************
## verzió v1.5.4
2022.07.19.
- dizájn fejlesztés
- keretrendszer fejlesztése (controller.mustLogin)
### *************************************

## verzió v1.5.3
2022.07.11.
- dizájn fejlesztés
- facebook megosztás gomb
### *************************************
## verzió v1.5.2
2022.07.09.
- recept lekérdező képernyőn az adag szám módosítható (mennyiségek átszámítódnak)
- refactoring
- dizájn fejlesztés
- php dokumentáció beillesztése 
- unitt est keretrendszer, unittest examples 
### *************************************

## verzió v1.5.1
2022.06.30.
- dizájn fejlesztés
- energia tartalom átvétele a nosalty -ról
### *************************************

## verzió v1.5.0
2022.06.28.
- recept átvátel a nosalty.hu -ól
- "+Hozzávaló" funkció hibajavítás
### *************************************

## verzió v1.4.1
2022.06.25.
- upgrade modul hiba javitás
### *************************************

## verzió v1.4
2022.06.25.
- cimkek.txt helyett adatbázis
- cimkék browser/editor a beállítás menübe
- facebook/google login javítása
- mobiltelefonos dizájn javítása
### ************************************

## verzió v1.3
2022.06.22
- receptneked.hu átvétel fejlesztése szinonimák és mertekegysegek lista kezelése
- recept megjelenítés fejlesztése (egész számok, hosszú hozzávaló nevek)
- user profilok kezelése (avatar kép, jelszó változtatás csoportok: regisztrált, moderátor, admin)
### ***************************************

## verzió v1.2
2022.06.14.
- recept átvehető a receptneked.hu oldalról is
- lapozó sor fejlesztése (első/utolsó/következő/elözö max 5 szomozott elem)
- mértékegység átváltás
### ***************************************

## verzió v1.1
2022.06.07
- újverzió kezelés csak admin számára jelenik meg
- változott fájlok listája nem a readme.md alapján hanem a files.txt alapján történik,
    a files.txt a fejlesztő környezetben a ./files.sh commanline paranccsal állítható elő
- dbupgrade funkció átkerült az upgrade komponensbe
- az energia mértékegysége az SI -ben Joul, itt "kJ" ez a receptkep.html -ben lett javítva
- comment rendszer
### ***************************************

## verzió v1.0
2022.05.30
- MVC struktúra, VUE form template
- 30 összetevő vihető fel egy recepthez
- Recept energia tartalom, elkészitési idő, adag kezelése
- recept cimkézés felvitelnél, módositásnál, törlésnél, keresésnél
- vendor/database/dbinit.sql
- index.php
- readme.md
- style.css
- includes/cimkek.txt
- includes/atvesz.php
- includes/controllers/index.php
- includes/controllers/napimenu.php
- includes/controllers/naptar.php
- includes/controllers/osszegzes.php
- includes/controllers/recept.php
- includes/controllers/upgrade.php
- includes/controllers/szovegek.php
- includes/controllers/user.php
- includes/models/model.php
- includes/models/receptmodel.php
- includes/models/napimenumodel.php
- includes/views/view.php
- includes/views/receptek.html
- includes/views/receptkep.html
- includes/views/napimenukep.html
- vendor/vue.global.js
- [del]includes/napimenu.php
- [del]includes/naptar.php
- [del]includes/osszegzes.php
- [del]includes/recept.php
- [del]includes/szovegek.php
- [del]includes/upgrade.php
- [del]includes/user.php

## verzió v0.2 
2022.05.21.
- program frissités kezelése (jelzi ha van új verzió, és kiirja a frissitendő fájlok listáját)
- admin user recept törlési lehetőség hibájának javítása
- facebook / google bejelentkezés hibájának javítása
- admin user recept modositással kapcsolatos hibajavitás
- napi összesítéssel kapcsolatos hibajavítás
### változott fájlok
- index.php
- readme.md
- style.css
- includes/upgrade.php
- includes/napimenu.php

## verzió v0.1 
- mindmegette.hu átvétel javítása
- recepet böngésző lapozás és szűrés
### változott fájlok
- index.php
- readme.md
- vendor/database/db.php
- includes/user.php
- includes/recept.php
- includes/atvesz.php






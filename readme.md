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

[http://szakacs.great-site.net](http://szakacs.great-site.net)

## képernyő képek

![napi menük](https://github.com/utopszkij/szakacskonyv/blob/main/images/kezdolap.png?raw=true)

![napi menü](https://github.com/utopszkij/szakacskonyv/blob/main/images/napimenu.png?raw=true)

![receptek](https://github.com/utopszkij/szakacskonyv/blob/main/images/receptek.png?raw=true)

![recept](https://github.com/utopszkij/szakacskonyv/blob/main/images/recept.png?raw=true)

![összesítés](https://github.com/utopszkij/szakacskonyv/blob/main/images/osszesites.png?raw=true)

![bevásárló lista](https://github.com/utopszkij/szakacskonyv/blob/main/images/bevlista.png?raw=true)


## Információk informatikusok számára      

## Szükséges sw környezet
- apache web szerver
- php 7+ (mysqli kiegészítéssel)
- mysql 5+

## Telepítés

- adatbázis létrehozása (utf8, magyar rendezéssel),
- config.php elkészítése a a config-example.php alapján,
- az includes/szovegek.php fájl szükség szerinti módosítása (impresszum, adatkezelési leírás),
- fájlok és könyvtárak feltöltése a szerverre,
- az images könyvtár legyen irható a web szerver számára, a többi csak olvasható legyen,
- adatbázis kezdeti feltöltése a vendor/database/dbinit.sql segitségével,
- többfelhasználós üzemmód esetén; a program "Regisztrálás" menüpontjában hozzuk létre a
  a system adminisztrátor fiokot (a config.php -ban beállított bejelentkezési névvel).

Könyvtár szerkezet
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
    atvesz.php - átvétel a mindmegette.hu -ról
    cimkek.txt - cimke lista
    szininima.txt       - szinonimák amiket a recept átvételnél használ
    mertekegysegek.txt  - mértékegység lista. a recept átvételnél van szerepe
  [vendor]
    keretrendszer fájlok és harmadik féltől származó fájlok (több alkönyvtárat is tartalmaz)
  index.php  - fő program
  config.php - konfigurációs adatok
  style.css  - megjelenés
  readme.md  - ez a fájl
  LICENSE    - licensz
  files.sh   - a files.txt -t előállító command. Csak fejlesztői környezetben kell és szabad        
               használni!
  files.txt  - a telepített fájlok felsorolása, az upgrade folyamat használja
```  

index.php hívással a "welcome" komponens betöltésével indul a program.

index.php?task=upgrade1&version=vx.x&branch=xxxx hívással a github megadott branch -et használva  
is tesztelhető/használható az upgrade folyamat.

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






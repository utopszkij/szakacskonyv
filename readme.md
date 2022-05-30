# Szakácskönyv
 - includes/models/receptmodel.php
és napi menüket lehet kezelni.
			
Ezek alapján a program adott id- includes/models/receptmodel.php
szak összesített anyagszü- includes/models/receptmodel.php
kségleteit tudja meghatározni. 
Ebből bevásárló listát lehet a program segitségével készíteni.
			
## Tulajdonságok

- Recepthez hozzávalók, elkészítési leírás és kép vihető fel,
- egy recepthez max 15 hozzávaló adható meg,
- a program támogatja a mindmegette.hu -ról történő adatátvételt,
- a receptek módosíthatóak, törölhetőek,
- ha a recepthez képet nem adunk meg akkor a program a recept neve 
alapján megpróbál a net-en képet keresni,
- a receptek kinyomtathatóak,			
- napi menübe naponta max. 4 fogás vihető fel, megadható hány főre főzünk aznap,
- a napi menük módosíthatóak, törölhetőek,			
- a számított hozzávaló összesítés (bevásárló lista), nyomtatás előtt módosítható
(pl. törölhető amiből "van a spájzban").			
			
A program konfigurálható egyfelhasználós vagy többfelhasználós módba.
			
Több felhasználós módban mindenki csak a sajátmaga által felvitt napi menüket 
látja és ezeket kezelheti, az összesítés is ezek alapján készül. A recepteknél 
látja, használhatja a mások által felvitteket is, de modosítani, törölni csak a 
sajátmaga által felvitteket tudja.
			- includes/models/receptmodel.php
ceptek és képek tartalmáért, a kizárólag
az azokat felvivő felhasználó a felelős, a program szerzője és üzemeltetője
ezekkel kapcsolatban semmilyen felelősséget nem vállal.
			
index.php hívással a "welcome" komponens betöltésével indul a program.

index.php?page=xxxx hívással a "xxxx" vue komponens betöltésével is inditható a program.

### A programot mindenki csak saját felelősségére használhatja.
			
## Szükséges sw környezet
- apache web szerver
- php 7+ (mysqli kiegészítéssel)
- mysql 5+

## Telepítés

- adatbázis létrehozása (utf8, magyar rendezéssel),
- config.php elkészítésa a config-example.php alapján,
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
  [vendor]
    keretrendszer fájlok, harmadik féltől származó fájlok (több alkönyvtárat is tartalmaz)
  index.php  - fő program
  config.php - konfigurációs adatok
  style.css  - megjelenés
  readme.md  - ez a fájl
  LICENSE    - licensz
```  

## Lecensz- includes/models/receptmodel.php

[http://szakacs.great-site.net](http://szakacs.great-site.net)

## képernyő képek

![napi menük](https://github.com/utopszkij/szakacskonyv/blob/main/images/kezdolap.png?raw=true)

![napi menü](https://github.com/utopszkij/szakacskonyv/blob/main/images/napimenu.png?raw=true)

![receptek](https://github.com/utopszkij/szakacskonyv/blob/main/images/receptek.png?raw=true)

![recept](https://github.com/utopszkij/szakacskonyv/blob/main/images/recept.png?raw=true)

![összesítés](https://github.com/utopszkij/szakacskonyv/blob/main/images/osszesites.png?raw=true)

![bevásárló lista](https://github.com/utopszkij/szakacskonyv/blob/main/images/bevlista.png?raw=true)

## verzió v1.0
2022.06.30
- MVC struktúra, VUE form template
- 30 összetevő vihető fel egy recepthez
- Recept energia tartalom, elkészitési idő, adag kezelése
- recept cimkézés felvitelnél, módositásnál, törlésnél, keresésnél
### változott fájlok
- index.php
- readme.md
- vendor/database/dbinit.sql
- includes/cimkek.txt
- includes/atvesz.php
- includes/controllers/index.php
- includes/controllers/napimenu.php
- includes/controllers/naptar.php
- includes/controllers/osszegzes.php
- includes/controllers/recept.php
- includes/controllers/szovegek.php
- includes/controllers/upgrade.php
- includes/controllers/user.php
- includes/models/model.php
- includes/models/receptmodel.php
- includes/models/napimenumodel.php
- includes/views/view.php
- includes/views/receptek.html
- includes/views/receptkep.html
- includes/views/napimenukep.html
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






# Szakácskönyv
 
A programba étel recepteket és napi menüket lehet kezelni.
			
Ezek alapján a program adott időszak összesített anyagszükségleteit tudja meghatározni. 
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
			
			
A felhasználók által felvitt receptek és képek tartalmáért, a kizárólag
az azokat felvivő felhasználó a felelős, a program szerzője és üzemeltetője
ezekkel kapcsolatban semmilyen felelősséget nem vállal.
			

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

## Lecensz

GNU/GPL

## Felhasznált opensource termékek

- PHP	v7  [https://php.net](https://php.net)
- MYSQL v5 [https://mysql.com](https://mysql.com)	  
- Bootstrap v5.1.3 [https://bootstrap.com/](https://bootstrap.com/)	  
- Font awesome 5.14.4 [https://fontawesome.com](https://fontawesome.com)	  

## Müködő web hely:

[http://szakacskonyv.great-site.net](http://szakacskonyv.great-site.net)

## képernyő képek

![napi menük](https://github.com/utopszkij/szakacskonyv/blob/main/images/kezdolap.png?raw=true)

![napi menü](https://github.com/utopszkij/szakacskonyv/blob/main/images/napimenu.png?raw=true)

![receptek](https://github.com/utopszkij/szakacskonyv/blob/main/images/receptek.png?raw=true)

![recept](https://github.com/utopszkij/szakacskonyv/blob/main/images/recept.png?raw=true)

![összesítés](https://github.com/utopszkij/szakacskonyv/blob/main/images/osszesites.png?raw=true)

![bevásárló lista](https://github.com/utopszkij/szakacskonyv/blob/main/images/bevlista.png?raw=true)







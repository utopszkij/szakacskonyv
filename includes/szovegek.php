<?php
function impresszum() {
	?>
	<div id="impresszum">
	  <em class="fas fa-info-circle" style="font-size:64pt; float:right"></em>
	  <h1>Impresszum</h1>
	  <h2>Szakácskönyv</h2>
	  <p>Recept és napi menü kezelő, anyagszükséglet számító program</p>
	  <p></p>
	  <h2>Szerző</h2>
	  <p>Fogler Tibor</p>
	  <p>tibor.fogler@gmail.com</p>
	  <h2>Adat kezelő</h2>
	  <p>Fogler Tibor</p>
	  <p>tibor.fogler@gmail.com</p>
	  <h2>Adat feldolgozó</h2>
	  <p>infinityfree.net</p>
	  <p></p>
	  <h2>Felhasznált opensource termékek</h2>
	  <ul>
			<li>PHP v7 (https://php.net)</li>	  
			<li>MYSQL v5 (https://mysql.com)</li>	  
			<li>Bootstrap v5.1.3 (https://getbootstrap.com/)</li>	  
			<li>Font awesome 5.14.4 (https://fontawesome.com)</li>	  
	  </ul>	
	</div>
	<?php
}

function adatkezeles() {
	?>
	<div id="adatkezeles">
	  <em class="fas fa-lock" style="font-size:64pt; float:right"></em>
	  <h1>Adatkezelési leírás</h1>
	  <p>A program személyes adatokat nem kezel.</p>	
	  <p>Kezelt adatok:</p>	
	  <p>bejelentkezési név, jelszó, recept adatok, napi menük</p>	
	  <p></p>	
	  <p>A jelszót magát a program nem tárolja a szerveren, csak annak "hash" kódját tárolja.</p>	
	  <p></p>	
	  <p>A recept adatok (név, hozzávalók, leírás, kép) minden a web oldalra 
	  látogató számára láthatóak.</p>	
	  <p>A napi menü adatokat csak az látja aki azokat felvitte.</p>
	  <p>A recept adatokat csak az azokat felvivő módosíthatja, a rendszergazda és
	  a felvivő törölheti.</p>
	  <p>A napi menü adatokat csak az azokat felvivő módosíthatja, törölheti</p>
	  <p>Az összesítések eredményeit és az ezekből kialakított bevásárló listákat
	  a program nem tárolja. Azokat csak az összesítést lekérő felhasználó látja.</p>	
	</div>
	<?php
}

function licensz() {
	?>
	<div id="licensz">
		<em class="fas fa-copyright" style="float:right; margin:10px; font-size:64px" /></em>  
		<h1>GNU General Public Licensz -- v2.0</h1>
		Ez a GNU General Public License nem hivatalos magyar fordítása. A fordítást nem a Free Software Foundation tette közzé és jogi értelemben nem határozza meg a GNU GPL révén jogosított szoftverek terjesztési feltételeit – e tekintetben csak a GNU GPL angol nyelvű verziója irányadó.
		<br />
		<br />http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
		<br />GNU General Public License (GPL)
		<br />Copyright (C) 1989, 1991 Free Software Foundation, Inc. 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA
		<br />Copyright (C) 1989, 1991 Free Software Foundation, Inc. 675 Mass Ave, Cambridge, MA 02139, USA
		<br /><br />
		<h2>Előszó</h2>
		<br />
		<br />A legtöbb szoftver licenceit azzal a szándékkal készítették, hogy Öntől a szoftver átdolgozásának és terjesztésének szabadságát elvonják. Ezzel szemben a GNU General Public License célja az, hogy garantálja az Ön számára a szabad szoftver átdolgozásának és terjesztésének szabadságát, ezáltal biztosítva a szoftver szabad használatát minden felhasználó számára. Ennek a General Public Licensenek a szabályai vonatkoznak a Free Software Foundation szoftvereinek nagy részére, illetve minden olyan programra, melynek szerzője úgy dönt, hogy ezt a licencet használja a felhasználási mód megjelölésekor. (A Free Software Foundation szoftvereinek egy másik részére a GNU LesserGeneral Public License vonatkozik.) Bárki engedélyezheti programjai felhasználását a General Public License-szel.
		<br />
		<br />A szabad szoftver megjelölés a szabadságra vonatkozik, és nem az árra. A General Public License-szek célja, hogy biztosítsa az Ön számára a szabad szoftver többszörözésének és terjesztésének jogát (és e szolgáltatásért akár díj felszámítását), a forráskód átadását (vagy igény szerint hozzáférést ehhez a kódhoz), a szoftver átdolgozásának lehetőségét, illetve hogy a szoftver egyes részeit új szabad programokban használhassa fel. és hogy e lehetőségekkel tudatosan élhessen.
		<br />Az Ön jogai védelmében hozott korlátozások azok, amelyek megakadályozzák, hogy valaki e jogok gyakorlását Önnek megtilthassa, vagy Önt ezekről lemondásra/tartózkodásra kényszeríthesse. Az ezekből a korlátozásokból eredő felelősség Önt is terheli, amennyiben a szoftver műpéldányokat terjeszti, illetve átdolgozza.
		<br />
		<br />Amennyiben Ön például ilyen [licenccel ellátott] program másolatait terjeszti, akár ingyenesen, akár bizonyos díj fejében, a szoftverrel kapcsolatos valamennyi jogot köteles átruházni a [harmadik személy] felhasználónak. Meg kell továbbá győződnie arról, hogy a [harmadik személy] felhasználó számára a forráskód, vagy a kódhoz jutás lehetősége biztosított. És annak érdekében, hogy a [harmadik személy] felhasználó megismerje jogait, ismertetnie kell vele a felhasználás kereteit meghatározó jelen licencet.
		<br />
		<br />Az Ön jogait két lépésben védjük:
		<br />(1) a szoftvereket szerzői oltalom alá helyezzük és
		<br />(2) felkínáljuk Önnek jelen licencet, amely jogosítja Önt a szoftver többszörözésére, terjesztésére és/vagy átdolgozására
		<br />
		<br />A szerző és a magunk [FSF] védelmében biztosítani kívánjuk továbbá, hogy mindenki úgy értelmezi, hogy erre szabad szoftverre nincs szavatosság. Ha a szoftvert átdolgozták és továbbadták, akkor mindenkinek, aki az átdolgozott változatot kapja, tudnia kell, hogy az nem az eredeti, így a mások által okozott hibák nem sérthetik az eredeti szerző hírnevét.
		<br />
		<br />Végül és utolsó sorban, valamennyi a szabad szoftver létét folyamatosan fenyegetik a szoftverszabadalmak. El kívánjuk kerülni annak veszélyét, hogy a szabad programra terjesztői egyedileg szabadalmi oltalmat igényelhessenek, és ezáltal a programot kisajátítsák. Ennek megakadályozásához tisztázni kívánjuk: szabadalom szabad szoftverrel kapcsolatban csak mindenkire vonatkozó hasznosítási joggal jegyezhető be, vagy egyáltalán nem jegyezhető be.
		<br />
		<br />A többszörözésre, terjesztésre, átdolgozásra vonatkozó konkrét szabályok és feltételek:
		<br /><br />
		<h2>A TÖBBSZÖRÖZÉSRE, TERJESZTÉSRE ÉS ÁTDOLGOZÁSRA VONATKOZÓ FELTÉTELEK ÉS KIKÖTÉSEK</h2>
		<br /><br />0. Ez a licenc minden olyan programra vagy más műre vonatkozik, amelyen a vagyoni jog jogosultja utal arra, hogy a mű a General Public License-ben foglaltak alapján terjeszthető. Az továbbiakban a “Program” kifejezés bármely ilyen programra vagy műre vonatkozik, a “Programon alapuló mű” pedig magát a programot , illetve bármely, annak a szerzői jog által védett átdolgozását jelenti: vagyis olyan művet, amely tartalmazza a Programot vagy annak egy részletét, átdolgozott vagy átdolgozástól mentes formában és/vagy más nyelvre fordítva. (A továbbiakban a fordítás minden egyéb megkötés nélkül beletartozik az átdolgozás fogalmába.) Minden felhasználási engedély jogosultjának (licencbevevő) megjelölése a továbbiakban: “Ön”.
		<br />A jelen licenc a többszörözésen, terjesztésen és átdolgozáson kívül más felhasználási módra nem vonatkozik, azok az engedélyezési körön kívül esnek. A Program futtatása nincs korlátozva, illetve a Program eredményeire is csak abban az esetben vonatkozik ez a szabályozás, ha az tartalmazza a Programon alapuló mű egy részletét (függetlenül attól, hogy ez a Program futtatásával jött-e létre). Ez tehát a Program működésétől függ.
		<br /><br />1. Ön a Program forráskódját átdolgozás nélkül többszörözheti és tetszőleges adathordozón terjesztheti, feltéve, hogy minden egyes példányon szembetűnően pontosan feltünteti a megfelelő szerzői jogi megjegyzést, illetve a garanciavállalás kizárását; érintetlenül kell hagynia minden erre a licencre és a garancia teljes hiányára utaló szöveget, továbbá a jelen licencfeltételeket is el kell juttatnia mindazokhoz, akik a Programot kapják.
		<br />A másolati példányok fizikai továbbítása fejében díjat kérhet, a Programhoz nyújthat anyagi ellentételezése fejében garanciális támogatást.
		<br /><br />2. Ön jogosult a Program másolatának vagy másolatainak vagy egy részének átdolgozására, amely következtében egy, a Programon alapuló mű jön létre. Az így keletkezett, átdolgozott művet ezt követően az 1. szakaszban adott feltételek szerint többszörözheti és terjesztheti, amennyiben az alábbi feltételek is teljesülnek :
		<br />a) Az átdolgozott fájlokat el kell látnia olyan feltűnő megjegyzéssel, amely tartalmazza, hogy Ön végezte az átdolgozást és rögzíti az átdolgozás dátumát.
		<br />b) Gondoskodnia kell arról, hogy minden, az Ön által terjesztett vagy nyilvánosságra hozott mű , amely részben vagy egészben tartalmazza a Programot, illetve a Program átdolgozásával jött létre, valamennyi harmadik személy számára, egységként jelen licencben meghatározott feltételek szerint díjmentesen kerüljön engedélyezésre.
		<br />c) Ha az átdolgozott Program alapesetben futtatáskor interaktív parancsokat olvas be, gondoskodnia kell arról is, hogy amennyiben ilyen interaktív felhasználás a megszokott módon kerül indításra, jelenítsen meg vagy nyomtasson ki egy, a szerzői jogi kitételeket tartalmazó megjegyzést, valamint egy utalást a szavatosság igények kizárására (vagy éppen arra, hogy Ön milyen feltételekkel biztosítja a garanciát), illetve arra, hogy e feltételek betartása mellett a felhasználó terjesztheti a Programot. A felhasználót tájékoztatni kell arról is, hogy miként ismerheti meg a licenc egy példányát. (Kivétel: ha a Program interaktív ugyan, de normál körülmények között nem jelenít meg hasonló megjegyzést, akkor a Programon alapuló műnek sem kell ezt tennie.)
		<br />Ezek a feltételek az átdolgozott műre, mint egészre vonatkoznak. Ha a mű azonosítható részei nem a Programon alapulnak/nem vezethetőek le a Programból és önálló műként elkülönülten azonosíthatók, akkor ez a felhasználási engedély nem vonatkozik ezekre a részekre, amennyiben ezeket Ön önálló műként terjeszti.
		<br />De ha ugyanezeket a részeket egy olyan mű részeként terjeszti, amely a Programon alapul, az egész terjesztésének ezen a szerződésen kell alapulnia, amely szerződésnek az engedélyei a többi felhasználóra is mint teljes egészre kiterjednek, és így minden részre is, függetlenül attól, hogy ki írta azokat.
		<br />E bekezdésnek tehát nem az a célja, hogy a művekkel kapcsolatos szerzői jogokat érvényesítse, vagy hogy a jogait az Ön által alkotott művel kapcsolatban vitassa. Sokkal inkább az a cél, hogy a Programon alapuló származékos, vagy gyűjteményes művek terjesztésének ellenőrzésére vonatkozó jogokat gyakorolja.
		<br />E felhasználási engedély nem vonatkozik más művekre, amelyek nem a Programon alapulnak, de a Programmal (vagy a Programon alapuló művel) azonos adathordozón kerülnek tárolásra, terjesztésre.
		<br /><br />3. Ön jogosult a Program (vagy a 2. szakasz értelmében a Programon alapuló mű) többszörözésére és terjesztésére tárgykódú vagy futtatható kódú formában az 1. és 2. szakaszban foglaltak szerint, feltéve, hogy az alábbi feltételeket is teljesíti:
		<br />a) A programhoz mellékelje a teljes, gép által értelmezhető forráskódot egy jellemzően e célt szolgáló adathordozón , amely az 1. és 2. szakaszban foglaltak szerint kerül terjesztésre, vagy
		<br />b) A programhoz mellékeljen egy legalább 3 évre vonatkozó írásbeli kötelezettségvállalást, amely alapján bármely harmadik személy rendelkezésére bocsátja a teljes gép által értelmezhető forráskódot a hordozó eljuttatásának költségét meg nem haladó díj fejében, amely az 1. és 2. szakaszban foglaltak szerint kerül terjesztésre, jellemzően e célt szolgáló adathordozón; vagy
		<br />c) A Programhoz mellékelje azt a forráskód rendelkezésre bocsátására vonatkozó írásbeli kötelezettségvállalást, amelyet Ön is megkapott. (Ez az alternatíva csak nem kereskedelmi terjesztés esetén alkalmazható, és akkor is csak abban az esetben, ha Ön a Programot tárgyi vagy futtatható kódban a b. cikkelynek megfelelő írásbeli kötelezettségvállalással kapta.)
		<br />Egy mű forráskódja a műnek azt a formáját jelenti, amely az átdolgozásra elsődlegesen alkalmas. Egy futtatható program a teljes forráskódot jelenti: valamennyi a program által tartalmazott modul forráskódját, továbbá a csatlakozófelület (interface) leírásait tartalmazó fájlokat éppúgy, mint a fordító- és telepítőszkripteket. Mindazonáltal, speciális kivételként, a terjesztett forráskódnak semmi olyasmit nem kell tartalmaznia, amit általában a binárist futtató operációs rendszer fő komponenseivel (compiler, kernel, stb.) terjesztenek (forráskód, vagy bináris formában), kivéve, ha maga az adott komponens a futtatható állományt kíséri.
		<br />Ha a futtatható program vagy tárgykód terjesztése akként történik, hogy egy erre kijelölt helyen másolási jogot biztosítanak, az a forráskód terjesztésének minősül. Akkor is terjesztésről beszélünk, ha a kódhoz (forráskódként, illetve futtatható formában) ezzel egyenértékű hozzáférést biztosítanak abban az esetben is, ha harmadik személyek nem kötelesek a forráskódot a tárgykóddal együtt lemásolni.
		<br /><br />4. Ön a nem jogosult a Programot többszörözni, átdolgozni/megváltoztatni, tovább [harmadik személy részére] licencelni (allicencebe adni) vagy terjeszteni, amennyiben Önt e licenc erre kifejezetten nem jogosítja. A többszörözés, átdolgozás, allicencbe adás, terjesztés valamennyi más módja semmis és automatikusan a licenc által megszerzett jogok elvesztését vonja maga után. Mindazonáltal azon harmadik személyek jogviszonya/felhasználási engedélye, akik Ön által e licenc hatálya alatt másolatot vagy jogot szereztek, nem szűnik meg, mindaddig, amíg e licencet teljes mértékben elismerik és betartják.
		<br /><br />5. Ön nem köteles ezen licencet elfogadni, hiszen nem írta alá. Azonban semmilyen más módon nem nyílik joga, hogy a Programot vagy a Programon alapuló művet átdolgozza vagy terjessze. E cselekményeket – amennyiben e licencet nem ismeri el - a törvény tiltja. Azáltal, hogy a Programot (vagy a Programon alapuló művet) átdolgozza vagy terjeszti, jelen licenccel és annak minden a Program többszörözésére, terjesztésére és átdolgozására vonatkozó feltételével való egyetértését nyilvánítja ki.
		<br /><br />6. Minden alkalommal, amikor Ön a Programot (vagy az azon alapuló művet) továbbadja, a fogadó fél automatikusan az eredeti vagyoni jogi jogosulttól (licencbeadó) kap felhasználási engedélyt (licenc) arra, hogy a Programot az itt meghatározott feltételeknek megfelelően többszörözhesse, terjeszthesse, és átdolgozhassa. Ön nem jogosult semmilyen módon a fogadó felet (felhasználót) megillető jogokat a továbbiakban korlátozni. Ön nem felel azért, hogy harmadik személyek jelen licencet betartsák.
		<br /><br />7. Amennyiben Önnek egy bírósági ítélet, szabadalomsértés vélelme, vagy más (nem szabadalmi kérdésekre korlátozódó) okból olyan feltételeknek kell megfelelnie (akár bírósági határozat, akár egyezség vagy bármi más eredményeképp) amelyek jelen Licenc feltételeivel ellentétesek, Ön nem mentesül jelen licenc rendelkezései alól. Amennyiben nem lehetséges, hogy a Programot egyidejűleg a licenc feltételeinek és a másrészről keletkezett kötelezettségeinek figyelembe vétele mellett terjessze, akkor Ön a Program terjesztésére egyáltalán nem jogosult. Ha például egy szabadalom nem teszi lehetővé, hogy azok, akik a Programot közvetlen vagy közvetett módon Öntől kapták meg a díjmentes továbbterjeszthessék, az egyetlen választható megoldás az marad, annak érdekében, hogy a szabadalmi jogot és ezen licencet is kövesse, ha a program terjesztésétől teljes mértékben tartózkodik/ eláll.
		<br />Ha ezen paragrafusok egy része érvénytelennek vagy bizonyos körülmények között érvényesíthetetlennek minősülne, ezen Paragrafust értelemszerűen kell alkalmazni; a többi esetben a Paragrafust, mint egészet kell érvényesíteni.
		<br />Ezen Paragrafusok célja nem az hogy Önt valamely szabadalom vagy más tulajdonjogi igény megsértésére ösztönözze, illetve hogy ezen igények hatályosságát/jogosságát vitassa; e Paragrafus egyetlen célja, hogy a szabad szoftverek terjesztési rendszerének integritását – amely a nyilvános licencek gyakorlata által valósul meg – megóvja. Jelen rendszer keretében terjesztett szoftverek jelentős kínálatához sok ember nagylelkű felajánlással, a rendszer következetes működésében bízva járult hozzá; a Szerzőn/Vagyoni jogi jogosult joga eldönteni, hogy a szoftvert valamely másik rendszer keretében is terjeszteni kívánja-e, a felhasználónak (licencebe vevőnek) erre a döntésre semmilyen ráhatása nincs.
		<br />E szakasz célja az, hogy pontosan tisztázza, mit kell következtetésként/következményként a licenc hátralévő részéből figyelembe venni.
		<br /><br />8. Ha a Program terjesztése és/vagy használata egyes országokban akár szabadalmak, akár szerzői jogi oltalom alatt álló csatlakozó felületek (interface) miatt korlátozott, akkor a Program szerzői jogi jogosultja, aki a Programot e licenccel tette közzé/e licenc alá helyezte, a terjesztést kifejezett területi korlátozással láthatja el. Amelyben bizonyos országok kizárásra kerülnek, a terjesztés csak olyan országokra vonatkozóan lesz engedélyezett, amelyek nincsenek kizárva. Ilyen esetben a korlátozás a licenc részét képezi, mintha ezen szöveg részeként került volna megfogalmazásra.
		<br /><br />9. A Free Software Foundation időről időre kiadja/nyilvánosságra hozza a General Public License dokumentum átdolgozott és/vagy új változatait. Ezek az újabb változatok alapvetően a korábbiak szellemében készülnek, de részletekben eltérhetnek, annak érdekében, hogy új problémákat vagy kihívásokat is kezeljenek.
		<br />Jelen licenc minden változatának egy egyedi megkülönböztető verziószáma van. Ha a Program szerzői jogi megjegyzésében egy bizonyos verziószám vagy vagy “valamennyi újabb verzió” van megjelölve, akkor Önnek lehetősége van arra, hogy akár a megjelölt, akár a Free Software Foundation által kiadott későbbi verzióban leírt feltételeket kövesse. Ha nincs ilyen megjelölt verzió, akkor Ön jogosult a Free Software Foundation által valaha kibocsátott bármelyik licencet választani.
		<br /><br />10. Amennyiben a Programot más szabad szoftverben kívánja felhasználni, amelynek a terjesztéssel kapcsolatos feltételei eltérőek, írjon a Szerzőnek, és kérje ehhez a hozzájárulását. Olyan szoftverek esetében, ahol szerzői jogi jogosultként a Free Software Foundation van megjelölve, írjon a Free Software Foundation-nek. Néha teszünk kivételt. A döntés a következő célok szem előtt tartásával hozzuk meg: maradjon meg a szabad szoftvereinken alapuló művek szabad állapota, valamint segítse elő általában véve a szoftver újrafelhasználását és megosztását.
		<br /><br />
		<h3>GARANCIAVÁLLALÁS HIÁNYA</h3>
		<br /><br />11. MIVEL JELEN PROGRAM DÍJMENTESEN KERÜL ENGEDÉLYEZÉSRE, A JOGSZABÁLYOKBAN MEGHATÁROZOTT KERETEKIG KIZÁRJUK A PROGRAMMAL KAPCSOLATOS GARANCIÁT. AMENNYIBEN ÍRÁSBAN ETTŐL ELTÉRŐEN NEM RENDELKEZNEK, A SZERZŐI JOGI JOGOSULTAK ÉS /VAGY HARMADIK SZEMÉLYEK A PROGRAMOT "JELEN ÁLLAPOTÁBAN" (AHOGY VAN) BOCSÁTJÁK RENDELKEZÉSRE, BÁRMILYEN GARANCIA NÉLKÜL, SEM KIFEJEZETTEN, SEM BELEFOGLALVA, IDE ÉRTVE - DE NEM KORLÁTOZVA – FORGALOMBAHOZATAL VAGY EGY BIZONYOS CÉLRA TÖRTÉNŐ ALKALMAZHATÓSÁGRA VONATKOZÓ GARANCIÁKAT. A PROGRAM MINŐSÉGÉBŐL ÉS MŰKÖDÉSÉBŐL/TELJESÍTMÉNYÉBŐL FAKADÓ ÖSSZES KOCKÁZATOT ÖN VISELI. AMENNYIBEN A PROGRAM HIBÁSAN MŰKÖDIK, ÖNNEK KELL VÁLLALNI A SZÜKSÉGES SZERVIZ, JAVÍTÁS VAGY KIIGAZÍTÁS KÖLTSÉGEIT.
		<br /><br />12. SEMMILYEN ESETBEN SEM – KIVÉVE, HA A HATÁLYOS JOGSZABÁLYOK MEGKÍVÁNJÁK VAGY ÍRÁSBAN RÖGZÍTÉSRE KERÜLT - KÖTELES A VAGYONI JOGI JOGOSULT VAGY VALAMELY, A PROGRAMOT A FENTEBB RÖGZÍTETT ENGEDÉLY ALAPJÁN ÁTDOLGOZÓ VAGY TERJESZTŐ HARMADIK SZEMÉLY ÖNNEL SZEMBEN KÁRÉRT, IDE ÉRTVE AZ ÁLTALÁNOS VAGY KÜLÖNÖS KÁROKÉRT KÁROK MELLÉKHATÁSAIÉRT VAGY KÖVETKEZMÉNYEIÉRT, AMELYEK A PROGRAM HASZNÁLATÁBÓL VAGY HASZNÁLHATATLANSÁGÁBÓL EREDTEK (IDE ÉRTVE, DE NEM KORLÁTOZVA AZ ADATVESZTÉSRE, AZ ADATOK HIBÁS FELDOLGOZÁSÁRA, VESZTESÉGEKRE, AMIT ÖNNEK VAGY HARMADIK FÉLNEK KELL VISELNIE VAGY OLYAN HIBÁRA AMELY KÖVETKEZTÉBEN A PROGRAM MÁS PROGRAMMAL NEM TUD EGYÜTTMŰKÖDNI) FELELNI, ABBAN AZ ESETBEN SEM, HA A VAGYONI JOGI JOGOSULT VAGY HARMADIK SZEMÉLY FIGYELMÉT EZEN KÁROK BEKÖVETKEZÉSÉNEK LEHETŐSÉGÉRE FELHÍVTÁK.
		<br /><br />
		FELTÉTELEK ÉS SZABÁLYOK VÉGE
		<br />
		Fordította: Dr. Dudás Ágnes
		<br />
	</div>
	<?php
}

function visszaeles() {
	?>
	<div id="visszaeles">
		<p style="text-align:right; font-size:64px; margin:10px">§</p>
		<p>Ha azt észleli, hogy valamely felhasználó jogsértő tartalmat töltött fel
		valamelyik recept hozzávalói, készítési leírása vagy képei közé;
		jelezze ezt a 
		<br><br /><a href="mailto:tibor.fogler@gmail.com">tibor.fogler@gmail.com</a> 
		<br /><br />címre küldött levélben!		
		</p>
	</div>
	<?php
}

?>
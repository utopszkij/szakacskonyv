function initArray() {
  this.length = initArray.arguments.length
  for (var i = 0; i < this.length; i++)
    this[i+1] = initArray.arguments[i];
}

function havinev(ev, ho, nap) {
    if (ho==1)
      { var napok = new initArray("Újév, Fruzsina", "Ábel", "Genovéva, Benjámin", "Titusz, Leona",
            "Simon", "Boldizsár", "Attila, Ramóna", "Gyöngyvér", "Marcell",
            "Melánia", "Ágota", "Ern&#337;", "Veronika", "Bódog", "Lóránt, Loránd",
            "Gusztáv", "Antal, Antónia", "Piroska", "Sára, Márió", "Fábián, Sebestyén",
            "Ágnes", "Vince, Artúr", "Zelma, Rajmund", "Timót", "Pál", "Vanda, Paula",
            "Angelika", "Károly, Karola", "Adél", "Martina, Gerda", "Marcella", "Ignác") }
    if (ho==2)
      if (ev % 4 != 0)
        { var napok=new initArray("Ignác", "Karolina, Aida", "Balázs", "Ráhel, Csenge", "Ágota, Ingrid",
            "Dorottya, Dóra", "Tódor, Rómeó", "Aranka", "Abigél, Alex", "Elvira",
            "Bertold, Marietta", "Lívia, Lídia", "Ella, Linda", "Bálint, Valentin",
            "Kolos, Georgina", "Julianna, Lilla", "Donát", "Bernadett", "Zsuzsanna",
            "Aladár, Álmos", "Eleonóra", "Gerzson", "Alfréd",
            "Mátyás", "Géza", "Edina", "Ákos, Bátor", "Elemér", "Albin")  }
         else
        {
            var napok=new initArray("Ignác", "Karolina, Aida", "Balázs", "Ráhel, Csenge", "Ágota, Ingrid",
            "Dorottya, Dóra", "Tódor, Rómeó", "Aranka", "Abigél, Alex", "Elvira",
            "Bertold, Marietta", "Lívia, Lídia", "Ella, Linda", "Bálint, Valentin",
            "Kolos, Georgina", "Julianna, Lilla", "Donát", "Bernadett", "Zsuzsanna",
            "Aladár, Álmos", "Eleonóra", "Gerzson", "Alfréd",
            "Szokonap", "Mátyás", "Géza", "Edina", "Ákos, Bátor", "Elemér", "Albin")
        }
    if (ho==3)
      { var napok=new initArray("Albin", "Lujza", "Kornélia", "Kázmér", "Adorján, Adrián", "Leonóra, Inez",
            "Tamás", "Nemz.N&#337;nap, Zoltán", "Franciska, Fanni", "Ildikó", "Szilárd",
            "Gergely", "Krisztián, Ajtony", "Matild", "Nemzeti Ünnep, Kristóf",
            "Henrietta", "Gertrúd, Patrik", "Sándor, Ede", "József, Bánk", "Klaudia",
            "Benedek", "Beáta, Izolda", "Em&#337;ke", "Gábor, Karina", "Irén, Irisz",
            "Emánuel", "Hajnalka", "Gedeon, Johanna", "Auguszta", "Zalán", "Árpád", "Hugó") }
    if (ho==4)
      { var napok=new initArray("Hugó", "Áron", "Buda, Richárd", "Izidor", "Vince", "Vilmos, Bíborka",
            "Herman", "Dénes", "Erhard", "Zsolt", "Leó, Szaniszló", "Gyula", "Ida",
            "Tibor", "Anasztázia, Tas", "Csongor", "Rudolf", "Andrea, Ilma", "Emma",
            "Tivadar", "Konrád", "Csilla, Noémi", "Béla", "György", "Márk", "Ervin",
            "Zita", "Valéria", "Péter", "Katalin, Kitti", "Munka Ünnepe, Fülöp, Jakab")}
    if (ho==5)
      { var napok=new initArray("Munka Ünnepe , Fülöp, Jakab", "Zsigmond", "Tímea, Irma", "Mónika, Flórián",
            "Györgyi", "Ivett, Frida", "Gizella", "Mihály", "Gergely", "Ármin, Pálma",
            "Ferenc", "Pongrác", "Szervác, Imola", "Bonifác", "Zsófia, Szonja",
            "Mózes, Botond", "Paszkál", "Erik, Alexandra", "Ivó, Milán",
            "Bernát, Felícia", "Konstantin", "Júlia, Rita", "Dezs&#337;", "Eszter, Eliza",
            "Orbán", "Fülöp, Evelin", "Hella", "Emil, Csanád", "Magdolna",
            "Janka, Zsanett", "Angéla, Petronella", "Tünde")}
    if (ho==6)
      { var napok=new initArray("Tünde", "Kármen, Anita", "Klotild", "Bulcsú", "Fatime", "Norbert, Cintia",
            "Róbert", "Medárd", "Félix", "Margit, Gréta", "Barnabás", "Vill&#337;",
            "Antal, Anett", "Vazul", "Jolán, Vid", "Jusztin", "Laura, Alida",
            "Arnold, Levente", "Gyárfás", "Rafael", "Alajos, Leila", "Paulina",
            "Zoltán", "Iván", "Vilmos", "János, Pál", "László", "Levente, Irén",
            "Péter, Pál", "Pál", "Tihamér") }
    if (ho==7)
      { var napok=new initArray("Tihamér, Annamária", "Ottó", "Kornél, Soma", "Ulrik", "Emese, Sarolta",
            "Csaba", "Appolónia", "Ellák", "Lukrécia", "Amália", "Nóra, Lili",
            "Izabella, Dalma", "Jenő", "&#336;rs, Stella", "Henrik, Roland", "Valter",
            "Endre, Elek", "Frigyes", "Emília", "Illés", "Dániel, Daniella",
            "Magdolna", "Lenke", "Kinga, Kincs&#337;", "Kristóf, Jakab", "Anna, Anikó",
            "Olga, Liliána", "Szabolcs", "Márta, Flóra", "Judit, Xénia", "Oszkár", "Boglárka")}
    if (ho==8)
      { var napok=new initArray("Boglárka", "Lehel", "Hermina", "Domonkos, Dominika", "Krisztina",
            "Berta, Bettina", "Ibolya", "László", "Em&#337;d", "Lörinc",
            "Zsuzsanna, Tiborc", "Klára", "Ipoly", "Marcell", "Mária", "Ábrahám",
            "Jácint", "Ilona", "Huba", "Alkotmány Ünnepe, István", "Sámuel, Hajna",
            "Menyhért, Mirjam", "Bence", "Bertalan", "Lajos, Patrícia", "Izsó",
            "Gáspár", "Ágoston", "Beatrix, Erna", "Rózsa", "Erika, Bella", "Egyed, Egon")}
    if (ho==9)
      { var napok= new initArray("Egyed, Egon", "Rebeka, Dorina", "Hilda", "Rozália", "Viktor, L&#337;rinc",
            "Zakariás", "Regina", "Mária, Adrienn", "Ádám", "Nikolett, Hunor",
            "Teodóra", "Mária", "Kornél", "Szeréna, Roxána", "Enik&#337;, Melitta", "Edit",
            "Zsófia", "Diána", "Vilhelmina", "Friderika", "Máté, Mirella", "Móric",
            "Tekla", "Gellért, Mercédesz", "Eufrozina, Kende", "Jusztina", "Adalbert",
            "Vencel", "Mihály", "Jeromos", "Malvin")}
    if (ho==10)
      { var napok= new initArray("Malvin", "Petra", "Helga", "Ferenc", "Aurél", "Brúnó, Renáta", "Amália",
            "Koppány", "Dénes", "Gedeon", "Brigitta", "Miksa", "Kálmán, Ede", "Helén",
            "Teréz", "Gál", "Hedvig", "Lukács", "Nándor", "Vendel", "Orsolya", "El&#337;d",
            "Köztársaság kikiált., Gyöngyi", "Salamon", "Blanka, Bianka", "Dömötör",
            "Szabina", "Simon, Szimonetta", "Nárcisz", "Alfonz", "Farkas", "Marianna")}
    if (ho==11)
      { var napok=new initArray("Marianna", "Achilles", "Gy&#337;z&#337;", "Károly", "Imre", "Lénárd", "Rezs&#337;",
            "Zsombor", "Tivadar", "Réka", "Márton", "Jónás, Renátó", "Szilvia",
            "Aliz", "Albert, Lipót", "Ödön", "Hortenzia, Gerg&#337;", "Jen&#337;", "Erzsébet",
            "Jolán", "Olivér", "Cecília", "Kelemen, Klementina", "Emma", "Katalin",
            "Virág", "Virgil", "Stefánia", "Taksony", "András, Andor", "Elza")}
    if (ho==12)
      { var napok=new initArray("Elza", "Melinda, Vivien", "Ferenc, Olívia", "Borbála, Barbara", "Vilma",
            "Miklós", "Ambrus", "Mária", "Natália", "Judit", "Árpád", "Gabriella",
            "Luca, Otília", "Szilárda", "Valér", "Etelka, Aletta", "Lázár, Olimpia",
            "Auguszta", "Viola", "Teofil", "Tamás", "Zéno", "Viktória", "Ádám, Éva",
            "Karácsony, Eugénia", "Karácsony, István", "János", "Kamilla",
            "Tamás, Tamara", "Dávid", "Szilveszter", "Újév, Fruzsina")}
    return napok[nap];
}

function honev(ho) {
    var month = new initArray("január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december");
    return month[ho];
}

function napnev(szam) {
    var napok = new initArray("vasárnap", "hétf&#337;", "kedd", "szerda", "csütörtök", "péntek", "szombat", "vasárnap")
    return napok[szam];
}

var ido = new Date()
  var ev = ido.getFullYear();
  var ho = ido.getMonth()+1;
  var nap = ido.getDate();

document.writeln('<font color="#004e79" face="ms sans serif, arial, verdana" size="2"><b>' + ev + '. ' + honev(ho) + ' ' + nap + '. ' + napnev(ido.getDay()+1) + ', Ma <font color="#f3152f" size="2"> ' + havinev(ev, ho, nap) + '</font>, holnap<font color="darkgreen" size="2"> ' + havinev(ev,ho, nap+1) + '</font> ünnepli a névnapját.<br /><br /></b> Sok Boldogságot kíván a receptoldal vezetősége!</font>')
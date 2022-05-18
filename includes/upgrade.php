<?php

/*
a githubról file elérési példa includes/napimenu.php:

https://raw.githubusercontent.com/utopszkij/szakacskonyv/main/includes/napimenu.php

koncepció:
1. olvassa a saját readme.md file -t ebből kiemeli a currentVersion -t
   1.1 az első ## Verzió sor 
2. olvassa a githubról a readme.md file-t, ebből liemeli a lastVersions -t
   2.1 az első ## Verzió sor 
3. ha lastVersion > currentVersion
	3.1 a githubon lévő reame.md -ből kiemeli a változott fájlokat
		3.11 keresi az első  ### Vátozott fájlok szöveget
		3.12 ciklus üres sorig
			3.121 "-" -el kezdödő fájl név kiolvasása
	3.2 ciklusban letölti a githubról a változott fájlokat
		3.21 ha már megvan a file akkor modositja az attributumát.
		3.22 letölti a fájlt memoriába
		3.23 ha megvolt akkor a meglévőt átnevezi .old -ra
		3.24 a letöltöttet lemezre irja
		

*/
?>
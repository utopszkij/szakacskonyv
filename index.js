	    const { createApp } = Vue; 
		/**
		 * csoki beállítás
		 */
		function setCookie(name,value,days) {
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days*24*60*60*1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "")  + expires + "; path=/";
		}
		
		function getCookie(cname) {
		  let name = cname + "=";
		  let decodedCookie = decodeURIComponent(document.cookie);
		  let ca = decodedCookie.split(';');
		  for(let i = 0; i <ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
			  c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
			  return c.substring(name.length, c.length);
			}
		  }
		  return "";
		}

		/**
		 * user jováhagyás kérés popup ablakban
		 */
		function popupConfirm(txt, yesfun) {
			document.getElementById('popupOkBtn').style.display="inline-block";
			document.getElementById('popupNoBtn').style.display='inline-block';
			document.getElementById('popup').className='popupSimple';
			document.getElementById('popupTxt').innerHTML = txt;
			document.getElementById('popupOkBtn').onclick=yesfun;
			document.getElementById('popup').style.display='block';
		}
		/**
		 * poup ablak bezárása
		 */
		function popupClose() {
			document.getElementById('popup').style.display='none';
		}
		/**
		 * popup üzenet
		 */
		function popupMsg(txt,className) {
			if (className == undefined) {
				className = 'popupSimple';
			}
			document.getElementById('popupOkBtn').style.display="none";
			document.getElementById('popupNoBtn').style.display='none';
			document.getElementById('popup').className=className;
			document.getElementById('popupTxt').innerHTML = txt;
			document.getElementById('popup').style.display='block';
		}
		/**
		 * nyelvi fordítás
		 */
		function lng(token) {
			var result = token;
			var w = token.split('<br>');
			for (var i = 0; i < w.length; i++) {
				if (tokens[w[i]] != undefined) {
					w[i] = tokens[w[i]];
			    }
			}
			result = w.join('<br>');	
			return result;
		}
		/**
		 * felső menüben almenü megjelenés/elrejtés
		 */
		function submenuToggle() {
			var submenu = document.getElementById('submenu');
			if (submenu.style.display == 'block') {
				submenu.style.display = 'none';
			} else {
				submenu.style.display = 'block';
			}
		}

		/**
		 * seo barát url képzéshez segéd rutin
		 * @param string task
		 * @param object params {name:value,...}
		 */
		function HREF(task, params) {
			var result = siteurl;
			if (rewrite) {
				result += '/task/'+task;
				for (var fn in params) {
					result += '/'+fn+'/'+params[fn];
				}
			} else {
				result += '?task='+task;
				for (var fn in params) {
					result += '&'+fn+'='+params[fn];
				}
			}
			return result;
		}

		// Scroll to top(fel a tetejere funkcio)
		window.onscroll = function() {scrollFunction()};
		function scrollFunction() {
			let mybutton = document.getElementById("scrolltotop");
			if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
				mybutton.style.display = "block";
			} else {
				mybutton.style.display = "none";
			}
		}
		function topFunction() {
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		}


		// képernyő méretek tárolása csokiba
		setCookie('screen_width',screen.width,100); 
		setCookie('screen_height',screen.height,100); 

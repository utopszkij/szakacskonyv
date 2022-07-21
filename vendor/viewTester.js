exports.mock = function() {
/*
const parser = new XMLParser();
const { XMLParser, XMLBuilder, XMLValidator} = require("fxp");
let jObj = parser.parse(XMLdata);
const builder = new XMLBuilder();
const xmlContent = builder.build(jObj);
*/

window = {
	setTimeout: function(fun, time) {
		if ((typeof fun) == 'function') {
         fun();
      }   
	},
   lng: function(s) {
      return s;
   },
   HREF: function(task, params) {
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
};
comp = {};
vueTest = function() {};
describeError = 0;
itCount = 0;
expectCount = 0;

msg = [];
axiosResults = [];

red = '\x1b[31m%s\x1b[0m';
green = '\x1b[32m%s\x1b[0m';
siteURL = 'http://localhost:8000';

class FormData {
   append(name,value) {

   }
}

axiosCaller = function(url,params, successFun, headers) {
   if (axiosResults.length > 0) {
	  let r =  { ...axiosResults[0] };
      axiosResults.splice(0,1);
      successFun(r);
   }
};

$ = function(name) {
  return {
    show: function() {},
    hide: function() {},
    toggle: function() {},
	 animate: function(par, speed, fun) {},
    removeClass(s) {},
    addClass(s) {},
    focus() {},
    val: function(value) { return ''},  
    attr(s,v) {},
    css(s) {},
	0: {
		vue: comp
	}  
  };
};

alert = function(txt) {
  msg.push(txt);
};

popupTxt = function(txt) {
  msg.push(txt);
};

popupConfirm = function(txt, yesFun) {
  msg.push(txt);
};

tokens = {};	
	
describe = function(txt, describeFun) {
    describeError = 0;
    console.log(txt);
    describeFun();
};

it = function(txt, itFun) {
    itCount++;
    console.log('  '+txt);
    itFun();
};

class Expect {
   constructor(value) {
      expectCount++;
      this.value = value;
   }
   toEqual(expected) {
      if (this.value == expected) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    Equal");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   toNotEqual(expected) {
      if (this.value != expected) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    notEqual");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   toBeLess(expected) {
      if (this.value < expected) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    Less");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   toBeLessOrEqual(expected) {
      if (this.value <= expected) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    LessOrEqual");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   toBeGreater(expected) {
      if (this.value > expected) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    Greater");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   toBeGreaterOrEqual(expected) {
      if (this.value >= expected) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    GreaterOrEqual");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   toBeDefined() {
      if (this.value != undefined) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    Defined");
         console.log("    --actual:"+this.value);
      }
   }
   toBeNotDefined() {
      if (this.value == undefined) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    notDefined");
         console.log("    --actual:"+this.value);
      }
   }
   toBeTruthy() {
      if (this.value) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    Truthy");
         console.log("    --actual:"+this.value);
      }
   }
   toBeFalsy() {
      if (this.value == false) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    False");
         console.log("    --actual:"+this.value);
      }
   }
   arrayContaining(expected) {
      if (this.value.indexOf(expected) >= 0) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    arrayContaining");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   arrayNotContaining(expected) {
      if (this.value.indexOf(expected) < 0) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    arrayNotContaining");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   objectContaining(expected) {
      if (this.value[expected] != undefined) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    arrayContaining");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
   objectNotContaining(expected) {
      if (this.value[expected] == undefined) {
         console.log(green,'    ok');
      } else {
         describeError++;
         console.log(red,"    arrayNotContaining");
         console.log("    --actual:"+this.value);
         console.log("    --expected:"+expected);
      }
   }
} // Expect

expect = function(value) {
   return new Expect(value);
};


	function getVueElement(data) {
	  	var result = {};
		var code = '';
   
		var matches = data.match(/v-for="[^"]*"/);
		if (matches) {
			result.matched = matches[0];
			matches = matches[0].match(/"[^"]*"/);
			var str = matches[0].substr(1, (matches[0].length - 2)).trim();
			var w = str.split('in');
			var item = w[0].trim();
			result.array = w[1].trim();
			w = item.split(',');
			if (w.length > 1) {
				item = trim(w[0]).substr(1,100);
			}
			result.item = item;
			return result;
		}	
		
		var matches = data.match(/v-if="[^"]*"/);
		if (matches) {
			result.matched = matches[0];
			matches = matches[0].match(/"[^"]*"/);
			result.code = matches[0].substr(1, (matches[0].length - 2));
			return result;
		}	

		var matches = data.match(/v-bind:[^=]*="[^"]*"/);
		if (matches) {
			result.matched = matches[0];
			matches = matches[0].match(/"[^"]*"/);
			result.code = matches[0].substr(1, (matches[0].length - 2));
			return result;
		}	

		var matches = data.match(/v-on:[^=]*="[^"]*"/);
		if (matches) {
			result.matched = matches[0];
			matches = matches[0].match(/"[^"]*"/);
			result.code = matches[0].substr(1, (matches[0].length - 2));
			return result;
		}	

		var matches = data.match(/v-model="[^"]*"/);
		if (matches) {
			result.matched = matches[0];
			matches = matches[0].match(/"[^"]*"/);
			result.code = matches[0].substr(1, (matches[0].length - 2));
			return result;
		}	
		
		var matches = data.match(/:[^=]="[^"]*"/);
		if (matches) {
			result.matched = matches[0];
			matches = matches[0].match(/"[^"]*"/);
			result.code = matches[0].substr(1, (matches[0].length - 2));
			return result;
		}	
		
		var matches = data.match(/{{[^}]*}}/);
		if (matches) {
			result.matched = matches[0];
			result.code = matches[0].substr(2, (matches[0].length - 4));
			return result;
		}	
		return false;
}

vueTest = function(v)  {	
	var result = true;
	var i = 0;
    // "v" propertyk és funkciók kitétele global -ba
	Object.keys(v).forEach(key => {
  		global[key] = v[key];
	});
	// v-for itemek kitétele globálba
	for (i=0; i < v.vueElements.length; i++) {
	   vueElement = v.vueElements[i];
	   if (vueElement.item != undefined) {
		   if (global[vueElement.array] != undefined) {
	   	  		global[vueElement.item] = global[vueElement.array][0];
		   }	   
	   }
	}

	// vue elemek tesztelése
	for (i=0; i < v.vueElements.length; i++) {
	   vueElement = v.vueElements[i];
	   if (vueElement.code != undefined) {
		   try {
		     eval(vueElement.code); 
		   } catch (e) {
            console.log('     ' + vueElement.matched + ' ' + e.message);
			   result = false; 
	       }
	   }   
	}
	return result;
}	

	
loadView = function(viewFileName, fun) {
  fs = require('fs');
  fs.readFile(viewFileName, 'utf8', function (err,data) {
    if (err) {
      return console.log(err);
    }
   
	// kiemeli a vue elemeket  
	var vueElements = [];
	var vueElement = getVueElement(data);  
	// kiemel egy vue elemet és kitörli a data -ból 
	// return: {type, item, array, code } vagy ha nincs akkor false
	while (vueElement) {
	    vueElements.push(vueElement);
		data = data.replace(vueElement.matched,'');  
	  	vueElement = getVueElement(data);
	}
	// console.log(vueElements); 
	  
    var i = data.indexOf('<script>');
    data = data.substr(i+8,800000).replace('</script>','');
    var methods = {};
    eval(data);

    // prepare v
    var v = { "vueElement" : vueElement };
    if (methods != undefined) {
      v = methods;
	   v.vueElements = vueElements;	
    } 
    v.HREF = function(task, params) {
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
    };
    v.lng = function(s) {
      return s;
    }
	  
	// call tester function  
    fun(v);
	  
	// echo test result  
    console.log(' ');
    console.log('  it count:'+itCount);
    console.log('  expect count:'+expectCount);
    console.log(' ');
    if (describeError == 0) {
        console.log(green,'  describe error:'+describeError);
    } else {
        console.log(red,'  describe error:'+describeError);
    };
    console.log(' ');
    process.exitCode = describeError;
  });
}; // loadView
}; // mock






<?php
/**
 * vue alapú viewer
 * 
 * használata:
 * 
 * view('viewName',[ "p1" => $value, ....], appName);
 * vagy
 * view('viewName',[ "p1" => $value, ....]);
 * 
 * /includes/views/viewname.html:
 *  html kód vue attributumokkal pl:
 *  <li v-for="(item, index) in items">{{ index }} {{ item}}</li>
 *  <input type="text" name="..." v-if="p2 == 1" v-model="adat" :disabled="disabled" />
 *  <var v-html="htmlstr" v-on:click="method1()" v-bind:class="classname"></var>
 *  ....
 *  include htmlname
 *  ....
 *  <script>  
 *    const methods = {
 *       method1(param) {
 *           ...  this.  használható
 *       },
 *       ....
 *       afterMount() {
 *           ...  this.  használható
 *       }
 *    };
 *  </script>
 * 
 * @param string $name
 * @param array $params
 * @return void
 */

function view(string $name,array $params, string $appName = 'app') {
    echo '
    <div id="'.$appName.'" style="display:none">'."\n";  
    $scriptExist = false;
    $lines = file(__DIR__.'/../includes/views/'.$name.'.html');
    foreach ($lines as $line) {
        if (trim($line) == '<script>') {
            $scriptExist = true;
            echoScript($appName);
        } else if (trim($line) == '</script>') {
            echoEndScript($params,$appName);
        } else if (substr(trim($line),0,7) == 'include') {
            $lines2 = file(__DIR__.'/../includes/views/'.trim(substr(trim($line),7,100)).'.html');
            echo implode("\n",$lines2);
        } else {
            echo $line."\n";
        } // if
    } // foreach
    if (!$scriptExist) {
        echoScript($appName);
        echoEndScript($params,$appName);
    }
} // function

function echoScript(string $appName) {
    echo '
    </div><!-- '.$appName.' -->		
    <script>'."\n";
}

function echoEndScript(array $params, string $appName) {
    echo '
    if (methods == undefined) { var methods = {}; }
    const '.$appName.' = createApp({
            data() {
            return {'."\n";
            foreach ($params as $fn => $param) {
                echo $fn.': '.JSON_encode($param).",\n";
            }			
            echo '				
            innerWidth : window.innerWidth
            };
        },
        mounted() {
            if (this.afterMount != undefined) {
                this.afterMount();
            }    
            document.getElementById("'.$appName.'").style.display="block";
        },
        methods: methods
    }).mount("#'.$appName.'");
    </script>'."\n";
}

?>
<?php
/**
 * vue alapú viewer
 * 
 * használata:
 * 
 * view('viewName',[ "p1" => $value, ....]);
 * 
 * /includes/views/viewname.html:
 *  html kód vue attributumokkal pl:
 *  <li v-for="(item, index) in items">{{ index }} {{ item}}</li>
 *  <input type="text" name="..." v-if="p2 == 1" v-model="adat" :disabled="disabled" />
 *  <var v-html="htmlstr" v-on:click="method1()" v-bind:class="classname"></var>
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

include_once ('vendor/url.php');

function view(string $name,array $params) {
    echo '
    <!-- script src="https://unpkg.com/vue@3"></script -->
    <script src="vendor/vue.global.js"></script>
    <div id="app" style="display:none">'."\n";  
    $scriptExist = false;
    $lines = file(__DIR__.'/'.$name.'.html');
    foreach ($lines as $line) {
        if (trim($line) == '<script>') {
            $scriptExist = true;
            echoScript();
        } else if (trim($line) == '</script>') {
            echoEndScript($params);
        } else {
            echo $line."\n";
        } // if
    } // foreach
    if (!$scriptExist) {
        echoScript();
        echoEndScript($params);
    }
} // function

function echoScript() {
    echo '
    </div><!-- app -->		
    <script>'."\n";
}

function echoEndScript($params) {
    URL::save();
    echo '
    if (methods == undefined) { var methods = {}; }
    const { createApp } = Vue;
    const app = createApp({
        data() {
            return {'."\n";
            foreach ($params as $fn => $param) {
                echo $fn.': '.JSON_encode($param).",\n";
            }			
            echo '				
            previousUrl : "'.URL::previous().'",
            currentUrl : "'.URL::current().'"
            };
        },
        mounted() {
            if (this.afterMount != undefined) {
                this.afterMount();
            }    
            document.getElementById("app").style.display="block";
        },
        methods: methods
    }).mount("#app");
    </script>'."\n";
}
  

?>
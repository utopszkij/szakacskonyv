<?php
/*
* file uploader 
* Licence GNU GPL v3
* Author: Hosszú Gábor, Fogler Tibor
* Author email: tibor.fogler@gmail.com
*/
class Uploader {

    public static function remove_accent($str) {
        $a=array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
        $b=array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
        return str_replace($a,$b,$str);
    }

    public static function clearFileName($s) {
        return preg_replace("/[^a-z0-9._-]/", '', strtolower(Uploader::remove_accent($s)));
    }

    /**
     * File upload. If target file exists overwrite it.
     * @param string input DOM component name
     * @param string target directory (not include last /)
     * @param string target Name+ext  optional, enable ''
     *   enable '.*' extension (use uploaded file extension)
     * @param array  enabled extensions
     * @return {"error":" ''|errorMsg ", "name":"uploaded filename+ext"}
     */
    public static function doUpload(string $cname, string $targetDir, 
        string $targetName = '', array $filter = []) {
        $result = JSON_decode('{"error":"", "name":""}');
        if (isset($_FILES[$cname])) {
            if (file_exists($_FILES[$cname]['tmp_name'])) { 
                if (!is_dir($targetDir.'/')) {
                    mkdir($targetDir,0755);
                }
                $targetDir .= '/';
                if ($targetName == '') {
                    $targetName = $_FILES[$cname]["name"];
                } else if (strpos($targetName,'.*') > 0) {
                    $uploadFileExt = pathinfo($_FILES[$cname]["name"],PATHINFO_EXTENSION);
                    $targetName = str_replace('*',$uploadFileExt,$targetName); 
                }
                $targetName = Uploader::clearFileName($targetName);
                $target_file = $targetDir.$targetName;
                $uploadFileExt = pathinfo($target_file,PATHINFO_EXTENSION);
                if (!in_array($uploadFileExt, $filter)) {
                    $result->error = 'upload not enabled';
                }

                $check = getimagesize($_FILES[$cname]["tmp_name"]);
                if($check == false) {
                    $result->error = 'nem kép fájl';
                }
                if ($_FILES[$cname]['size'] > (UPLOADLIMIT * 1024 * 1024)) {
                    $result->error = 'túl nagy kép fájl';
                }
                if (file_exists($target_file) & ($result->error == '')) {
                    unlink($target_file);
                }
                if ($result->error == '') {
                    if (!move_uploaded_file($_FILES[$cname]["tmp_name"], $target_file)) {
                        $result->error = "Hiba a kép fájl feltöltés közben "; 
                    }
                    $result->name = $targetName;
                    $result->error = '';
                } else {
                    $result->name = '';
                }
            } else {
                $result->error = 'not upoladed file (1)';
            }
        } else {
            $result->error = 'not upoladed file (2)';
        }
        return $result;
    }

    /**
     * Image File upload. If target file exists overwrite it.
     * @param string input DOM component name
     * @param string target directory (not include last /)
     * @param string target Name+ext  optional, enable ''
     *   enable '.*' extension (use uploaded file extension)
     * @return {"error":" ''|errorMsg ", "name":"uploaded filename+ext"}
     */
    public static function doImgUpload(string $cname, string $targetDir, string $targetName = '') {
        return Uploader::doUpload($cname, $targetDir, $targetName, Array('jpg','jpeg','png','gif'));
    }
}
?>

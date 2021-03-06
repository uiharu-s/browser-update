<?php

define('BU_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('BU_LIB_PATH', BU_PATH . 'lib' . DIRECTORY_SEPARATOR);
$default_lang = 'en';


function cache_output($function,$hours=0.1) {
	$cachefile = "cache/" . md5($function) . '.cache.html';

	if (!file_exists($cachefile) || filemtime($cachefile) < (time() - 3600*$hours)) {
                ob_start();
		$dataf = call_user_func($function);
                $data=ob_get_contents().$dataf;
                ob_end_clean();
                file_put_contents($cachefile, $data);
                chmod($cachefile, 0777);
	}
	else {
		$data = file_get_contents($cachefile);
	}
	 return $data;
}

$__uastr=str_replace(array("/","+","_","\n","\t")," ", strtolower($_SERVER['HTTP_USER_AGENT']));
function det($str, $version) {
    global $__uastr;
    if(!preg_match("#".$str."#", $__uastr, $regs))
        return false;
    return $regs[1]<$version;
}
$currentbrowsers=False;

function is_outdated() {
    global $currentbrowsers;
    if (!$currentbrowsers) {        
        $browsers_file = file_get_contents("browsers.json");
        $currentbrowsers = json_decode($browsers_file, true);
    }

    $vs="?(\d+[.]\d+)";
    if(det("opera.*version $vs",16)||
            det("trident.$vs",7)||
            det("trident.*rv:$vs",10)||
            det("msie $vs",10)||
            det("firefox $vs",23)||
            det("version $vs.*safari",6))
        return true;
}

function currentv($browser,$set="desktop"){
    global $currentbrowsers;
    if (!$currentbrowsers) {        
        $browsers_file = file_get_contents("browsers.json");
        $currentbrowsers = json_decode($browsers_file, true);
    }
    return $currentbrowsers["current"][$set][$browser];
}

#"it,sl,jp,nb,ch"
function countSites() {
    require_once("config.php");
    $r = mysql_query("SELECT COUNT(DISTINCT referer) FROM updates") or die(mysql_error(). $q);
    list($num) = mysql_fetch_row($r);
    return $num;
}
function countUpdates() {
    require_once("config.php");
    $r = mysql_query("SELECT COUNT(*) FROM updates") or die(mysql_error(). $q);
    list($num) = mysql_fetch_row($r);
    return $num;
}
?>

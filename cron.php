<?php
ini_set('max_execution_time', 600);

$url    =  'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
$cm     = @$_GET['cm'];

if(empty($cm)){
    echo '??';

} else {
    header('Content-Type: application/json');
    echo file_get_contents($url.$cm);

}

//print_r($_SERVER);

//echo file_get_contents('http://taxicenter.focusit.pe/cron-bot-inc.php');
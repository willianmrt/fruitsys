<?php
$call = isset($_REQUEST['call']) ?  $_REQUEST['call'] : '';
 
$array_call = explode('.', $call);
$className = $array_call[0];
$methodName = $array_call[1];   

if(file_exists('class/'.$className.'.php')){
    require 'class/'.$className.'.php';
}
//sanitização do request
unset($_REQUEST['call']);
unset($_REQUEST['PHPSESSID']);
unset($_REQUEST['sidebar-minified']);

//print_r($_REQUEST);
$Obj = new $className();
$Obj->$methodName($_REQUEST);

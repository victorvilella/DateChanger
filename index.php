<?php

require_once "DataChange.php";

if(count($argv) !== 4){
    die("São necessários 3 argumentos (Data, operador, valor)");
}
$date = $argv[1];
$operator = $argv[2];
$value = $argv[3];
try{
    $x = new DataChange($date, $operator, $value);
    echo $x->process();
} catch (Exception $e){
    echo $e->getMessage();
}

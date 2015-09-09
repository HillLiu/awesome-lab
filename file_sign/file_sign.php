<?php
/**
 * usage
 * #for test
 * ./convmv.php --path=/yourpath
 * #for run
 * ./convmv.php --path=/yourpath --notest
 */
include_once('vendor/autoload.php');
PMVC\Load::plug();
$params = PMVC\plug('cmd')->commands($argv);

$mypath = $params['path'];

if(is_dir($mypath)){
    $path = $mypath;
}else{
    $mypath = pathinfo($mypath);
    $path = $mypath['dirname'];
    $pattern = $mypath['basename'];
}

$test = !$params['notest'];
if($test){
    echo "Run in Test Mode\n";
}
if( empty($path) || !realpath($path) )
{
    exit();
}
if(empty($pattern)){
    $pattern='*';
}
echo "Run in ".$path."\n";
echo "File pattern: ".$pattern."\n";
$from = 'big-5';
$to = 'utf-8';
$files = PMVC\plug('file_list',array('hash'=>true))->ls($path,$pattern);

foreach ($files as $f) {
    var_dump($f);
}


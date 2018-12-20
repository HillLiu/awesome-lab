<?php
/**
 * usage
 * #for test
 * ./convmv.php --path=/yourpath
 * #for run
 * ./convmv.php --path=/yourpath --notest
 */
include_once('vendor/autoload.php');
\PMVC\Load::plug(['debug'=>['output'=>'debug_cli']]);

$params = PMVC\plug('cli')->getopt();

$mypath = \PMVC\get($params, 'path');
if(is_dir($mypath)){
    $path = $mypath;
}else{
    $mypath = pathinfo($mypath);
    $path = \PMVC\get($mypath, 'dirname');
    $pattern = \PMVC\get($mypath, 'basename');
}

$test = !\PMVC\get($params, 'notest');
if($test){
    echo "Run in Test Mode\n";
}
if( empty($path) || !realpath($path) )
{
    trigger_error("not defined path", E_USER_ERROR);
    exit();
}
if(empty($pattern)){
    $pattern='*';
}
echo "Run in ".$path."\n";
echo "File pattern: ".$pattern."\n";
$from = 'big-5';
$to = 'utf-8';
$files = PMVC\plug('file_list')->ls($path,$pattern);
$detect_encodes = array( 
    'ASCII',
    'big-5',
    'utf-8',
    'GB2312'
);

$changes = [];
foreach($files as $i){
    $i['encode']=mb_detect_encoding($i['name'],$detect_encodes);
    \PMVC\d([$i['encode'], $i['name']]);
    if('BIG-5'!=$i['encode']){
        continue;
    }
    $i['newname']=mb_convert_encoding($i['name'], $to, $from);
    $changes[]=$i;
    if(!$test){
        $dir=dirname($i['wholePath']);
        $new_path = \PMVC\lastSlash($dir).$i['newname'];
        if(realpath($i['wholePath'])){
            rename($i['wholePath'],$new_path);
        }
    }
}
\PMVC\d(['Changes'=>$changes]);
?>

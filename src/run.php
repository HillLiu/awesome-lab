<?php
/**
 * usage
 * #for test
 * ./convmv.php --path=/yourpath
 * #for run
 * ./convmv.php --path=/yourpath --notest
 */
include_once('/home/sys/web/lib/pmvc/include_plug.php');
include("class.cmd.php");

PMVC\setPlugInFolder('vendor/pmvc-plugin/');

$cmd = new cmd();
$params = $cmd->arguments($argv);

$mypath = $params['commands']['path'];
if(is_dir($mypath)){
    $path = $mypath;
}else{
    $mypath = pathinfo($mypath);
    $path = $mypath['dirname'];
    $pattern = $mypath['basename'];
}

$test = !$params['commands']['notest'];
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
$files = PMVC\plug('file-list')->ls($path,$pattern);
$detect_encodes = array( 
    'ASCII',
    'big-5',
    'utf-8',
    'GB2312'
);

function EndWithSlash($str)
{
    $str1 = str_replace('\\','/',$str);
    if (substr($str1,strlen($str1)-1,1) != "/")
        $str = $str.'/';
    return $str;
}

$changes = array();
foreach($files as &$i){
    $i['encode']=mb_detect_encoding($i['name'],$detect_encodes);
    if('BIG-5'!=$i['encode']){
        continue;
    }
    $changes[]=&$i;
    $i['newname']=mb_convert_encoding($i['name'], $to, $from);
    if(!$test){
        $dir=dirname($i['wholePath']);
        $new_path = EndWithSlash($dir).$i['newname'];
        if(realpath($i['wholePath'])){
            rename($i['wholePath'],$new_path);
        }
    }
}
print_r($changes);
?>

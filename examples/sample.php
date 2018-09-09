<?php

include_once __DIR__ . '../CSRFProtector.php';

$error = function (){
    die('Nice try dude!');
};

$token = function (){
    return '_' . mt_rand(1, 200) . md5(mt_rand(2, 100));
};

$time = 120; //in seconds
$min = 1; //in seconds

$jsPath = 'CSRFProtector';

$csrf = new CSRFProtector($jsPath, $error, $token, $time, $min);
$csrf->run();
echo "<html><body><a href=\"Sample.php\">click me</a></body></html>";

?>

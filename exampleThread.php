<?php



error_reporting(E_ALL);
ini_set('display_errors', '1');


include("thread.php");
 
function doSomething($res, $t)
{
    
	
	echo 'hhh';
	usleep($t);
    exit($res);
}
 
$thread1 = new Thread('doSomething');
//$thread2 = new Thread('doSomething');
//$thread3 = new Thread('doSomething');
 
$thread1->start(3, 10);
//$thread2->start(2, 40);
//$thread3->start(1, 30);
 
////while ($thread1->isAlive(1) || $thread2->isAlive(2) || $thread3->isAlive(3));
 
echo "Thread 1 exit code (should be 3): " . $thread1->getExitCode() . "\n";
//echo "Thread 2 exit code (should be 2): " . $thread2->getExitCode() . "\n";
//echo "Thread 3 exit code (should be 1): " . $thread3->getExitCode() . "\n";


echo "ll";

?>
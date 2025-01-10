<?php 
$redis = new Redis();  
$redis->connect('127.0.0.1', 6379); 
$lockKey = 'script_lock'; 
if ($redis->exists($lockKey)) {  
 echo "The script is now being executed. Please Wait!\n";  
 exit; 
} 
try { 
    $redis->set($lockKey, '1', ['nx', 'ex' => 6]);  
    echo "Start executing the script...\n"; 
    sleep(20);  
    echo "The script has been successfully executed.\n"; 
}  
catch (Exception $e) { 
    echo "Problem occured: " . $e->getMessage() . "\n"; 
}  
finally { 
    if ($redis->exists($lockKey)){ 
      $redis->del($lockKey); 
    } 
}

<?php

/** CLI FILE **/

function s($p1, $p2)
{
    return array_sum(array($p1,$p2));
}

switch (true)
{
    case (isset($argv[1]) &&
        isset($argv[2]) &&
        is_numeric($argv[1]) &&
        is_numeric($argv[2])):
        echo "Sum: ".s($argv[1],$argv[2])."\n\n";
        break;
    case (isset($argv[1]) && $argv[1] == "-h"):
    default:
        echo "usage: php task2.php [arguments]\n";
        echo "\n";
        echo "Arguments\n";
        echo "-h                  Show this help screen\n";
        echo "<number number>     Two numbers to be summed.\n";
        echo "\n";
}

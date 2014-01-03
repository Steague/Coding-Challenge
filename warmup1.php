<?php

/** CLI FILE **/

function d($dir)
{
    $result = array();

    $cdir = scandir($dir);
    foreach ($cdir as $key => $value)
    {
        if (!in_array($value,array(".","..")))
        {
            if (is_dir($dir.DIRECTORY_SEPARATOR.$value))
            {
                $result[$value] = d($dir.DIRECTORY_SEPARATOR.$value);
            }
            else
            {
                $result[] = $value;
            }
        }
    }

    return $result;
}

switch (true)
{
    case (isset($argv[1]) &&
        is_dir($argv[1])):
        echo "Dir: ".print_r(d($argv[1]),true)."\n\n";
        break;
    case (isset($argv[1]) && $argv[1] == "-h"):
    default:
        echo "usage: php warmup1.php [arguments]\n";
        echo "\n";
        echo "Arguments\n";
        echo "-h              Show this help screen\n";
        echo "<directory>     Two numbers to be summed.\n";
        echo "\n";
}

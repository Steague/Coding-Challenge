<?php

/** CLI FILE **/

function x($x)
{
        $food = new SimpleXMLElement(file_get_contents($x));

        $steps = array();
        foreach ($food->instructions->step as $step)
        {
                $steps[(string) $step['order']] = (string) $step;
        }

        ksort($steps);
        return implode(', ', $steps);
}

switch (true)
{
    case (isset($argv[1]) &&
        is_file($argv[1])):
        echo "Order: ".x($argv[1])."\n\n";
        break;
    case (isset($argv[1]) && $argv[1] == "-h"):
    default:
        echo "usage: php warmup2.php [arguments]\n";
        echo "\n";
        echo "Arguments\n";
        echo "-h             Show this help screen\n";
        echo "<xml file>     XML file to be parsed.\n";
        echo "\n";
}

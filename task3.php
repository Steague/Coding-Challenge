<?php

/** Webserver of CLI FILE **/

/**
 * Recursive
 */
function f($x)
{
	if ($x <= 3)
	{
		return $x;
	}

	return pow(f($x-1),3)+pow(f($x-2),2)+f($x-3);
}

/**
 * Iterative
 */
function f2($x)
{
	if ($x <= 3)
	{
		return $x;
	}

	$p3 = 1;
	$p2 = 2;
	$p1 = 3;
	$sum = 0;

	for ($i = 4; $i <= $x; $i++)
	{
		$sum = pow(($p1),3)+pow(($p2),2)+($p3);
		$p3 = $p2;
		$p2 = $p1;
		$p1 = $sum;
	}

	return $sum;
}

echo f(4)."\n"; // 32
echo f(5)."\n"; // 32779
echo f(6)."\n"; // 35219817466166

echo f2(4)."\n"; // 32
echo f2(5)."\n"; // 32779
echo f2(6)."\n"; // 35219817466166

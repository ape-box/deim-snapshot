<?php

date_default_timezone_set('Europe/Rome');

define('DOBENCHMARK', false);
if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
{
	require_once 'library/Ape/Benchmark.php';
	Ape_Benchmark::registerPoint('First php call!');
}

require './app/bootstrap.php';


if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
	echo Ape_Benchmark::getTimes();


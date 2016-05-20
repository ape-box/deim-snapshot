<?php

class Ape_Benchmark
{
	static private $_stack = array();

	static public function registerPoint($name = null)
	{
		!$name and $name = 'Pinpoint '.count(self::$_stack);

		self::$_stack[$name] = microtime(true);
	}

	static public function getTimes()
	{
		switch (count(self::$_stack))
		{
			case 0:
			case 1:
				$o = "\r\nNot enough pinpoints was registered!\r\n";
				break;
			case 2:
				$data = array_values(self::$_stack);
				$time = $data[1] - $data[0];
				$time *= 1000000;
				$time    = number_format($time, 0);
				$o = "\r\nSPAN : {$time} µS";
				break;
			default:
				$starttime = current(self::$_stack);
				$last = 0;
				$o = '';
				foreach (self::$_stack as $label => $pinpoint)
				{
					$time = $pinpoint - $starttime;
					$time *= 1000000;

					$elapsed = $time - $last;
					$last = $time;

					$time    = number_format($time);
					$elapsed = number_format($elapsed);
					$time    = sprintf('%7s', $time);
					$elapsed = sprintf('%7s', $elapsed);

					$label = sprintf('%-64s', $label);
					$o .= "\r\n[{$time} µS] : $label | {$elapsed} µS";
				}
				break;
		}
		return "<pre>$o</pre>";
	}
}
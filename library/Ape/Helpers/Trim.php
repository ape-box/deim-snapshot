<?php

class Ape_Helpers_Trim {
	static public function trim($txt, $limit=120)
	{
		$txt = strip_tags($txt);
		$txt = trim($txt, " \t\n\r\0\x0B\"'");
		$txt = str_replace('  ', ' ', $txt);
		$txt = explode(' ', $txt);
		$str = '';
		foreach($txt as $c){
			if (strlen($str) > $limit) {
				$str .= ' ...';
				break;
			}
			else {
				$str .= ' '.$c;
			}
		}
		return $str;
	}
}
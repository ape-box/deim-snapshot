<?php

class Ape_Filter_TextTokenizer
{
	/**
	 * @param string $string
	 * @param int $length
	 * @param string $separator
	 * @param bool $count_lenth count for strlen or token number
	 * @return string
	 */
	public static function filter($string, $length = 128, $separator = ' ', $count_lenth = true)
	{
		$string = (string)$string;
		$length = (int)$length;
		$separator = (string)$separator;
		$count_lenth = (bool)$count_lenth;
		if (empty($string) OR empty($separator)) return $string;

		$tokens = explode($separator, $string);
		$clean_string = array_shift($tokens);
		if ($count_lenth)
			foreach ($tokens as $token)
				if (strlen($clean_string.$separator.$token) > $length) return $clean_string;
				else $clean_string .= $separator.$token;
		else for ($i = 0; $i < $length; $i++)
			$clean_string .= $separator.$tokens[$i];
		return $clean_string;
	}
}
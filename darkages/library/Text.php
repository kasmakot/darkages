<?


class Text
{
	static function cutTo($text, $sTo) { return substr($text, 0, strrpos($text, $sTo)); }

	static function cutFrom($text, $sFrom) { return substr($text, strrpos($text, $sFrom) + strlen($sFrom)); }

	static function cut($text, $length, $eos = '')
	{
		if (strlen($text) <= $length) {
			return $text;
		}

		return substr($text, 0, $length) . $eos;
	}

	static function insert($text, $position, $string) { return substr_replace($text, $string, $position, 0); }

	static function isLength($text, $min = 0, $max = null)
	{
		return ($length = strlen($text)) >= $min && (is_null($max) || $length <= $max);
	}		

	static function toMonth($monthId)
	{
		$sMonth = self::monthList();

		return $sMonth[$monthId];
	}

	static function monthList()
	{
		return array(
			1 => 'января',
			2 => 'февраля',
			3 => 'марта',
			4 => 'апреля',
			5 => 'мая',
			6 => 'июня',
			7 => 'июля',
			8 => 'августа',
			9 => 'сентября',
			10 => 'октября',
			11 => 'ноября',
			12 => 'декабря'
		);
	}
}
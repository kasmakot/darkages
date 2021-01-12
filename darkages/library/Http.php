<?


class Http
{
	static function redirect($url, $code = 301, $info = 'Moved Permanently')
	{
		self::headers(array(
			'HTTP/1.1 ' . $code . ' ' . $info,
			'Location' => $url
		));
	}

	static function headers($data)
	{
		foreach ($data as $option => $value) {
			header((is_string($option) ? $option . ': ' : '') . $value);
		}
	}

	static function ajaxAnswer($code = 200, $data = array())
	{
		header('HTTP/1.1 ' . $code);

		if (count($data)) {
			self::headers(array('Content-Type' => 'application/vnd.api+json'));
			echo json_encode(self::utf8size($data));
		}

		exit;
	}

	static function utf8size($mixed)
	{
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = self::utf8size($value);
			}
		} else if (is_string($mixed)) {
			return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
		}

		return $mixed;
	}

	static function setupSession($data)
	{
		session_start();

		foreach ($data as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}

	static function getSession($checkLogin = false)
	{
		session_start();

		return $checkLogin
			? (key_exists('login', $_SESSION) && key_exists('password', $_SESSION) ? $_SESSION : null)
			: $_SESSION;
	}

	static function cleanSession()
	{
		session_start();
		session_unset();
		session_destroy();
		session_write_close();
	}
}
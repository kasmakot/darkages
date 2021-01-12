<?


class Database
{
	public static $hDb = null;


	public function __construct($params)
	{
		self::$hDb = mysqli_connect($params['host'], $params['username'], $params['password'], $params['name']);

		if (key_exists('charset', $params)) {
			self::$hDb->set_charset($params['charset']);
		}
	}

	public static function getLink() { return self::$hDb; }
}
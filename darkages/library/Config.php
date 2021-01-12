<?


class Config
{
	const DEFAULT_FILENAME = 'config.ini';
	const DEFAULT_DIRECTORY = 'configs';

	public $fileName = Config::DEFAULT_FILENAME;
	public $data = array();


	public function __construct($filePath = null)
	{
		if (file_exists(
			$path = is_null($filePath)
				? APPLICATION_PATH . Config::DEFAULT_DIRECTORY . '/' . $this->fileName
				: $filePath
		)) {
			$this->data = parse_ini_file($path);
		}
	}

	public function getData() { return $this->data; }
}
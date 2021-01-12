<?


class Autoloader
{
	public function __construct() { spl_autoload_register(array($this, '_autoload')); }

	protected function _autoload($class)
	{
		include((is_null($path = $this->_systemload($class)) ? $class : $path) . '.php');
	}

	protected function _systemload($class)
	{
		if (strpos($class, 'Service_') === 0) {
			return substr($class, strlen('Service_')) . 'Service';
		}

		if (strpos($class, 'Helper_') === 0) {
			return substr($class, strlen('Helper_'));
		}

		if (strpos($class, 'Form_') === 0) {
			return substr($class, strlen('Form_')) . 'Form';
		}

		if (strpos($class, 'Model_') === 0) {
			$modelName = substr($class, strlen('Model_'));
			return strtolower($modelName) . '/' . 'Model' . $modelName;
		}

		return null;
	}
}
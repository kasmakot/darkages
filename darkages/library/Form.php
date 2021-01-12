<?


class Form
{
	public $serviceName = null;

	protected $_errors = array();
	protected $_data = array();
	protected $_service = null;


	public function __construct()
	{
		if (!is_null($serviceName = $this->serviceName) && class_exists($serviceName = 'Service_' . $serviceName)) {
			$this->_service = new $serviceName();
		}
	}

	public function validate($data = null)
	{
		$this->_errors = array();

		$this->_execValidation(is_null($data) ? $this->_data : $data);

		return count($this->_errors) == 0; 
	}

	public function fill($data) { $this->_data = array_merge($this->_data, $data); }

	public function errors() { return $this->_errors; }

	public function addError($text) { $this->_errors[] = $text; }

	protected function _execValidation($data = null) {}

	static public function isInteger($input)
	{
		return ctype_digit(strval($input)) ? intval($input) : false;
	}
}
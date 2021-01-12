<?


class Controller
{
	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION = 'index';
	const DEFAULT_DIRECTORY = 'controllers';

	public $queries = array(
		'controller' => Controller::DEFAULT_CONTROLLER,
		'action' => Controller::DEFAULT_ACTION
	);
	public $configData = array();
	public $database = null;

	protected $_controllerDirectory = Controller::DEFAULT_DIRECTORY;
	protected $_controllerName = Controller::DEFAULT_CONTROLLER;


	public function __construct($data = array())
	{
		$this->configData = $data;

		if (key_exists('db', $data)) {
			$this->database = new Database($data['db']);
		}

		if (key_exists('path', $data) && key_exists('controllers', $data['path'])) {
			$this->_controllerDirectory = $data['path']['controllers'];
		}

		$this->_parseQuery();
		$this->_runController();

		$this->init();
	}
	
	public function init() {}

	public function isControllerExists($controllerName)
	{
		return file_exists(
			APPLICATION_PATH . $this->_controllerDirectory . '/'
				. ucfirst($controllerName) . 'Controller.php'
		);
	}

	public function getControllerName() { return $this->_controllerName; }

	public function isPost() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

	protected function _parseQuery()
	{
		$urlQueries = explode('/', $_SERVER['REQUEST_URI']);

		if (count($urlQueries) > 1) {
			$this->queries['controller'] = $urlQueries[1];
		}
		if (count($urlQueries) > 2) {
			$this->queries['action'] = $this->_parseAction($urlQueries[2]);
		}

		for ($i = 3; $i < count($urlQueries) - 1; $i++) {
			$this->queries[$urlQueries[$i]] = $urlQueries[$i+1];
		}

		$this->queries = array_merge($this->queries, $this->isPost() ? $_POST : $_GET);
	}

	protected function _runController()
	{
		$this->_controllerName = $this->isControllerExists($this->queries['controller'])
			? $this->queries['controller']
			: Controller::DEFAULT_CONTROLLER;

		return $this->_runActionController($this->_controllerName);
	}

	protected function _runActionController($controllerName)
	{
		$actionControllerName = ucfirst($controllerName) . 'Controller';

		include(APPLICATION_PATH . $this->_controllerDirectory . '/' . $actionControllerName . '.php');

		$this->_actionController = new $actionControllerName($this);

		return $this->_actionController;
	}

	protected function _parseAction($actionString)
	{
		$result = '';

		$up = false;

		for ($i = 0; $i < strlen($actionString); $i++) {
			$char = $actionString[$i];

			if ($char == '-') {
				$up = true;
				continue;
			}

			if ($up) {
				$char = ucfirst($char);
				$up = false;
			}

			$result .= $char;
		}

		return $result;
	}
}
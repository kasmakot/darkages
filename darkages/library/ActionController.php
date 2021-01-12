<?


class ActionController
{
	const DEFAULT_ACTION = 'index';

	public $actionName = ActionController::DEFAULT_ACTION;
	public $view = null;

	protected $_baseController = null;


	public function __construct($baseController)
	{
		$this->_baseController = $this->_loadBaseController($baseController);
		$configData = $this->_baseController->configData;

		if (!is_null($actionName = $this->getParam('action')) && $this->isActionExists($actionName)) {
			$this->actionName = $actionName;
		}

		$this->view = new View($this->getControllerName(), $this->actionName, $this->getAllParams());

		if (key_exists('path', $configData)) {
			if (key_exists('views', $configData['path'])) {
				$this->view->viewsPath = $configData['path']['views'];
			}
			if (key_exists('layouts', $configData['path'])) {
				$this->view->layoutsPath = $configData['path']['layouts'];
			}
		}

		$this->init();

		return call_user_func_array(array($this, ucfirst($this->actionName) . 'Action'), array());
	}
	// TODO? render view in __construct?
	public function __destruct() { $this->view->render(); }

	public function isActionExists($actionName)
	{
		return method_exists($this, ucfirst($actionName) . 'Action');
	}

	public function init() {}

	public function getParam($paramName, $defaultValue = null)
	{
		return array_key_exists($paramName, $this->_baseController->queries)
			? $this->_baseController->queries[$paramName]
			: $defaultValue;
	}

	public function getAllParams() { return $this->_baseController->queries; }

	public function getControllerName() { return $this->_baseController->getControllerName(); }

	public function getConfigData() { return $this->_baseController->configData; }

	public function isPost() { return $this->_baseController->isPost(); }

	protected function _loadBaseController($baseController) { return $baseController; }

	protected function _ajaxAnswer($data = array(), $code = 200)
	{
		$this->view->disableLayout();

		Http::ajaxAnswer($code, (is_array($data) && count($data)) ? $data : array(
			'result' => $code == 200 ? 'success' : 'error',
			'message' => $data,
			'statusCode' => $code
		));

		exit;
	}

	protected function _ajaxError($data = array(), $code = 403) { $this->_ajaxAnswer($data, $code); }

	protected function _ajaxErrors($errorList = array())
	{
		$this->_ajaxError(array(
			'result' => 'error',
			'messsages' => is_array($errorList) ? $errorList : array($errorList),
			'statusCode' => 403
		));
	}

	protected function _ajaxSuccess() { $this->_ajaxAnswer(); }

	protected function _ajaxSuccessData($data)
	{
		$this->_ajaxAnswer(array(
			'result' => 'success',
			'data' => $data,
			'statusCode' => 200
		));
	}
}
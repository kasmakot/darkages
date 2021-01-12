<?


class View
{
	const DEFAULT_VIEWS_DIRECTORY = 'views';
	const DEFAULT_LAYOUTS_DIRECTORY = 'layouts';
	const DEFAULT_LAYOUT_FILENAME = 'layout.phtml';

	public $data = array();
	public $viewPath = View::DEFAULT_VIEWS_DIRECTORY;
	public $layoutsPath = View::DEFAULT_LAYOUTS_DIRECTORY;
	public $layoutFileName = View::DEFAULT_LAYOUT_FILENAME;
	public $viewFileName = '';

	protected $_showLayout = true;
	protected $_showView = true;


	public function __construct($controllerName = null, $actionName = null, $params = array())
	{
		if (!is_null($controllerName)) {
			$this->viewFileName = $controllerName . '/'
				. $this->_normalizeActionViewName(
					is_null($actionName) ? ActionController::DEFAULT_ACTION : $actionName
				) . '.phtml';
		}

		$this->data = $params;
	}

	public function render()
	{
		if (!$this->_showLayout) {
			return;
		}

		extract($this->data);

		ob_start();
		include(APPLICATION_PATH . $this->layoutsPath . '/' . $this->layoutFileName);
		$fileContent = ob_get_contents();
		ob_end_clean();

		$viewContent = '';
		if ($this->_showView) {
			ob_start();
			include(APPLICATION_PATH . $this->viewPath . '/' . $this->viewFileName);
			$viewContent = ob_get_contents();
			ob_end_clean();
		}

		echo str_replace('{content}', $viewContent, $fileContent);
	}

	public function disableView() { $this->_showView = false; }
	public function disableLayout() { $this->_showLayout = false; }

	public function setLayout($filename)
	{
		$this->layoutFileName = $filename;
		$this->_showLayout = true;
	}

	public function helper() { return new Helper(); }

	protected function _normalizeActionViewName($name)
	{
		$result = '';

		for ($i = 0; $i < strlen($name); $i++) {
			$char = $name[$i];

			if (IntlChar::isupper($char)) {
				$char = strtolower($char);
				if ($i > 0) {
					$result .= '-';
				}
			}

			$result .= $char;
		}

		return $result;
	}
}
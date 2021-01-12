<?


class Service
{
	protected $_model = null;
	protected $_modelTableName = null;


	public function __construct()
	{
		if (is_null($this->_model)) {
			$modelClass = 'Model_' . Text::cutFrom(get_class($this), 'Service_');
			
			if (class_exists($modelClass)) {
				$this->_model = new $modelClass();
			} else if (!is_null($this->_modelTableName)) {
				$this->_model = new Table($this->_modelTableName);
			}
		} else if (is_string($this->_model)) {
			$this->_model = new $this->_model();
		}
	}
	
	public function getAll() { return $this->_model->fetchAll(); }
	
	public function find($data, $limit = null, $order = null)
	{
		return !is_array($data)
			? $this->_model->fetchById($data)
			: $this->model->fetchAllByParams($data, $order, $limit);
	}

	public function create($data) { return $this->_model->insert($data); }
}
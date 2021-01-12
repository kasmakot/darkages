<?


class Table
{
	protected $_tableName = null;
	protected $_fields = array();
	protected $_sql = null;
	protected $_indexFieldName = 'id';


	public function __construct($tableName = null)
	{
		if (!is_null($tableName)) {
			$this->_tableName = $tableName;
		}
		
		$this->_sql = new Sql($this->_tableName);
	}

	public function sql() { return $this->_sql; }

	public function query($query) { return Database::$hDb->query($query); }

	public function insert($data)
	{
		$sql = 'INSERT INTO `' . $this->_tableName . '`';

		$columns = array();
		$values = array();

		foreach ($data as $column => $value) {
			$columns[] = '`' . $column . '`';
			$values[] = is_null($value) ? 'NULL' : '\'' . $value . '\'';
		}

		$sql .= ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ');';
var_dump($sql);
		return $this->query($sql) ? Database::getLink()->insert_id : false;
	}

	public function fetchAll() { return $this->sql()->select()->query()->fetchAll(); }

	public function fetchAllByParams($data, $limit = null, $order = null)
	{
		return $this->sql()->select()
			->whereData($data)
			->limit($limit)
			->order($order)
			->query()->fetchAll();
	}

	public function firstByParams($data)
	{
		return $this->sql()->select()
			->whereData($data)
			->query()->fetchFirst();
	}

	public function fetchById($id)
	{
		return $this->firstByParams(array($this->_indexFieldName => $id));
	}
}
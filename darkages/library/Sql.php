<?


class Sql
{
	protected $_sql = '';
	protected $_wheres = array();
	protected $_joins = array();
	protected $_limit = 0;
	protected $_orders = array();
	protected $_tableName = null;
	protected $_tableAlias = null;
	protected $_queryResult = null;
	protected $_indexFieldName = 'id';
	protected $_fields = array();


	public function __construct($tableName = null)
	{
		$this->_tableName = $tableName;

		return $this;
	}

	public function whereCondition($text)
	{
		if (strlen($text)) {
			$this->_wheres[] = $text;
		}

		return $this;
	}

	public function where($field, $data = null, $operator = null)
	{
		$fieldName = is_array($field) ? '`' . $field[0]. '`.`' . $field[1]. '`' : '`' . $field . '`';

		if (is_array($data)) {
			$this->_wheres[] = $fieldName . ' IN (\'' . implode('\',\'', $data) . '\')';
		} else if (is_null($data)) {
			$this->_wheres[] = $fieldName . ' IS NULL';
		} else {
			$this->_wheres[] = $fieldName . ' ' . (!is_null($operator) ? $operator : '=') . '\'' . $data . '\'';
		}

		return $this;
	}

	public function whereIsNotNull($field, $data)
	{
		if (!is_null($data)) {
			return $this->where($field, $data);
		}

		return $this;
	}

	public function whereData($data)
	{
		foreach ($data as $key => $value) {
			$this->where(array(!is_null($this->_tableAlias) ? $this->_tableAlias : $this->_tableName, $key), $value);
		}

		return $this;
	}

	public function joinLeft($table, $field, $fieldSource, $fields = null, $alias = null)
	{
		if (is_array($table)) {
			$key = array_key_last($table);
			$alias = $table[$key];
			$table = $key;
		}

		$this->_joins[] = array($table, $field, $fieldSource, $type = 'LEFT', $alias);

		if (is_null($alias)) {
			$alias = $table;
		}

		if (is_null($fields)) {
			$this->_fields[] = array($alias, '*');
		} else if (is_array($fields) && count($fields)) {
			foreach ($fields as $key => $value) {
				$this->_fields[] = !is_int($key) ? array($alias, $key, $value) : array($alias, $value);
			}
		}

		return $this;
	}

	public function fields($list = array(), $resetFields = false)
	{
		if ($resetFields) {
			$this->_fields = array();
		}

		if (is_array($list) && count($list)) {
			foreach ($list as $key => $field) {
				if (is_array($field)) {
					if (count($field) == 3) {
						$this->_fields[] = $field;
					} else if (count($field) == 2) {
						$this->_fields[] = array(NULL, $field[0], $fields[1]);
					}
				} else {
					$this->_fields[] = is_int($key) ? array($field) : array(NULL, $key, $field);
				}
			}
		}

		return $this;
	}

	public function select($tableInfo = null)
	{
		if (!is_null($tableInfo)) {
			if (is_array($tableInfo) && count($tableInfo)) {
				foreach ($tableInfo as $key => $value) {
					if (is_string($key)) {
						$this->_tableName = $key;
						$this->_tableAlias = $value;
					} else {
						$this->_tableName = $value;
					}
					break;
				}
			} else {
				$this->_tableName = $tableInfo;
			}
		}

		$this->_fields[] = array(!is_null($this->_tableAlias) ? $this->_tableAlias : $this->_tableName, '*');

		return $this;
	}

	public function delete()
	{
		$this->_sql = "DELETE FROM `" . $this->_tableName . "`";

		return $this;
	}

	public function setQuery($txt)
	{
		$this->_sql = $txt;

		return $this;
	}

	public function query($run = true)
	{
		if (count($this->_fields)) {
			$this->_sql = "SELECT ";

			foreach ($this->_fields as $key => $field) {
				if (is_array($field)) {
					$this->_sql .= !is_null($field[0])
						? ($field[0] == '*' ? '*' : '`' . $field[0] . '`')
						: '';

					if (count($field) > 1) {
						$this->_sql .= (!is_null($field[0]) ? '.' : '')
							. ($field[1] == '*' ? '*' : '`' . $field[1] . '`');
					}
					if (count($field) > 2) {
						$this->_sql .= ' as `' . $field[2] . '`';
					}
				} else {
					$this->_sql .= ($field == '*' ? '*' : '`' . $field . '`');
				}

				if (/*array_key_last*/key(array_reverse($this->_fields)) != $key) {
					$this->_sql .= ',';
				}
			}

			$this->_sql .= " FROM `" . $this->_tableName . "`"
				. (!is_null($this->_tableAlias) ? ' as `' . $this->_tableAlias . '`' : '');
		}

		foreach ($this->_joins as $join) {
			$this->_sql .= ' ' . $join[3] . ' JOIN `' . $join[0] . '`'
				. (!is_null($join[4]) ? ' as `' . $join[4] . '`' : '')
				. ' ON ';
			if (!is_array($join[1])) {
				$this->_sql .= '`' . $join[!is_null($join[4]) ? 4 : 0] . '`.`' . $join[1] . '`' . ' = '
					. (is_array($join[2]) ? '`' . $join[2][0] . '`.`' . $join[2][1] . '`' : '`' . $join[2] . '`');
			} else {
				foreach ($join[1] as $key => $field) {
					$this->_sql .= '`' . $join[!is_null($join[4]) ? 4 : 0] . '`.`' . $field . '`' . ' = '
						. '`' . $join[2][$key][0] . '`.`' . $join[2][$key][1] . '`';
					if (array_key_last($join[1]) != $key) {
						$this->_sql .= ' AND ';
					}
				}
			}
		}

		if (count($this->_wheres)) {
			$this->_sql .= ' WHERE (' . implode(') AND (', $this->_wheres) . ')';
		}

		if (count($this->_orders)) {
			$first = true;

			foreach ($this->_orders as $key => $value) {
				$this->_sql .= (!$first ? ',' : '') . ' ORDER BY `' . $key . '` ' . $value;
				$first = false;
			}
		}

		if ($this->_limit > 0) {
			$this->_sql .= ' LIMIT ' . $this->_limit;
		}

		if (!$run) {
			return $this->_sql;
		}

		if (!($this->_queryResult = Database::$hDb->query($this->_sql))) {
			var_dump(mysqli_error(Database::$hDb));
		}

		return $this->clear();
	}

	public function fetchAll()
	{
		return !is_null($this->_queryResult) && $this->_queryResult && $this->_queryResult->num_rows > 0
			? $this->_queryResult->fetch_all(MYSQLI_ASSOC)
			: null; 
	}

	public function fetchFirst()
	{
		return !is_null($rows = $this->fetchAll()) ? $rows[0] : null; 
	}

	public function limit($num)
	{
		if (!is_null($num) && is_integer($num)) {
			$this->_limit = $num;
		}

		return $this;
	}

	public function order($order, $desc = null)
	{
		if (is_array($order)) {
			foreach ($order as $key => $value) {
				$this->_orders[$key] = $value;
			}
		} else if (!is_null($order)) {
			$this->_orders[$order] = !is_null($desc) ? $desc : 'ASC';
		}

		return $this;
	}

	public function clear()
	{
		$this->_wheres = array();
		$this->_joins = array();
		$this->_limit = 0;
		$this->_orders = array();
		$this->_fields = array();

		return $this;
	}

	public function setIndexFieldName($name)
	{
		$this->_indexFieldName = $name;

		return $this;
	}

	public function debug()
	{
		echo $this->_sql;

		return $this;
	}
}
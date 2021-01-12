<?


class Service_User extends Service
{
	public function getAll()
	{
		return $this->_model->fetchAll();
	}

	public function findByLogin($login)
	{
		return $this->_model->fetchFirstByLogin($login);
	}

	public function findByEmail($email)
	{
		return $this->_model->fetchAllByEmail($email);
	}
}
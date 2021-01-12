<?


class Model_User extends Table
{
	protected $_tableName = 'persons';


	public function fetchFirstByLogin($login)
	{
		return $this->firstByParams(array('p_login' => $login));
	}

	public function fetchAllByEmail($email)
	{
		return $this->fetchAllByParams(array('p_email' => $email));
	}
}
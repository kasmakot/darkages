<?


class Form_Registration extends Form
{
	public $serviceName = 'User';


	protected function _execValidation($data = array())
	{
		if (key_exists('name', $data) && strlen($data['name']) > 32) {
			$this->addError('Имя должно содержать не более 32 символов');
		}

		if (key_exists('surname', $data) && strlen($data['surname']) > 64) {
			$this->addError('Фамилия должна содержать не более 64 символов');
		}

		if (!key_exists('race', $data) || !in_array($data['race'], array(1, 2, 3))) {
			$this->addError('Необходимо выбрать расу');
		}

		if (!key_exists('email', $data) || empty($data['email'])) {
			$this->addError('Необходимо задать eMail');
		} else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$this->addError('Некорректный формат адреса почты');
		} else if ($this->_service->findByEmail($data['email'])) {
			$this->addError('Указанный eMail уже зарегистрирован');
		}

		if (key_exists('country', $data) && strlen($data['country']) > 32) {
			$this->addError('Длина названия страны не может выходить за рамки 32 символов');
		}

		if (key_exists('city', $data) && strlen($data['city']) > 32) {
			$this->addError('Длина названия города не может выходить за рамки 32 символов');
		}

		if (!key_exists('birthday', $data) || empty($data['birthday'])) {
			$this->addError('Необходимо задать дату рождения');
		} else if (!$this->_validateBirthday($data['birthday'])) {
			$this->addError('Некорректный формат указанного дня рождения');
		}

		if (key_exists('sex', $data) && !in_array($data['sex'], array(0, 1))) {
			$this->addError('Необходимо задать пол');
		}

		if (!key_exists('login', $data) || empty($data['login'])) {
			$this->addError('Необходимо задать никнейм');
		} else if (!preg_match('/^[a-zA-Z0-9а-яА-Я]{1}[a-zA-Z0-9а-яА-Я\s]+[a-zA-Z0-9а-яА-Я]{1}$/u', $data['login'])) {
			$this->addError('Неверный формат игрового логина');
		} else if ($this->_service->findByLogin($data['login'])) {
			$this->addError('Указанный логин уже существует');
		}

		if (!key_exists('password', $data) || empty($data['password'])) {
			$this->addError('Необходимо задать пароль');
		} else if (!(($passwordLength = strlen($data['password'])) >= 6 && $passwordLength <= 32)) {
			$this->addError('Длина пароля должна быть от 6 до 32 символов');
		} else if (!key_exists('repeat', $data)) {
			$this->addError('Необходимо подтвреждение пароля');
		} else if ($data['password'] !== $data['repeat']) {
			$this->addError('Указанные паоли не совпадают');
		}
	}

	protected function _validateBirthday($birthday)
	{
		$list = explode('.', $birthday);

		if (count($list) != 3) {
			return false;
		}

		if (!($day = self::isInteger($list[0])) || !($day > 0 && $day <= 31)) {
			return false;
		}

		if (!($month = self::isInteger($list[1])) || !($month > 0 && $month <= 12)) {
			return false;
		}

		if (($year = self::isInteger($list[2])) === false || !($year >= 0 && $year <= date('Y', time()))) {
			return false;
		}

		return true;
	}
}
<?


class VrController extends ActionController
{
	public function init()
	{
		$this->view->setLayout('blank.phtml');
	}

	public function indexAction()
	{
		Http::redirect('/vr/default/');
	}

	public function registrationAction()
	{
		$userService = new Service_User();

		if ($this->isPost()) {
			$registrationForm = new Form_Registration();

			$params = $this->getAllParams();
			$registrationForm->fill($params);

			if ($registrationForm->validate()) {
				if ($userService->create(array(
					'p_login' => $params['login'],
					'p_password' => md5($params['password']),
					'p_firstName' => $params['name'],
					'p_lastName' => $params['surname'],
					'p_country' => $params['country'],
					'p_city' => $params['city'],
					'p_email' => $params['email'],
					'p_birthday' => date('Y-m-d', strtotime($params['birthday'])),
					'p_gender' => $params['sex'],
					'p_icq' => Text::cut($params['icq'], 16),
					'p_im' => Text::cut($params['aol'], 32),
					'p_description' => Text::cut($params['info'], 2048),
					'p_race' => $params['race'],
					'p_referrer' => Text::cut($params['partner'], 128)
				))) {
					Http::setupSession(array(
						'login' => $params['login'],
						'password' => md5($user['password'])
					));
					Http::redirect('/vr/layout/');
				} else {
					$this->view->data['system_errors'][] = 'Ошибка создания записи в БД';
				}
			} else {
				$this->view->data['system_errors'] = $registrationForm->errors();
			}
			$this->view->data['params'] = $params;
		}
	}

	public function defaultAction()
	{
		$this->view->setLayout('layout.phtml');
		
		$this->view->data['users'] = (new Service_User())->getAll();
	}
}
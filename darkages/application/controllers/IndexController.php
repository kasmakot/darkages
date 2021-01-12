<?


class IndexController extends ActionController
{
	public function indexAction()
	{
		$userService = new Service_User();

		$this->view->data['users'] = $userService->getAll();
	}
}
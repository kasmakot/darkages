<?
	include('Autoloader.php');


	class Starter
	{
		protected $autoloader = null;


		public function __construct() { $this->autoloader = new Autoloader(); }

		public function run()
		{
			return new Controller((new Config())->getData());
		}
	}
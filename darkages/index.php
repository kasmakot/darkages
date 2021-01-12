<?


define('APPLICATION_PATH', getcwd() . '/application/'); //print 'Starting...';

set_include_path(implode(PATH_SEPARATOR . APPLICATION_PATH, array(get_include_path(),
	'services/',
	'../library/',
	'forms/',
	'helpers/'
)));

include('Starter.php');

(new Starter())->run();
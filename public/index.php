<?php
declare(strict_types = 1);
namespace ObieSampleApp;
use Obie\Http\Response;
use Obie\Http\Route;
use Obie\Http\Router;
use Obie\Http\RouterInstance;
use Obie\View;

/** @var App $app */
if (!($app = require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'init.php')) return;

class SampleController {
	function index(Route $route, array $args) {
		$route->setContentType(Router::CONTENT_TYPE_HTML);
		return View::render('index');
	}
}

Router::get('/', [SampleController::class, 'index']);

switch (Router::execute()) {
case RouterInstance::ENOT_FOUND:
	App::respond(status_code: Response::HTTP_NOT_FOUND);
	return;

case RouterInstance::EINVALID_METHOD:
	App::respond(status_code: Response::HTTP_METHOD_NOT_ALLOWED);
	return;

case RouterInstance::ENO_CONTENT:
	App::respond(status_code: Response::HTTP_OK);
	return;
}

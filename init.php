<?php
declare(strict_types = 1);
namespace ObieSampleApp;
use Obie\Encoding\Uuid;
use Obie\Http\Response;
use Obie\Http\Router;
use Obie\Log;
use Obie\View;

define('OBIE_APP_DIR', dirname(__FILE__));
$autoload_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!is_file($autoload_path)) {
	error_log('vendor/autoload.php not found. Install composer dependencies: https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies', E_USER_ERROR);
	return false;
}
require $autoload_path;
unset($autoload_path);

class App extends \Obie\App {
	public static function initRouter(): bool {
		if (!parent::initRouter()) return false;

		// Set security headers
		$csp = [
			'frame-ancestors' => "'none'",
			'script-src' => "'nonce-" . Router::vars()->get('nonce') . "'",
		];
		$i = 0;
		$cspstr = '';
		foreach ($csp as $k => $v) {
			if ($i > 0) $cspstr .= '; ';
			$cspstr .= $k . ' ' . $v;
			$i++;
		}
		Response::current()?->setHeader('Content-Security-Policy', $cspstr);
		Response::current()?->setHeader('Referrer-Policy', 'same-origin');

		// Set request ID
		$request_id = Uuid::generate();
		Response::current()?->setHeader('X-Request-Id', $request_id);
		Log::setDefaultContext(['request_id' => $request_id]);
		Log::setFormat("[%datetime%][%context.request_id%] %channel%.%level_name%: %context.prefix%%message%%context.suffix%\n");

		// Set request timestamp
		$now = new \DateTime('now', new \DateTimeZone('UTC'));
		Router::vars()->set('now', $now);
		Response::current()?->setHeader('Date', $now->format('D, d M Y H:i:s T'));

		return true;
	}

	public static function initViews(): bool {
		if (!parent::initViews()) return false;

		View::$default_vars = array_merge_recursive(View::$default_vars, [
			'nonce' => Router::vars()->get('nonce'),
			'language' => static::getI18n()->getLanguage(),
		]);

		return true;
	}

	public static function respond(mixed $data = null, array $meta = [], int $status_code = Response::HTTP_OK, ?string $status_description = null) {
		if (!Response::current()) return;
		$status_description ??= Router::HTTP_STATUSTEXT[$status_code];
		Response::current()->setCode($status_code);
		Router::sendJSON([
			'meta' => array_merge([
				'statusDescription' => $status_description,
				'requestId' => Router::vars()->get('nonce'),
				'requestTime' => Router::vars()->get('now')?->format(\DateTime::ATOM),
			], $meta),
			'data' => $data,
		]);
	}
}

App::register();
if (!App::init()) {
	App::respond(status_code: Response::HTTP_INTERNAL_SERVER_ERROR);
	return;
}

return App::$app;
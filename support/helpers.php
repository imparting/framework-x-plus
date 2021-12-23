<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use React\Http\Message\Response;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use support\Config;
use function React\Promise\resolve;

define('BASE_PATH', realpath(__DIR__ . '/../'));

function dd(...$params)
{
    var_dump(...$params);
}

/**
 * @return string
 */
function base_path()
{
    return BASE_PATH;
}

/**
 * @return string
 */
function app_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'app';
}

/**
 * @return string
 */
function public_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'public';
}

/**
 * @return string
 */
function config_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'config';
}

/**
 * @return string
 */
function runtime_path()
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'runtime';
}

/**
 * @param $data
 * @return Response
 */
function ok($data = null): Response
{
    return response($data);
}

/**
 * @param string $msg
 * @param null $data
 * @param int $code
 * @return Response
 */
function fail($msg = 'fail', $data = null, $code = 1000): Response
{
    return response($data, $msg, $code);
}

/**
 * @param string $body
 * @param string $msg
 * @param int $code
 * @param int $status
 * @return Response
 */
function response($body = '', $msg = '', $code = 0, $status = 200): Response
{
    return match (support\App::$contentType) {
        'json' => json($body, $code, $msg, $status),
        'jsonp' => jsonp($body, $status),
        'xml' => xml($body, $status),
        default => new Response($status, [], (string)$body),
    };
}

/**
 * @param $data
 * @param int $code
 * @param string $msg
 * @param int $status
 * @param int $options
 * @return Response
 */
function json($data, $code = 0, $msg = '', $status = 200, $options = JSON_UNESCAPED_UNICODE)
{
    if ($data === null) $data = new stdClass();
    $body = new stdClass();
    $body->code = $code;
    $body->data = $data;
    $body->msg = $msg;
    return new Response($status, ['Content-Type' => 'application/json'], json_encode($body, $options));
}

/**
 * @param $xml
 * @param $status
 * @return Response
 */
function xml($xml, $status)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response($status, ['Content-Type' => 'text/xml'], $xml);
}

/**
 * @param $data
 * @param $status
 * @param string $callback_name
 * @return Response
 */
function jsonp($data, $status, $callback_name = 'callback')
{
    if (!is_scalar($data) && null !== $data) {
        $data = json_encode($data);
    }
    return new Response($status, [], "$callback_name($data)");
}

/**
 * @param $location
 * @param int $status
 * @param array $headers
 * @return Response
 */
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        foreach ($headers as $name => $value) {
            $response->withHeader($name, $value);
        }
    }
    return $response;
}

///**
// * @param $template
// * @param array $vars
// * @param null $app
// * @return Response
// */
//function view($template, $vars = [], $app = null)
//{
//    static $handler;
//    if (null === $handler) {
//        $handler = config('view.handler');
//    }
//    return new Response(200, [], $handler::render($template, $vars, $app));
//}

///**
// * @return Request
// */
//function request()
//{
//    return App::request();
//}

/**
 * @param $key
 * @param null $default
 * @return mixed
 */
function config($key = null, $default = null)
{
    return Config::get($key, $default);
}

///**
// * @param $name
// * @param array $parameters
// * @return string
// */
//function route($name, $parameters = [])
//{
//    $route = Route::getByName($name);
//    if (!$route) {
//        return '';
//    }
//    return $route->url($parameters);
//}

///**
// * @param null $key
// * @param null $default
// * @return mixed
// */
//function session($key = null, $default = null)
//{
//    $session = request()->session();
//    if (null === $key) {
//        return $session;
//    }
//    if (\is_array($key)) {
//        $session->put($key);
//        return null;
//    }
//    return $session->get($key, $default);
//}

///**
// * @param null|string $id
// * @param array $parameters
// * @param string|null $domain
// * @param string|null $locale
// * @return string
// */
//function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
//{
//    $res = Translation::trans($id, $parameters, $domain, $locale);
//    return $res === '' ? $id : $res;
//}
//
///**
// * @param null|string $locale
// * @return string
// */
//function locale(string $locale = null)
//{
//    if (!$locale) {
//        return Translation::getLocale();
//    }
//    Translation::setLocale($locale);
//}

///**
// * @param $worker
// * @param $class
// */
//function worker_bind($worker, $class)
//{
//    $callback_map = [
//        'onConnect',
//        'onMessage',
//        'onClose',
//        'onError',
//        'onBufferFull',
//        'onBufferDrain',
//        'onWorkerStop',
//        'onWebSocketConnect'
//    ];
//    foreach ($callback_map as $name) {
//        if (method_exists($class, $name)) {
//            $worker->$name = [$class, $name];
//        }
//    }
//    if (method_exists($class, 'onWorkerStart')) {
//        call_user_func([$class, 'onWorkerStart'], $worker);
//    }
//}

/**
 * @return int
 */
function cpu_count()
{
    // Windows does not support the number of processes setting.
    if (\DIRECTORY_SEPARATOR === '\\') {
        return 1;
    }
    if (strtolower(PHP_OS) === 'darwin') {
        $count = shell_exec('sysctl -n machdep.cpu.core_count');
    } else {
        $count = shell_exec('nproc');
    }
    $count = (int)$count > 0 ? (int)$count : 4;
    return $count;
}


/**
 * @param $stream
 * @param mixed ...$callables
 * @return PromiseInterface|Promise
 */
function streamMap($stream, ...$callables): PromiseInterface|Promise
{
    // stream already ended => resolve with empty buffer
    if ($stream instanceof ReadableStreamInterface) {
        // readable or duplex stream not readable => already closed
        // a half-open duplex stream is considered closed if its readable side is closed
        if (!$stream->isReadable()) {
            return resolve(array());
        }
    } elseif ($stream instanceof WritableStreamInterface) {
        // writable-only stream (not duplex) not writable => already closed
        if (!$stream->isWritable()) {
            return resolve(array());
        }
    }

    $buffer = array();
    $bufferer = function ($data = null) use (&$buffer, $callables) {
        foreach ($callables as $callable) {
            $data = $callable($data);
        }
        $buffer [] = $data;
    };
    $stream->on('data', $bufferer);
    $promise = new Promise(function ($resolve, $reject) use ($stream, &$buffer) {
        $stream->on('error', function (\Exception $e) use ($reject) {
            $reject(new \RuntimeException(
                'An error occured on the underlying stream while buffering: ' . $e->getMessage(),
                $e->getCode(),
                $e
            ));
        });
        $stream->on('close', function () use ($resolve, &$buffer) {
            $resolve($buffer);
        });
    }, function ($_, $reject) {
        $reject(new \RuntimeException('Cancelled buffering'));
    });
    return $promise->then(null, function ($error) use (&$buffer, $bufferer, $stream) {
        // promise rejected => clear buffer and buffering
        $buffer = array();
        $stream->removeListener('data', $bufferer);
        throw $error;
    });
}

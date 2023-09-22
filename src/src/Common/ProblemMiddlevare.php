<?php
declare(strict_types=1);

namespace Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use ErrorException;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

/**
 *
 */
class ProblemMiddlevare implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            set_error_handler($this->createErrorHandler());
            $response = $handler->handle($request);
        } catch (Throwable $t) {
            dd($t->getMessage(), " in ". $t->getFile() .':'. $t->getLine());
        } finally {
            restore_error_handler();
        }

        return $response;
    }

    /**
     * @return callable
     */
    protected function createErrorHandler(): callable
    {
        /**
         * @param int $errno
         * @param string $errstr
         * @param string $errfile
         * @param int $errline
         */
        return static function (int $errno, string $errstr, string $errfile, int $errline): void {
            if (error_reporting() === 0) {
                return;
            }
            if (! (error_reporting() & $errno)) {
                return;
            }
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }
}
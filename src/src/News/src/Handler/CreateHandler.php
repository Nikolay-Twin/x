<?php

declare(strict_types=1);

namespace News\Handler;

use Common\Exceptions\AppErrorException;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use News\Contract\NewsServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;


/**
 *
 */
final class CreateHandler implements RequestHandlerInterface
{

    public function __construct(
        private readonly NewsServiceInterface $newsService,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();
            $this->validate($data);
            $this->newsService->create($data['title'], $data['text']);
        } catch (AppErrorException $e) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (Throwable $t) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => 'Internal server error'
            ], StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'News added'
        ], StatusCodeInterface::STATUS_CREATED);
    }

    /**
     * @param array $data
     * @return void
     * @throws AppErrorException
     */
   private function validate(array $data): void
   {
       match (true) {
           !isset($data['title']) || '' === $data['title']
               => throw new AppErrorException('Title must not be empty'),

           !is_string($data['title'])
               => throw new AppErrorException('Title must be a string'),

           !isset($data['text']) || '' === $data['text']
               => throw new AppErrorException('Text must not be empty'),

           !is_string($data['text'])
               => throw new AppErrorException('Text must be a string'),

           default => null
       };
   }

}

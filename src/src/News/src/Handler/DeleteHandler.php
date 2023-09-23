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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class DeleteHandler implements RequestHandlerInterface
{

    public function __construct(
        private readonly NewsServiceInterface $newsService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();
            $id = $this->validate($request->getAttribute('id'));
            $mode = !isset($data['soft']);
            $this->newsService->delete($id, $mode);
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
            'message' => 'News removed'
        ], StatusCodeInterface::STATUS_CREATED);
    }

    /**
     * @param string $id
     * @return string
     * @throws AppErrorException
     */
    private function validate(string $id): UuidInterface
    {
        try {
            return Uuid::fromString($id);
        } catch (\Throwable $t) {
            throw new AppErrorException($t->getMessage());
        }
    }
}


<?php

declare(strict_types=1);

namespace News\Handler;

use Common\Exceptions\AppErrorException;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use News\Contract\NewsServiceInterface;
use News\Entity\Status;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

final class UpdateHandler implements RequestHandlerInterface
{

    public function __construct(
        private readonly NewsServiceInterface $newsService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        try {
            $id = $request->getAttribute('id');
            $data = $request->getParsedBody();
            $data = $this->validate(array_merge($data, ['id' => $id]));
            $this->newsService->update($data);
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
            'message' => 'News edited'
        ], StatusCodeInterface::STATUS_CREATED);
    }

    /**
     * @param array $data
     * @return array
     * @throws AppErrorException
     */
    private function validate(array $data): array
    {
        $statuses = [
            Status::Publicated->value => Status::Publicated,
            Status::Draft->value => Status::Draft,
        ];
        $allowedStatuses = array_keys($statuses);

        try {
            $data['id'] = Uuid::fromString($data['id']);

            $data = match (true) {
                isset($data['title']) && !is_string($data['title'])
                    => throw new AppErrorException('Title must be a string'),

                isset($data['text'])  && !is_string($data['text'])
                    => throw new AppErrorException('Text must be a string'),

                isset($data['status']) && !in_array($data['status'], $allowedStatuses)
                    => throw new AppErrorException(sprintf('Status must be %s or %s', $allowedStatuses[0], $allowedStatuses[1])),

                default => $data
            };
            $data['status'] = $statuses[$data['status']];
            return $data;
        } catch (\Throwable $t) {
            throw new AppErrorException($t->getMessage());
        }
    }
}
<?php

declare(strict_types=1);

namespace News\Handler;


use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use News\ConfigProvider;
use News\Contract\NewsServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ListHandler implements RequestHandlerInterface
{

    public function __construct(
        private readonly NewsServiceInterface $newsService,
        private readonly ConfigProvider $config
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getQueryParams();
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? $this->config->__invoke()['limit'];

        $news = $this->newsService->findAll($page, $limit);
        $data = [];
        foreach($news as $item) {
            $data[] = [
                'id' => $item->getId(),
                'title' => $item->getTitle(),
                'text' => $item->getText(),
                'created' => $item->getCreated()->format('c')
            ];
        }
        if (empty($data)) {
            $data = ['title' => 'Ничего интересного((('];
        }
        return new JsonResponse($data, StatusCodeInterface::STATUS_OK);
    }
}

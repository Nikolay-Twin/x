<?php

declare(strict_types=1);

namespace News;
use Common\Exceptions\AppErrorException;
use Doctrine\ORM\EntityManagerInterface;
use News\Contract\NewsServiceInterface;
use News\Entity\News;
use News\Entity\Status;
use News\Repository\NewsRepository;
use Ramsey\Uuid\UuidInterface;

class NewsService implements NewsServiceInterface
{

    public function __construct(
        private EntityManagerInterface $em
    ) { }

    /**
     * @param UuidInterface $id
     * @return News|null
     */
    public function findById(UuidInterface $id): ?News
    {
        return $this->getRepository()->findById($id);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param Status $status
     * @return iterable
     */
    public function findAll(
        int $page = 1,
        int $limit = 10,
        Status $status = Status::Publicated
    ): iterable {
        $offset =  ($page - 1) * $limit;
        return $this->getRepository()->findBy([
           'status' => $status
        ], [
            'created' => 'DESC'
        ], $limit, $offset);
    }

    /**
     * @param string $title
     * @param string $text
     * @return News
     */
    public function create(string $title, string $text): News
    {
        $news = new News($title, $text);
        $this->em->persist($news);
        $this->em->flush();
        return $news;
    }

    /**
     * @param array $data
     * @return void
     * @throws AppErrorException
     */
    public function update(array $data): void
    {
        $news = $this->getRepository()->findById($data['id']);
        if (is_null($news)) {
            throw new AppErrorException(sprintf('News with id %s not found', $data['id']));
        }
        unset($data['id']);
        foreach ($data as $property => $value) {
            $news->$property = $value;
        }
        $this->em->persist($news);
        $this->em->flush();
    }

    /**
     * @param UuidInterface $id
     * @param bool $hard
     * @return void
     */
    public function delete(UuidInterface $id, bool $hard = false): void
    {
        $news = $this->findById($id);
        if (!is_null($news)) {
            if ($hard) {
                $this->em->remove($news);
            } else {
                $news->status = Status::Deleted;
            }
            $this->em->flush();
        }
    }

    /**
     * @return NewsRepository
     */
    private function getRepository(): NewsRepository
    {
        return $this->em->getRepository(News::class);
    }
}

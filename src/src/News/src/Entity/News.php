<?php

declare(strict_types=1);

namespace News\Entity;

use Common\BaseEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use News\Repository\NewsRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\Table(name: 'news')]
final class News extends BaseEntity
{

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private UuidInterface $id;

    #[ORM\Column(name: 'title', type: 'string')]
    private string $title;


    #[ORM\Column(name: 'text', type: 'string')]
    private string $text;

    
    #[ORM\Column(name: 'status', type: 'smallint', enumType: Status::class)]
    private Status $status;

    #[ORM\Column(name: 'created_date', type: 'datetime_immutable')]
    private readonly DateTimeImmutable $created;


    public function __construct(
        string $title,
        string $text
    ) {
        $this->id = Uuid::uuid7();
        $this->title = $title;
        $this->text = $text;
        $this->created = new DateTimeImmutable();
        $this->status = Status::Draft;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @param Status $status
     * @return void
     */
    public function changeStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $title
     * @return void
     */
    public function changeTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $text
     * @return void
     */
    public function changeText(string $text): void
    {
        $this->text = $text;
    }
}

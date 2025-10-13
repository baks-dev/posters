<?php

declare(strict_types=1);

namespace BaksDev\Posters\Repository\NewBdal;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Entity\Text\PosterText;


final class NewBdalRepository implements NewBdalInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function findAll(): array|bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal->select('id');
        $dbal->from(Poster::class, 'poster');

        $dbal->leftJoin(
            'poster',
            PosterEvent::class,
            'event',
            'event.id = posters.event',

        );

        $dbal
            ->addSelect('text.css AS css')
            ->leftJoin(
                'poster',
                PosterText::class,
                'text',
                'text.event = posters.event',
            );


        return $dbal
            ->enableCache('posters', '30 minutes')
            ->fetchAllAssociative();
    }
}
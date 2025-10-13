<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Posters\Repository\AllPosters;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Entity\Profile\PosterProfile;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileTokenStorage\UserProfileTokenStorageInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class AllPostersRepository implements AllPostersInterface
{
    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
        private readonly UserProfileTokenStorageInterface $UserProfileTokenStorage
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    /** Метод возвращает пагинатор Poster */
    public function findPaginator(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('poster.id')
            ->addSelect('poster.event AS event')
            ->from(Poster::class, 'poster');

        $dbal
            ->addSelect('poster_profile.value AS profile_uid')
            ->leftJoin(
                'poster',
                PosterProfile::class,
                'poster_profile',
                'poster_profile.event = poster.event',
            );

        $dbal->andWhere('(poster_profile.value IS NULL OR poster_profile.value = :profile)')
            ->setParameter(
                'profile',
                $this->UserProfileTokenStorage->getProfile(),
                UserProfileUid::TYPE,
            );

        $dbal
            ->addSelect('event.title AS poster_title')
            ->addSelect('event.sort AS poster_sort')
            ->addSelect('event.device AS poster_device')
            ->leftJoin(
                'poster',
                PosterEvent::class,
                'event',
                'event.id = poster.event',
            );

        $dbal->addSelect(
            'CASE 
            WHEN event.start <= CURRENT_TIMESTAMP 
                 AND (event.ended IS NULL OR event.ended >= CURRENT_TIMESTAMP)
            THEN TRUE 
            ELSE FALSE 
         END AS poster_active',
        );

        if($this->search && $this->search->getQuery())
        {
            $dbal
                ->andWhere('event.title ILIKE :search')
                ->setParameter('search', '%'.$this->search->getQuery().'%');
        }

        $dbal->orderBy('event.sort', 'ASC');

        return $this->paginator->fetchAllHydrate($dbal, AllPostersResult::class);
    }
}

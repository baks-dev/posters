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

namespace BaksDev\Posters\Repository\PosterCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Type\Event\PosterEventUid;
use InvalidArgumentException;


final class CurrentPosterEventRepository implements CurrentPosterEventInterface
{

    private PosterEventUid|false $event = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forEvent(PosterEvent|PosterEventUid|string $event): self
    {
        if(empty($event))
        {
            $this->event = false;
            return $this;
        }

        if(is_string($event))
        {
            $event = new PosterEventUid($event);
        }

        if($event instanceof PosterEvent)
        {
            $event = $event->getId();
        }

        $this->event = $event;

        return $this;
    }


    /**
     * Метод возвращает активное состояние сущности PosterEvent по идентификатору события
     */
    public function find(): PosterEvent|false
    {
        if(false === ($this->event instanceof PosterEventUid))
        {
            throw new InvalidArgumentException('Invalid Argument PosterEvent');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(PosterEvent::class, 'poster_event')
            ->where('poster_event.id = :event')
            ->setParameter(
                key: 'event',
                value: $this->event,
                type: PosterEventUid::TYPE,
            );

        $orm
            ->join(
                Poster::class,
                'poster',
                'WITH',
                'poster.id = poster_event.main',
            );

        $orm->select('current')
            ->join(
                PosterEvent::class,
                'current',
                'WITH',
                'current.id = poster.event',
            );

        return $orm->getQuery()->getOneOrNullResult() ?: false;
    }
}
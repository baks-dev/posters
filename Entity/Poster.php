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

namespace BaksDev\Posters\Entity;

use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\Type\Id\PosterUid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;


/* Poster */

#[ORM\Entity]
#[ORM\Table(name: 'poster')]
class Poster
{
    /**
     * Идентификатор сущности
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: PosterUid::TYPE)]
    private PosterUid $id;

    /**
     * Идентификатор события
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: PosterEventUid::TYPE, unique: true)]
    private PosterEventUid $event;

    public function __construct()
    {
        $this->id = new PosterUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * Идентификатор
     */
    public function getId(): PosterUid
    {
        return $this->id;
    }

    /**
     * Идентификатор события
     */
    public function getEvent(): PosterEventUid
    {
        return $this->event;
    }

    public function setEvent(PosterEventUid|PosterEvent $event): void
    {
        $this->event = $event instanceof PosterEvent ? $event->getId() : $event;
    }
}
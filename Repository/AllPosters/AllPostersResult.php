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

use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\Type\Id\PosterUid;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

/** @see AllPostersResult */
#[Exclude]
final class AllPostersResult
{
    public function __construct(

        private readonly string $id,
        private readonly string $event,
        private readonly string $poster_title,
        private readonly int $poster_sort,
        private readonly string $poster_device,
        private readonly ?string $profile_uid,
        private readonly bool $poster_active,

    ) {}

    public function getPosterDevice(): string
    {
        return $this->poster_device;
    }

    public function isPosterActive(): bool
    {
        return true === $this->poster_active;
    }

    public function getProfileUid(): ?PosterUid
    {
        return $this->profile_uid !== null ? new PosterUid($this->profile_uid) : null;
    }

    public function getPosterSort(): int
    {
        return $this->poster_sort;
    }

    public function getPosterTitle(): string
    {
        return $this->poster_title;
    }

    public function getId(): PosterUid
    {
        return new PosterUid($this->id);
    }

    public function getEvent(): PosterEventUid
    {
        return new PosterEventUid($this->event);
    }
}
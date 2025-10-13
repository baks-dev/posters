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

namespace BaksDev\Posters\UseCase\Admin\NewEdit;

use BaksDev\Posters\Entity\Event\PosterEventInterface;
use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\UseCase\Admin\NewEdit\Image\PosterImageDTO;
use BaksDev\Posters\UseCase\Admin\NewEdit\Profile\PosterProfileDTO;
use BaksDev\Posters\UseCase\Admin\NewEdit\Text\PosterTextDTO;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Callback('validateDates')]
/** @see PosterEventEvent */
final class PosterEventDTO implements PosterEventInterface
{

    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?PosterEventUid $id = null;

    private ArrayCollection $text;

    private PosterImageDTO $image;

    private ?DateTimeImmutable $start;

    private ?DateTimeImmutable $ended;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $title;

    #[Assert\NotBlank]
    private int $sort;

    #[Assert\Choice(['pc', 'tablet', 'mobile'])]
    #[Assert\NotBlank]
    private string $device = 'pc';

    private bool $public = false;

    private PosterProfileDTO $profile;


    /**
     * Идентификатор события
     */
    public function getEvent(): ?PosterEventUid
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->image = new PosterImageDTO();
        $this->text = new ArrayCollection();
        $this->profile = new PosterProfileDTO();
    }

    public function getText(): ArrayCollection
    {
        return $this->text;
    }

    public function getImage(): PosterImageDTO
    {
        return $this->image;
    }

    public function addText(PosterTextDTO $text): self
    {
        $this->text->add($text);
        return $this;
    }

    public function removeText(PosterTextDTO $text): self
    {
        $this->text->removeElement($text);
        return $this;

    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(DateTimeImmutable $start): self
    {
        $this->start = $start;
        return $this;
    }

    public function getEnded(): ?DateTimeImmutable
    {
        return $this->ended;
    }

    public function setEnded(?DateTimeImmutable $ended): self
    {
        $this->ended = $ended;
        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;
        return $this;
    }

    public function validateDates(ExecutionContextInterface $context): void
    {
        if($this->start && $this->ended && $this->start > $this->ended)
        {
            $context->buildViolation('Дата начала не может быть позже даты окончания.')
                ->atPath('start')
                ->addViolation();
        }
    }

    public function getProfile(): PosterProfileDTO
    {
        return $this->profile;
    }

    public function getPublic(): bool
    {
        return true === $this->public;
    }

    public function setPublic(bool $isPublic): void
    {
        $this->public = $isPublic;
    }

    public function setEntity(PosterEventInterface $entity): self
    {
        $this->public = $this->profile->getValue() === null;
        return $this;
    }

}
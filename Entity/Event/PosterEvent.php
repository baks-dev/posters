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

namespace BaksDev\Posters\Entity\Event;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Posters\Entity\Image\PosterImage;
use BaksDev\Posters\Entity\Modify\PosterModify;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Entity\Profile\PosterProfile;
use BaksDev\Posters\Entity\Text\PosterText;
use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\Type\Id\PosterUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* PosterEvent */

#[ORM\Entity]
#[ORM\Table(name: 'poster_event')]
class PosterEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: PosterEventUid::TYPE)]
    private PosterEventUid $id;

    /**
     * Идентификатор Poster
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: PosterUid::TYPE, nullable: false)]
    private ?PosterUid $main = null;

    /** One To One */
    #[ORM\OneToOne(targetEntity: PosterImage::class, mappedBy: 'event', cascade: ['all'])]
    private PosterImage $image;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: PosterModify::class, mappedBy: 'event', cascade: ['all'])]
    private PosterModify $modify;

    #[ORM\OneToMany(targetEntity: PosterText::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $text;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $start;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ended = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    private int $sort = 0;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\Choice(choices: ['pc', 'tablet', 'mobile'], message: 'Invalid device type')]
    private string $device = 'pc';

    #[ORM\OneToOne(targetEntity: PosterProfile::class, mappedBy: 'event', cascade: ['all'])]
    private PosterProfile $profile;


    public function __construct()
    {
        $this->id = new PosterEventUid();
        $this->modify = new PosterModify($this);
        $this->image = new PosterImage($this);
        $this->profile = new PosterProfile($this);
    }

    /**
     * Идентификатор события
     */

    public function __clone()
    {
        $this->id = clone new PosterEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): PosterEventUid
    {
        return $this->id;
    }

    /**
     * Идентификатор Poster
     */
    public function setMain(PosterUid|Poster $main): void
    {
        $this->main = $main instanceof Poster ? $main->getId() : $main;
    }

    public function getMain(): ?PosterUid
    {
        return $this->main;
    }

    public function getPosterImage(): PosterImage
    {
        return $this->image;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    public function getDto($dto): mixed
    {
        if(is_string($dto) && class_exists($dto))
        {
            $dto = new $dto();
        }


        if($dto instanceof PosterEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof PosterEventInterface)
        {

            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
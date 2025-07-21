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

namespace BaksDev\Posters\Entity\Text;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Type\Text\PosterTextUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Перевод PosterText */

#[ORM\Entity]
#[ORM\Table(name: 'poster_text')]
class PosterText extends EntityEvent
{
    /** Уникальный идентификатор */
    #[ORM\Id]
    #[ORM\Column(type: PosterTextUid::TYPE)]
    private PosterTextUid $id;

    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\ManyToOne(targetEntity: PosterEvent::class, inversedBy: "text")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    private PosterEvent $event;

    /** Текст */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING)]
    private string $text;

    /** Позиционирование */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING)]
    private string $position;

    /** Каскадные классы */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING)]
    private ?string $css = null;

    public function __construct(PosterEvent $event)
    {
        $this->id = new PosterTextUid();
        $this->event = $event;
    }

    public function __clone()
    {
        $this->id = clone new PosterTextUid();
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof PosterTextInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof PosterTextInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function name(Locale $locale): ?string
    {
        if($this->local->getLocalValue() === $locale->getLocalValue())
        {
            return $this->name;
        }

        return null;
    }
}

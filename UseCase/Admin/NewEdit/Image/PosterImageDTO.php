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

namespace BaksDev\Posters\UseCase\Admin\NewEdit\Image;

use BaksDev\Posters\Entity\Image\PosterImageInterface;
use BaksDev\Posters\Type\Event\PosterEventUid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\When;

final class PosterImageDTO implements PosterImageInterface
{
    /**
     * Файл изображения
     */
    #[When(
        expression: 'this.getName() === null',
        constraints: [
            new Assert\NotBlank(message: 'Загрузите изображение'),
            new Assert\File(
                maxSize: '8M',
                mimeTypes: [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'image/pjpeg',
                ],
                mimeTypesMessage: 'Please upload a valid image file',
            ),
        ]
    )]
    public ?File $file = null;

    private ?string $name = null;

    private ?string $ext = null;

    private ?bool $cdn = false;

    #[Assert\Uuid]
    private ?PosterEventUid $dir = null;

    /** Сущность для загрузки и обновления файла */

    private mixed $EntityUpload = 'null';


    /* NAME */

    public function getName(): ?string
    {
        return $this->name;
    }

    /* EXT */

    public function getExt(): ?string
    {
        return $this->ext;
    }

    /* CDN */

    public function getCdn(): bool
    {
        return $this->cdn;
    }

    /* DIR */

    public function getDir(): ?PosterEventUid
    {
        return $this->dir;
    }

    public function getEntityUpload(): mixed
    {
        return $this->EntityUpload;
    }

    public function setEntityUpload(mixed $EntityUpload): self
    {
        $this->EntityUpload = $EntityUpload;
        return $this;
    }
}
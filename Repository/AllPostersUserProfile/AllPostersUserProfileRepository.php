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

namespace BaksDev\Posters\Repository\AllPostersUserProfile;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Type\Device\Device;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Entity\Image\PosterImage;
use BaksDev\Posters\Entity\Profile\PosterProfile;
use BaksDev\Posters\Entity\Text\PosterText;
use BaksDev\Users\Profile\UserProfile\Entity\Discount\UserProfileDiscount;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;
use http\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


final class AllPostersUserProfileRepository implements AllPostersUserProfileInterface
{
    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        #[Autowire(env: 'PROJECT_PROFILE')] private readonly ?string $projectProfile = null,
    ) {}

    private Device $device;

    public function forDevice(Device|string $device): self
    {

        if(is_string($device))
        {
            $device = new Device($device);
        }

        $this->device = $device;

        return $this;
    }

    /** @return Generator<int, AllPostersUserProfileResult>|false */
    public function findAll(): Generator|false
    {
        if(false === ($this->device instanceof Device))
        {
            throw new InvalidArgumentException('Invalid Argument Device');
        }

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class);

        $dbal
            ->select('poster.id')
            ->addSelect('poster.event AS event')
            ->from(Poster::class, 'poster');

        $dbal
            ->leftJoin(
                'poster',
                PosterProfile::class,
                'project_profile',
                'project_profile.event = poster.event',
            );


        /** По умолчанию ищем только NULL  */
        $dbal->where('project_profile.value IS NULL');


        /** Получаем постеры профиля магазина */
        if($this->projectProfile)
        {
            $dbal
                ->where('project_profile.value IS NULL OR project_profile.value = :project')
                ->setParameter(
                    key: 'project',
                    value: new UserProfileUid($this->projectProfile),
                    type: UserProfileUid::TYPE,
                );
        }

        $dbal
            ->addSelect('event.title AS poster_title')
            ->addSelect('event.sort AS poster_sort')
            ->addSelect('event.start AS poster_start')
            ->addSelect('event.ended AS poster_end')
            ->join(
                'poster',
                PosterEvent::class,
                'event',
                '
                    event.id = poster.event AND
                    event.device = :device AND
                    event.start <= CURRENT_TIMESTAMP AND
                    (event.ended IS NULL OR event.ended >= CURRENT_TIMESTAMP)
                 ',
            )
            ->setParameter(
                key: 'device',
                value: $this->device,
                type: Device::TYPE,
            );

        $dbal
            ->addSelect(
                "JSON_AGG(
                    DISTINCT JSONB_BUILD_OBJECT(
                        'texts', poster_text.text,
                        'positions', poster_text.position,
                        'css', poster_text.css
                    )
                ) AS poster_text",
            )
            ->leftJoin(
                'poster',
                PosterText::class,
                'poster_text',
                'poster_text.event = poster.event',
            );

        //  /upload/poster_image/63e1e959-f346-7dd4-82ce-d87f2db3847e/image.png
        //  https://cdn.baks.dev/upload/poster_image/63e1e959-f346-7dd4-82ce-d87f2db3847e/medium.webp
        $dbal
            ->addSelect("CONCAT ('/upload/".$dbal->table(PosterImage::class)."' , '/', poster_image.name) AS poster_image_name")
            ->addSelect('poster_image.ext AS poster_image_ext')
            ->addSelect('poster_image.cdn AS poster_image_cdn')
            ->leftJoin(
                'poster',
                PosterImage::class,
                'poster_image',
                'poster_image.event = poster.event',
            );

        $dbal->allGroupByExclude();
        $dbal->orderBy('event.sort', 'ASC');

        $result = $dbal->fetchAllHydrate(AllPostersUserProfileResult::class);

        return (true === $result->valid()) ? $result : false;
    }
}

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

namespace BaksDev\Posters\UseCase\Admin\NewEdit\Tests;

use BaksDev\Core\BaksDevCoreBundle;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Type\Id\PosterUid;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterEventDTO;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterHandler;
use BaksDev\Posters\UseCase\Admin\NewEdit\Text\PosterTextDTO;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;


/**
 * @group posters
 */
#[When(env: 'test')]
#[Group('posters')]
class NewPosterTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(Poster::class)
            ->findOneBy(['id' => PosterUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(PosterEvent::class)
            ->findBy(['main' => PosterUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {
        /** @see PosterDTO */
        $PosterDTO = new PosterEventDTO();

        /** @var ContainerBagInterface $containerBag */
        $container = self::getContainer();
        $containerBag = $container->get(ContainerBagInterface::class);
        $fileSystem = $container->get(Filesystem::class);
        /** Создаем путь к тестовой директории */
        $testUploadDir = implode(DIRECTORY_SEPARATOR, [$containerBag->get('kernel.project_dir'), 'public', 'upload', 'tests']);

        /** Проверяем существование директории для тестовых картинок */
        if(false === is_dir($testUploadDir))
        {
            $fileSystem->mkdir($testUploadDir);
        }

        /**
         * Создаем тестовый файл загрузки Photo Collection
         */
        $fileSystem->copy(
            BaksDevCoreBundle::PATH.implode(
                DIRECTORY_SEPARATOR,
                ['Resources', 'assets', 'img', 'empty.webp'],
            ),
            $testUploadDir.DIRECTORY_SEPARATOR.'photo.webp',
        );

        $filePhoto = new File($testUploadDir.DIRECTORY_SEPARATOR.'photo.webp', false);


        $PosterImageDTO = $PosterDTO->getImage();
        $PosterImageDTO->file = $filePhoto;

        $PosterTextDTO = new PosterTextDTO();

        $PosterTextDTO
            ->setText('testText')
            ->setPosition('testPosition')
            ->setCss('test-class');

        $PosterDTO
            ->setStart(new DateTimeImmutable('2025-01-01 00:00:00'))
            ->setEnded(new DateTimeImmutable('2025-12-31 23:59:59'))
            ->setSort(5)
            ->setTitle('Test Title')
            ->setDevice('pc');

        $PosterDTO->addText($PosterTextDTO);
        self::assertCount(1, $PosterDTO->getText());
        self::assertContains($PosterTextDTO, $PosterDTO->getText());

        /** @var PosterHandler $PosterHandler */
        $PosterHandler = self::getContainer()->get(PosterHandler::class);
        $handle = $PosterHandler->handle($PosterDTO);

        self::assertTrue(($handle instanceof Poster), $handle.': Ошибка Poster');
    }

}
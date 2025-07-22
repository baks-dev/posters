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

namespace BaksDev\Posters\UseCase\Admin\Delete\Tests;


use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Posters\Entity\Event\PosterEvent;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Repository\PosterCurrentEvent\CurrentPosterEventInterface;
use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\Type\Id\PosterUid;
use BaksDev\Posters\UseCase\Admin\Delete\DeletePosterDTO;
use BaksDev\Posters\UseCase\Admin\Delete\DeletePosterHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Posters\UseCase\Admin\NewEdit\Tests\NewPosterTest;

/**
 * @group posters
 *
 * @depends BaksDev\Posters\UseCase\Admin\NewEdit\Tests\EditPosterTest::class
 */
#[When(env: 'test')]
class DeletePosterHandlerTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CurrentPosterEventInterface $PosterCurrentEvent */
        $PosterCurrentEvent = self::getContainer()->get(CurrentPosterEventInterface::class);
        $PosterEvent = $PosterCurrentEvent
            ->forEvent(PosterEventUid::TEST)
            ->find();

        self::assertNotNull($PosterEvent);

        /** @see PosterDeleteDTO */
        $PosterDeleteDTO = $PosterEvent->getDto(DeletePosterDTO::class);

        /** @var DeletePosterHandler $PosterDeleteHandler */
        $PosterDeleteHandler = self::getContainer()->get(DeletePosterHandler::class);
        $handle = $PosterDeleteHandler->handle($PosterDeleteDTO);

        self::assertTrue(($handle instanceof Poster), $handle.': Ошибка Poster');
    }

    public static function tearDownAfterClass(): void
    {
        NewPosterTest::setUpBeforeClass();
    }
}


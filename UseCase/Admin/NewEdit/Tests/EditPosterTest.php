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


use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\Repository\PosterCurrentEvent\CurrentPosterEventInterface;
use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\Type\Id\PosterUid;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterEventDTO;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterHandler;
use BaksDev\Posters\UseCase\Admin\NewEdit\Text\PosterTextDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Posters\UseCase\Admin\NewEdit\Tests\NewPosterTest;

/**
 * @group posters
 *
 * @depends BaksDev\Posters\UseCase\Admin\NewEdit\Tests\NewPosterTest::class
 */
#[When(env: 'test')]
class EditPosterTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        $PosterCurrentEvent = self::getContainer()->get(CurrentPosterEventInterface::class);

        /** @var CurrentPosterEventInterface $PosterCurrentEvent */
        $PosterEvent = $PosterCurrentEvent
            ->forEvent(PosterEventUid::TEST)
            ->find();

        self::assertNotNull($PosterEvent);

        /** @var PosterEventDTO $PosterEventDTO */
        $PosterEventDTO = $PosterEvent->getDto(PosterEventDTO::class);

        $PosterTextCollection = $PosterEventDTO->getText();
        $PosterTextDTO = $PosterTextCollection->current();

        self::assertInstanceOf(PosterTextDTO::class, $PosterTextDTO);

        self::assertEquals('testText', $PosterTextDTO->getText());

        $PosterTextDTO->setText('edit testText');

        self::assertEquals('testPosition', $PosterTextDTO->getPosition());

        $PosterTextDTO->setPosition('2');

        self::assertEquals('test-class', $PosterTextDTO->getCss());

        $PosterTextDTO->setCss('tests-class');


        /** @var PosterHandler $PosterHandler */

        $PosterHandler = self::getContainer()->get(PosterHandler::class);
        $handle = $PosterHandler->handle($PosterEventDTO);

        self::assertTrue(($handle instanceof Poster), $handle.': Ошибка Poster');

    }
}
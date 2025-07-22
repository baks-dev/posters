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

namespace BaksDev\Posters\Repository\AllPostersUserProfile\Tests;

use BaksDev\Core\Type\Device\Devices\Desktop;
use BaksDev\Posters\Repository\AllPostersUserProfile\AllPostersUserProfileInterface;
use BaksDev\Posters\Repository\AllPostersUserProfile\AllPostersUserProfileResult;
use BaksDev\Posters\Type\Event\PosterEventUid;
use BaksDev\Posters\Type\Id\PosterUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group posters-userprofile
 */
#[When(env: 'test')]
final class AllPostersUserProfileRepositoryTest extends KernelTestCase
{
    public function testFindAllReturnsGeneratorWhenDeviceSet(): void
    {
        /** @var AllPostersUserProfileInterface $AllPostersUserProfile */
        $AllPostersUserProfile = self::getContainer()->get(AllPostersUserProfileInterface::class);

        $generator = $AllPostersUserProfile
            ->forDevice(Desktop::class)
            ->findAll();

        if($generator === false || false === $generator->valid())
        {
            $this->assertFalse($generator);
            return;
        }

        $this->assertIsIterable($generator);

        foreach($generator as $item)
        {

            $this->assertInstanceOf(AllPostersUserProfileResult::class, $item);

            $this->assertInstanceOf(PosterUid::class, $item->getId());
            $this->assertInstanceOf(PosterEventUid::class, $item->getEvent());


            $this->assertNotEmpty($item->getImageUrl());
            $this->assertIsArray($item->getPosterText());

            $this->assertIsString($item->getPosterImageName());
            $this->assertIsString($item->getPosterImageExt());

            $this->assertIsInt($item->getPosterSort());
            $this->assertIsString($item->getPosterStart());
            $end = $item->getPosterEnd();

            $end ? $this->assertNull($end) : $this->assertIsString($end);


            $blocks = $item->getPosterText();

            if($blocks !== false)
            {
                $this->assertIsArray($blocks);

                foreach($blocks as $block)
                {
                    $this->assertArrayHasKey('texts', $block);
                    $this->assertArrayHasKey('positions', $block);
                    $this->assertArrayHasKey('css', $block);

                    $this->assertIsString($block['texts']);
                    $this->assertIsString($block['positions']);
                    $this->assertIsString($block['css']);
                }
            }
            break;
        }
    }
}

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

namespace BaksDev\Posters\Controller\Admin;


use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Posters\Entity\Poster;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterEventDTO;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterEventForm;
use BaksDev\Posters\UseCase\Admin\NewEdit\PosterHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_POSTER_NEW')]
final class NewController extends AbstractController
{
    #[Route('/admin/poster/new', name: 'admin.newedit.new', methods: ['GET', 'POST'])]
    public function news(
        Request $request,
        PosterHandler $PosterHandler
    ): Response
    {

        $PosterDTO = new PosterEventDTO();
        // Форма
        $form = $this
            ->createForm(
                type: PosterEventForm::class,
                data: $PosterDTO,
                options: ['action' => $this->generateUrl('posters:admin.newedit.new'),],
            )->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('poster_event'))
        {
            if(false === ($PosterDTO->getPublic()))
            {
                $PosterDTO->getProfile()->setValue($this->getProfileUid());
            }

            $handle = $PosterHandler->handle($PosterDTO);

            $this->addFlash
            (
                'page.new',
                $handle instanceof Poster ? 'success.new' : 'danger.new',
                'posters.admin',
                $handle,
            );

            return $handle instanceof Poster ? $this->redirectToRoute('posters:admin.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
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


use BaksDev\Posters\UseCase\Admin\NewEdit\Image\PosterImageForm;
use BaksDev\Posters\UseCase\Admin\NewEdit\Text\PosterTextForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class PosterEventForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('image',
            PosterImageForm::class,
            [
                'required' => true,
                'constraints' => [
                    new Valid(),
                ],
            ],
        );

        $builder->add('text',
            CollectionType::class,
            [
                'entry_type' => PosterTextForm::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'error_bubbling' => false,
                'constraints' => [
                    new Valid(),
                ],
            ],
        );

        $builder->add(
            'start',
            DateType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
            'required' => true,
            'format' => 'dd.MM.yyyy',
            'input' => 'datetime_immutable',
        ]);

        $builder->add(
            'ended',
            DateType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
            'required' => true,
            'format' => 'dd.MM.yyyy',
            'input' => 'datetime_immutable',
        ]);


        $builder->add('sort',
            IntegerType::class, [
                'label' => false,
                'required' => true,
            ],
        );

        $builder->add('title',
            TextType::class, [
                'label' => false,
                'required' => true,
            ],
        );

        $builder->add('device', ChoiceType::class, [
            'choices' => [
                'ПК версия' => 'pc',
                'Планшет' => 'tablet',
                'Мобильная версия' => 'mobile',
            ],
            'expanded' => true,
            'multiple' => false,
            'label' => false,
            'required' => true,
        ]);

        $builder->add('public', CheckboxType::class, [
            'required' => false,
            'label' => 'Общий постер',
        ]);

        /* Сохранить ******************************************************/
        $builder->add(
            'poster_event',
            SubmitType::class, [
                'label' => 'Save',
                'label_html' => true,
                'attr' => ['class' => 'btn-primary'],
            ],
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PosterEventDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}
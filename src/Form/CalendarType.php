<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('start', DateTimeType::class, [
                'date_widget' => 'single_text'
            ])
            ->add('end', DateTimeType::class, [
                'date_widget' => 'single_text'
            ])
            ->add('description')
            ->add('all_day')
            ->add('background_color', ColorType::class, [
                'data' => '#3788d8'
            ])
            ->add('border_color', ColorType::class, [
                'data' => '#3788d8'
            ])
            ->add('text_color', ColorType::class, [
                'data' => '#fff'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}

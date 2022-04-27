<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Brand name',
                    'required' => true,
                    'attr' => [
                        'minlength' => 5,
                        'maxlength' => 30
                    ]
                ]
            )
            ->add(
                'owner',
                TextType::class,
                [
                    'label' => 'Owner name',
                    'required' => true,
                    'attr' => [
                        'minlength' => 5,
                        'maxlength' => 30
                    ]
                ]
            )
            ->add(
                'value',
                MoneyType::class,
                [
                    'label' => 'Book Price',
                    'required' => true,
                ]
            )
            ->add(
                'image',
                TextType::class,
                [
                    'label' => 'Genre image',
                    'required' => true
                ]
            )
            ->add(
                'rate',
                MoneyType::class,
                [
                    'label' => 'Book Price',
                    'required' => true,
                ]
            )
            ->add(
                'country',
                EntityType::class,
                [
                    'label' => 'Country name',
                    'required' => true,
                    'class' => Country::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false
                ]
            )
            ->add('Save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' =>'Title of Product',
                'required'=> true
            ])
            ->add('price',MoneyType::class,[
                'required'=> true,
                'currency' => 'VND'
                
            ])
            ->add('image', TextType::class)
            ->add('quantity',IntegerType::class,[
                'label' => 'Quantity of Product',
                'required'=> true,
                'attr' => [
                    'min' => 1,
                    'max' => 100
                ]
            ])
            ->add('color', TextType::class)
            ->add('size',ChoiceType::class,[
                'label' => 'Size of Product',
                'required'=> true,
                'choices'=>[
                    'M'=>'M',
                    'X'=>'X',
                    'XL'=>'XL',
                    'XXL'=>'XXL'
                ],
                'expanded' =>false

            ])
            ->add('category',EntityType::class,[
                'label' => 'Category of Product',
                'class' => Category::class,
                'choice_label'=>'name',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('Save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

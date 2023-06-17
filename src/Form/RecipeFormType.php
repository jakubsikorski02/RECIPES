<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Enter title'
                ),
                'required' => false,
                'label' => false
            ])
            ->add('ingridients', TextType::class, [
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Enter ingridients'
                ),
                'required' => false,
                'label' => false
            ])
            ->add('howTo', TextareaType::class, [
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Enter how to'
                ),
                'required' => false,
                'label' => false
            ])
            ->add('imagePath', FileType::class, array(
                'required' => false,
                'mapped' => false,
                'label' => false
            ))
        //    ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}



// ->add('ingridients', CollectionType::class, [
//     'entry_type' => TextType::class,
//     'allow_add' => true,
//     'attr' => [
//         'class' => 'form-control',
//         'placeholder' => 'Enter ingredient'
//     ],
//     'required' => false,
//     'label' => false,
// ])
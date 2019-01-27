<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Video;
use App\Manager\CategoryManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{

    private $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $entityManager = $options['entity_manager'];

        $builder
            ->add('title', TextType::class)
            ->add('url', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'expanded' => true,
            ])
            ->add('published', CheckboxType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
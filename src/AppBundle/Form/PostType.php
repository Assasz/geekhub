<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, ['attr' => [
            'class' => 'form-control'
          ]])
        ->add('category', EntityType::class, [
          'attr' => [
            'class' => 'form-control'
          ],
          'class' => 'AppBundle:Category',
          'choice_label' => 'name',
          'placeholder' => 'Select category'
        ])
        ->add('tags', TextType::class, ['attr' => [
          'class' => 'form-control'
        ]])
        ->add('content', TextareaType::class, ['attr' => [
          'class' => 'form-control',
          'rows' => 10
        ]])
        ->add('add_post', SubmitType::class, ['label' => 'Add post', 'attr' => [
          'class' => 'btn btn-primary'
        ]]);
    }
}
?>

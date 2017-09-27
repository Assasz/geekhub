<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', TextareaType::class, ['attr' => [
          'class' => 'form-control',
          'rows' => 5
        ]])
        ->add('add_comment', SubmitType::class, ['label' => 'Add comment', 'attr' => [
          'class' => 'btn btn-primary',
          'disabled' => 'disabled'
        ]]);
    }
}

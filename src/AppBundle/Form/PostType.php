<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, ['attr' => [
            'class' => 'form-control'
        ]])
        ->add('tags', TextType::class, [
            'attr' => [
                'class' => 'form-control tokenfield',
                'placeholder' => 'Type tag and hit space or enter to add, up to 5 tags'
            ],
            'mapped' => false
        ])
        ->add('content', CKEditorType::class, [
            'attr' => [
                'class' => 'form-control',
                'rows' => 10
            ],
            'config_name' => 'standard_config'
        ])
        ->add('add_post', SubmitType::class, [
            'label' => 'Add post',
            'attr' => [
                'class' => 'btn btn-primary'
        ]]);
    }
}
?>

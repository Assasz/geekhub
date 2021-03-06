<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, ['attr' => [
            'class' => 'form-control',
            'autofocus' => 'autofocus',
            'placeholder' => 'Min 3 characters, max 255.'
        ]])
        ->add('image', FileType::class, [
            'label' => 'Post image'
        ])
        ->add('tags', TextType::class, [
            'attr' => [
                'class' => 'form-control tokenfield',
                'placeholder' => 'Type tag and hit space or enter to add. Up to 5 tags.'
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
                'class' => 'btn btn-lg btn-primary pull-left',
                'style' => 'margin-top: 15px; margin-right: 20px;'
        ]])
        ->add('preview_post', ButtonType::class, [
            'label' => 'Preview',
            'attr' => [
                'class' => 'btn btn-lg btn-default pull-left',
                'style' => 'margin-top: 15px;',
                'data-action' => 'post-preview'
        ]]);
    }
}
?>

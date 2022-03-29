<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label'=>'Email address',
                'attr'=>['class'=>'form-control mb-3','placeholder'=>'youremailaddress@gmail.com']
            ])
            ->add('password',RepeatedType::class,[
                'type'=>PasswordType::class,
                'first_name'=>'Password',
                'second_name'=>'RepeatPassword',
                'first_options'=>['label'=>'Password','attr'=>['placeholder'=>'Password','class'=>'form-control mb-3']],
                'second_options'=>['label'=>'Repeat password','attr'=>['placeholder'=>'Repeat password','class'=>'form-control mb-3']]
            ])
            ->add('username',TextType::class,[
                'label'=>'Username',
                'attr'=>['class'=>'form-control mb-3','placeholder'=>'John67']
            ])
            ->add('submit',SubmitType::class,[
                'attr'=>['class'=>'btn btn-success w-100','value'=>'Submit']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

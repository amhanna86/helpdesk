<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, [
            'label' => 'user.entity.mail',
            'constraints' => [
                new NotBlank(),
                new Email()
            ]
        ]);

        if ($options['edit'] === false){
            $builder->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ]);
        }


        $builder->add('firstName', TextType::class, [
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add('lastName', TextType::class, [
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add('phone', IntegerType::class);
        $builder->add('company', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'edit' => false
        ]);
    }
}

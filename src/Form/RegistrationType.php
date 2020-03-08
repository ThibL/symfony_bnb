<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, $this->getConfig("Prénom :", "Votre prénom"))
            ->add('lastName', TextType::class, $this->getConfig("Nom :", "Votre nom de famille"))
            ->add('email', EmailType::class, $this->getConfig("Email :", "Votre adresse email"))
            ->add('picture', UrlType::class, $this->getConfig("Avatar :", "Entrez l'url de votre avatar"))
            ->add('hash', PasswordType::class, $this->getConfig("Mot de passe :", "Entrez votre mot de passe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfig("Confirmation du mot de passe :", "Confirmez votre mot de passe"))
            ->add('introduction', TextType::class, $this->getConfig("Introduction :", "Présentez-vous en quelques mots"))
            ->add('description', TextareaType::class, $this->getConfig("Description :", "Entrez une description détaillée"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',
                TextType::class, $this->getConfig("Titre", "Entrez le titre de votre annonce"))
            ->add('slug',
                TextType::class, ['required' => false], $this->getConfig("Adresse web", "Adresse web (automatique)"))
            ->add('coverImage',
                UrlType::class, $this->getConfig("Url de l'image", "Entrez l'url de votre image de couverture"))
            ->add('introduction',
                TextType::class, $this->getConfig("Introduction", "Donnez une courte description"))
            ->add('content',
                TextareaType::class, $this->getConfig("Contenu de l'annonce", "Description détaillée"))
            ->add('rooms',
                IntegerType::class, $this->getConfig("Nombre de chambres", "Le nombre de chambres disponibles"))
            ->add('price',
                MoneyType::class, $this->getConfig("Prix par nuit", "indiquez le prix pour une nuit"))
            ->add('images',
                CollectionType::class, [
                    'entry_type' => ImageType::class,
                    "allow_add" => true,
                    'allow_delete' => true
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}

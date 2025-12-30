<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\UserAddress;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use QueryBuilder;

class UserAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressLine1')
            ->add('city')
            ->add('postalCode')
            /* On ajoute le champ country en utilisant EntityType.
               Cela créera automatiquement un <select> HTML.
            */
            ->add('country', EntityType::class, [
                'class' => Country::class,
                // Quelle propriété afficher dans la liste ?
                'choice_label' => 'name',

                // Optionnel : Trier les pays par nom alphabétique
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },

                'label' => 'Pays',
                'placeholder' => 'Choisissez un pays',
                'attr' => ['class' => 'form-select'] // Si tu utilises Bootstrap ou Tailwind
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAddress::class,
        ]);
    }
}

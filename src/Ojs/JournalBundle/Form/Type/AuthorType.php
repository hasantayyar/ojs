<?php

namespace Ojs\JournalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations')
            ->add('middleName')
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('firstNameTransliterated')
            ->add('middleNameTransliterated')
            ->add('lastNameTransliterated')
            ->add('initials')
            ->add('email', 'email')
            ->add('address', 'textarea')
            ->add('publisher', null, [
                'attr' => [
                    'class' => 'publisher'
                ]
            ])
            ->add('publisherNotListed', null, [
                'attr' => [
                    'class' => 'publisherNotListed'
                ]
            ])
            ->add('publisherName', null, [
                'attr' => [
                    'class' => 'publisherName'
                ]
            ])
            ->add('country')
            ->add('authorDetails', 'textarea');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Ojs\JournalBundle\Entity\Author',
                'cascade_validation' => true,
                'attr' => [
                    'class' => 'form-validate',
                ],
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ojs_journalbundle_author';
    }
}

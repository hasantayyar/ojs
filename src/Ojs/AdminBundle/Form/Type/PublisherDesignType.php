<?php

namespace Ojs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublisherDesignType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'publisher',
                'entity',
                array(
                    'label' => 'publisher',
                    'class' => 'Ojs\JournalBundle\Entity\Publisher',
                    'property' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                    'error_bubbling' => true,
                )
            )
            ->add(
                'title',
                'text',
                [
                    'label' => 'Title',
                ]
            )
            ->add('editableContent', 'hidden')
            ->add(
                'isPublic',
                'checkbox',
                [
                    'label' => 'ojs.is_public',
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Ojs\JournalBundle\Entity\PublisherDesign',
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
        return 'ojs_adminbundle_publisher_design';
    }
}

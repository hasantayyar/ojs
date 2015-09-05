<?php

namespace Ojs\JournalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SubmissionChecklistType.
 */
class SubmissionChecklistType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', ['label' => 'submission_checklist.label'])
            ->add('detail', 'textarea', ['label' => 'submission_checklist.detail'])
            ->add(
                'locale',
                'choice',
                [
                    'choices' => $options['languages'],
                ]
            )
            ->add('visible', 'checkbox', ['label' => 'submission_checklist.visible']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Ojs\JournalBundle\Entity\SubmissionChecklist',
                'cascade_validation' => true,
                'languages' => array(
                    array('tr' => 'Türkçe'),
                    array('en' => 'English'),
                ),
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
        return 'ojs_journalbundle_submissionchecklist';
    }
}

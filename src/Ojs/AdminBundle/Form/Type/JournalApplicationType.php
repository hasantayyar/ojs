<?php

namespace Ojs\AdminBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ojs\JournalBundle\Form\Type\JournalContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalApplicationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations')
            ->add('titleAbbr', null, ['label' => 'journal.titleAbbr', 'attr' => ['class' => 'validate[required]']])
            ->add('titleTransliterated', null, ['label' => 'journal.titleTransliterated', 'attr' => ['class' => 'validate[required]']])
            ->add('domain', null, ['label' => 'journal.domain', 'attr' => ['class' => 'validate[required]']])
            ->add(
                'country',
                'entity',
                array(
                    'class' => 'OjsLocationBundle:Country',
                    'label' => 'journal.country',
                    'attr' => ['class' => 'select2-element validate[required]'],
                )
            )
            ->add('issn', null, ['label' => 'journal.issn', 'attr' => ['class' => 'validate[required] maskissn']])
            ->add('eissn', null, ['label' => 'journal.eissn', 'attr' => ['class' => 'validate[required] maskissn']])
            ->add(
                'founded',
                'collot_datetime',
                [
                    'label' => 'journal.founded',
                    'attr' => ['class' => 'validate[required]'],
                    'date_format' => 'dd-MM-yyyy',
                    'pickerOptions' => [
                        'format' => 'dd-mm-yyyy',
                        'startView' => 'month',
                        'minView' => 'month',
                        'todayBtn' => 'true',
                        'todayHighlight' => 'true',
                        'autoclose' => 'true',
                    ],
                ]
            )
            ->add(
                'mandatoryLang',
                'entity',
                [
                    'label' => 'Mandatory Lang',
                    'class' => 'Ojs\JournalBundle\Entity\Lang',
                    'attr' => [
                        'class' => 'select2-element ',
                    ]
                ]
            )
            ->add(
                'periods',
                'entity',
                array(
                    'label' => 'Periods',
                    'class' => 'Ojs\JournalBundle\Entity\Period',
                    'property' => 'period',
                    'multiple' => true,
                    'expanded' => false,
                    'attr' => [
                        'class' => 'select2-element validate[required]',
                    ]
                )
            )
            ->add('tags', 'tags', ['attr' => ['class' => 'validate[required]', 'label' => 'journal.tags']])
            ->add('url', 'url', ['label' => 'journal.url', 'attr' => ['class' => 'validate[required]']])
            ->add(
                'publisher',
                'entity',
                array(
                    'class' => 'OjsJournalBundle:Publisher',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                    ->andWhere('i.status = :status')
                                    ->setParameter('status', 1);
                    },
                    'attr' => ['class' => 'select2-element validate[required]'],
                    'label' => 'journal.publisher',
                )
            )
            ->add(
                'languages',
                'entity',
                array(
                    'class' => 'OjsJournalBundle:Lang',
                    'multiple' => true,
                    'label' => 'journal.languages',
                    'attr' => ['class' => 'select2-element validate[required]'],
                )
            )
            ->add(
                'subjects',
                'entity',
                array(
                    'class' => 'OjsJournalBundle:Subject',
                    'multiple' => true,
                    'required' => false,
                    'label' => 'journal.subjects',
                    'attr' => ['class' => 'select2-element'],
                )
            )
            ->add('header', 'jb_crop_image_ajax', array(
                'endpoint' => 'journal',
                'label' => 'Header Image',
                'img_width' => 960,
                'img_height' => 200,
                'crop_options' => array(
                    'aspect-ratio' => 960 / 200,
                    'maxSize' => "[960, 200]"
                )
            ))
            ->add('image', 'jb_crop_image_ajax', array(
                'endpoint' => 'journal',
                'label' => 'Cover Image',
                'img_width' => 200,
                'img_height' => 300,
                'crop_options' => array(
                    'aspect-ratio' => 200 / 300,
                    'maxSize' => "[200, 300]"
                )
            ))
            ->add('address', null, ['label' => 'journal.address', 'attr' => ['class' => 'validate[required]']])
            ->add('phone', null, ['label' => 'journal.phone', 'attr' => ['class' => 'validate[required,custom[email]]']])
            ->add('email', 'email', ['label' => 'journal.email', 'attr' => ['class' => 'validate[required,custom[email]]']])
            ->add('journalContacts', 'collection', ['type' => new JournalContactType(), 'allow_add' => true]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Ojs\JournalBundle\Entity\Journal',
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
        return 'ojs_journalbundle_journalapplication';
    }
}

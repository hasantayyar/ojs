<?php

namespace Ojs\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnouncementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('image', 'jb_crop_image_ajax', array(
                'endpoint' => 'announcement',
                'img_width' => 128,
                'img_height' => 128,
                'crop_options' => array(
                    'aspect-ratio' => 128 / 128,
                    'maxSize' => '[128, 128]',
                ),
            ))
            ->add('content', 'ace_editor', array(
                    'wrapper_attr' => array(),
                    'width' => 700,
                    'height' => 200,
                    'font_size' => 12,
                    'mode' => 'ace/mode/html',
                    'theme' => 'ace/theme/chrome',
                    'tab_size' => null,
                    'read_only' => null,
                    'use_soft_tabs' => null,
                    'use_wrap_mode' => null,
                    'show_print_margin' => null,
                    'highlight_active_line' => null,
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Ojs\CmsBundle\Entity\Announcement',
            'cascade_validation' => true,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ojs_cms_announcement';
    }
}

<?php

namespace MauticPlugin\MauticFocusBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<string, mixed>>
 */
class PropertiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'bar',
            FocusPropertiesType::class,
            [
                'focus_style' => 'bar',
                'data'        => $options['data']['bar'] ?? [],
            ]
        );

        $builder->add(
            'modal',
            FocusPropertiesType::class,
            [
                'focus_style' => 'modal',
                'data'        => $options['data']['modal'] ?? [],
            ]
        );

        $builder->add(
            'notification',
            FocusPropertiesType::class,
            [
                'focus_style' => 'notification',
                'data'        => $options['data']['notification'] ?? [],
            ]
        );

        $builder->add(
            'page',
            FocusPropertiesType::class,
            [
                'focus_style' => 'page',
                'data'        => $options['data']['page'] ?? [],
            ]
        );

        $builder->add(
            'animate',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.focus.form.animate',
                'data'  => $options['data']['animate'] ?? true,
                'attr'  => [
                    'onchange' => 'Mautic.focusUpdatePreview()',
                ],
            ]
        );

        $builder->add(
            'link_activation',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.focus.form.activate_for_links',
                'data'  => $options['data']['link_activation'] ?? true,
                'attr'  => [
                    'data-show-on' => '{"focus_properties_when": ["leave"]}',
                ],
            ]
        );

        $builder->add(
            'colors',
            ColorType::class,
            [
                'label' => false,
            ]
        );

        $builder->add(
            'content',
            ContentType::class,
            [
                'label' => false,
            ]
        );

        $builder->add(
            'when',
            ChoiceType::class,
            [
                'choices'           => [
                    'mautic.focus.form.when.immediately'   => 'immediately',
                    'mautic.focus.form.when.scroll_slight' => 'scroll_slight',
                    'mautic.focus.form.when.scroll_middle' => 'scroll_middle',
                    'mautic.focus.form.when.scroll_bottom' => 'scroll_bottom',
                    'mautic.focus.form.when.leave'         => 'leave',
                ],
                'label'       => 'mautic.focus.form.when',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'expanded'    => false,
                'multiple'    => false,
                'required'    => false,
                'placeholder' => false,
            ]
        );

        $builder->add(
            'timeout',
            TextType::class,
            [
                'label'      => 'mautic.focus.form.timeout',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'          => 'form-control',
                    'postaddon_text' => 'sec',
                ],
                'required' => false,
            ]
        );

        $builder->add(
            'frequency',
            ChoiceType::class,
            [
                'choices'           => [
                    'mautic.focus.form.frequency.everypage' => 'everypage',
                    'mautic.focus.form.frequency.once'      => 'once',
                    'mautic.focus.form.frequency.q2m'       => 'q2min',
                    'mautic.focus.form.frequency.q15m'      => 'q15min',
                    'mautic.focus.form.frequency.hourly'    => 'hourly',
                    'mautic.focus.form.frequency.daily'     => 'daily',
                ],
                'label'       => 'mautic.focus.form.frequency',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'expanded'    => false,
                'multiple'    => false,
                'required'    => false,
                'placeholder' => false,
            ]
        );

        $builder->add(
            'stop_after_conversion',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.focus.form.engage_after_conversion',
                'data'  => $options['data']['stop_after_conversion'] ?? true,
                'attr'  => [
                    'tooltip' => 'mautic.focus.form.engage_after_conversion.tooltip',
                ],
            ]
        );

        $builder->add(
            'stop_after_close',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.focus.form.stop_after_close',
                'data'  => (isset($options['data']['stop_after_close'])) ? $options['data']['stop_after_close'] : false,
                'attr'  => [
                    'tooltip' => 'mautic.focus.form.stop_after_close.tooltip',
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'focus_entity_properties';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'label' => false,
            ]
        );
    }
}

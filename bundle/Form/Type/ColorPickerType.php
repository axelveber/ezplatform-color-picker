<?php
declare(strict_types=1);

namespace Codein\eZColorPicker\Form\Type;

use Codein\eZColorPicker\FieldType\ColorPicker\Value as ColorPickerValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ColorPickerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('RGBa', HiddenType::class);
        $builder->add('HEXa', HiddenType::class);
        $builder->add('HSVa', HiddenType::class);
        $builder->add('RGB', HiddenType::class);
        $builder->add('HEX', HiddenType::class);

        if(is_array($options['defaultValue'])) {
            $defaultValue = $options['defaultValue'];
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($defaultValue) {
                /** @var ColorPickerValue $colorPickerValue */
                $colorPickerValue = $event->getData();
                $defaultValue = (new ColorPickerValue())->setValueFromArray($defaultValue);
                if($colorPickerValue->isEmpty() && !$defaultValue->isEmpty()) {
                    $event->setData($defaultValue);
                }
            });
        }

        if($options['useViewTransformer']) {
            $builder->addViewTransformer(new CallbackTransformer(
                function ($value) {
                    $colorPickerValue = new ColorPickerValue();
                    if(is_array($value)) {
                        $colorPickerValue->setValueFromArray($value);
                    }
                    return $colorPickerValue;
                },
                function ($value) {
                    if ($value instanceof ColorPickerValue) {
                        return $value->getValueAsArray();
                    }
                    $colorPickerValue = new ColorPickerValue();
                    return $colorPickerValue->getValueAsArray();
                }
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ColorPickerValue::class,
            'useViewTransformer' => false,
            'defaultValue' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'codeincolor';
    }
}
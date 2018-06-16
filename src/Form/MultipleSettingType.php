<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 16/06/2018
 * Time: 08:51
 */
namespace App\Form;

use App\Entity\Setting;
use App\Form\Transformer\SettingValueTransformer;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MultipleSettingType
 * @package App\Form
 */
class MultipleSettingType extends AbstractType
{
    /**
     * buildForm
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $key = trim($options['property_path'], '[]');
        $data = null;
        if ($key !== "") {
            $data = $options['all_data'][$key];
            $attr = [];
            switch ($data->getType()) {
                case 'array':
                    $formType = TextareaType::class;
                    $attr = ['rows' => '5',];
                    break;
                default:
                    $formType = TextType::class;
            }
            $builder
                ->add('value', $formType,
                    [
                        'label' => $data->getDisplayName(),
                        'help' => $data->getDescription(),
                        'translation_domain' => empty($data->getTranslateChoice()) ? 'Setting' : $data->getTranslateChoice(),
                        'attr' => $attr,
                        'required' => false,
                        'constraints' => $data->getValidators(),
                    ]
                )
            ;
        } else
            $builder
                ->add('value', TextType::class);

        $builder->get('value')->addViewTransformer(new SettingValueTransformer($data ? $data->getType() : 'string'));
    }

    /**
     * getBlockPrefix
     *
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'multiple_setting';
    }

    /**
     * configureOptions
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'all_data',
            ]
        );
        $resolver->setDefaults(
            [
                'translation_domain' => 'Setting',
                'data_class' => Setting::class,
            ]
        );
    }
}
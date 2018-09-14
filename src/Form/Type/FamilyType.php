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
 * Date: 14/09/2018
 * Time: 17:30
 */
namespace App\Form\Type;

use App\Entity\Family;
use Hillrange\Form\Type\EnumType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FamilyType
 * @package App\Form\Type
 */
class FamilyType extends AbstractType
{
    /**
     * buildForm
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'family.name.label',
                ]
            )
            ->add('nameAddress', TextType::class,
                [
                    'label' => 'family.name_address.label',
                    'help' => 'family.name_address.help',
                ]
            )
            ->add('status', EnumType::class,
                [
                    'label' => 'family.status.label',
                ]
            )
/*            ->add('careGivers', CollectionType::class, array(
                    'label'        => 'family.caregivers.label',
                    'entry_type'   => CareGiverType::class,
                    'allow_add'    => true,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'attr'         => array(
                        'class' => 'careGiverList',
                        'help'  => 'family.caregivers.help',
                    ),
                    'required'     => false,
                )
            )
            ->add('students', CollectionType::class, array(
                    'label'        => 'family.label.students',
                    'entry_type'   => StudentType::class,
                    'allow_add'    => true,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'attr'         => array(
                        'class' => 'studentList',
                        'help'  => 'family.help.students',
                    ),
                    'required'     => false,
                )
            )  */
            ->add('languageHomePrimary', LanguageType::class,
                [
                    'label'       => 'family.language_home_primary.label',
                    'placeholder' => 'family.language_home_primary.placeholder.e',
                    'required'    => false,
                    'preferred_choices' => ['en','yue','zh'],
                ]
            )
            ->add('languageHomeSecondary', LanguageType::class,
                [
                    'label'       => 'family.language_home_secondary.label',
                    'placeholder' => 'family.language_home_secondary.placeholder',
                    'required'    => false,
                    'preferred_choices' => ['en','yue','zh'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Family::class,
                'translation_domain' => 'Person',
            ]
        );
    }
}
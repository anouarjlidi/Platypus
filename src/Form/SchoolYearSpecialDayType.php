<?php
namespace App\Form;

use App\Entity\SchoolYear;
use App\Entity\SchoolYearSpecialDay;
use Hillrange\Form\Type\DateType;
use Hillrange\Form\Type\EnumType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;

class SchoolYearSpecialDayType extends AbstractType
{
	/**
	 * @var    EntityManagerInterface
	 */
	private $manager;

	/**
	 * Construct
	 */
	public function __construct(EntityManagerInterface $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$key  = isset($options['property_path']) ? str_replace(array('[', ']'), '', $options['property_path']) : '__name__';
		$calendar = $options['schoolYear'];
		if (is_null($calendar->getFirstDay()))
			$calendars = array(date('Y'));
		else
			$calendars = range($calendar->getFirstDay()->format('Y'), $calendar->getLastDay()->format('Y'));
		$builder
			->add('date', DateType::class,
				array(
					'label' => 'special_day.date.label',
					'help' => 'special_day.date.help',
					'years' => $calendars,
					'attr' => [
						'class' => 'small'
					],
				)
			)
			->add('type', EnumType::class,
				[
					'label' => 'special_day.type.label',
                    'help'  => 'special_day.type.help',
					'attr'  => [
						'class' => 'alterType form-control-sm',
					],
				]
			)
			->add('name', TextType::class,
				array(
					'label' => 'special_day.name.label',
					'help' => 'special_day.name.help',
				)
			)
			->add('description', TextareaType::class,
				[
					'label'    => 'special_day.description.label',
					'attr'     => [
						'rows' => '3',
                        'class' => 'form-control-sm',
					],
					'help' => 'special_day.description.help',
					'required' => false,
				]
			)
			->add('schoolOpen', TimeType::class,
				array(
					'label'       => 'special_day.open.label',
					'attr'        => array(
						'class' => 'alterTime small',
					),
					'help'  => 'special_day.open.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('schoolStart', TimeType::class,
				array(
					'label'       => 'special_day.start.label',
					'attr'        => array(
						'class' => 'alterTime small',
					),
					'help'  => 'special_day.start.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('schoolEnd', TimeType::class,
				array(
					'label'       => 'special_day.end.label',
					'attr'        => array(
						'class' => 'alterTime small',
					),
					'help'  => 'special_day.end.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('schoolClose', TimeType::class,
				array(
					'label'       => 'special_day.close.label',
					'attr'        => array(
						'class' => 'alterTime small',
					),
					'help'  => 'special_day.close.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('schoolYear', HiddenEntityType::class,
				[
					'class' => SchoolYear::class,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => SchoolYearSpecialDay::class,
				'translation_domain' => 'SchoolYear',
			)
		);
		$resolver->setRequired(
		    [
		        'schoolYear',
            ]
        );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'school_year_special_day';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->getBlockPrefix();
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['schoolYear'] = $options['schoolYear'];
	}
}

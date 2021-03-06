<?php
namespace App\Validator\Constraints;

use App\Entity\SchoolYearTerm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TermDateValidator extends ConstraintValidator
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * TermDateValidator constructor.
	 *
	 * @param EntityManagerInterface $objectManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 *
	 * @return mixed|void
	 */
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$firstDay = $constraint->schoolYear->getFirstDay();
		$lastDay  = $constraint->schoolYear->getLastDay();

		$terms = $this->entityManager->getRepository(SchoolYearTerm::class);
		$terms = $terms->findBy(['schoolYear' => $constraint->schoolYear->getId()], ['firstDay' => 'ASC']);

		if (!empty($terms))
			foreach ($terms as $t)
			{
				if (!$value->contains($t))
					if (!$t->canDelete())
					{
						$this->context->buildViolation('year.term.error.delete', ['%term%' => $t->getName()])
							->addViolation();

						return;
					}
			}

		foreach ($value as $key => $term)
		{
			if (empty($term->getName()) || empty($term->getNameShort()))
			{
				$value->remove($key);
				$constraint->schoolYear->removeTerm($term);
			}
		}

		$terms = array();
		foreach ($value as $term)
		{
			if (!($term->getFirstDay() instanceof \DateTime && $term->getLastDay() instanceof \DateTime))
			{
				$this->context->buildViolation('year.term.error.invalid')
                    ->setTranslationDomain('SchoolYear')
					->addViolation();

				return;
			}
			if ($term->getFirstDay() > $term->getLastDay())
			{
				$this->context->buildViolation('year.term.error.order')
                    ->setTranslationDomain('SchoolYear')
					->addViolation();

				return;
			}
			if ($term->getFirstDay() < $firstDay)
			{
				$this->context->buildViolation('year.term.error.outsideYear', array('%term_date%' => $term->getFirstDay()->format('jS M Y'), '%year_date%' => $firstDay->format('jS M Y'), '%operator%' => '<'))
					->addViolation();

				return;
			}
			if ($term->getLastDay() > $lastDay)
			{
				$this->context->buildViolation('year.term.error.outsideYear', array('%term_date%' => $term->getLastDay()->format('jS M Y'), '%year_date%' => $lastDay->format('jS M Y'), '%operator%' => '>'))
					->addViolation();

				return;
			}
			foreach ($terms as $name => $test)
			{
				if ($term->getFirstDay() >= $test['start'] && $term->getFirstDay() <= $test['end'])
				{
					$this->context->buildViolation('year.term.error.overlapped', array('%name1%' => $name, '%name2%' => $term->getName()))
						->addViolation();

					return;
				}
				if ($term->getLastDay() >= $test['start'] && $term->getLastDay() <= $test['end'])
				{
					$this->context->buildViolation('year.term.error.overlapped', array('%name1%' => $name, '%name2%' => $term->getName()))
						->addViolation();

					return;
				}
			}
			$terms[$term->getName()]['start'] = $term->getFirstDay();
			$terms[$term->getName()]['end']   = $term->getLastDay();
		}

		return $value;
	}
}
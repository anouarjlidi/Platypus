<?php
namespace App\Repository;

use App\Entity\SchoolYearSpecialDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * SchoolYearSpecialDayRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchoolYearSpecialDayRepository extends ServiceEntityRepository
{
	/**
	 * SchoolYearSpecialDayRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, SchoolYearSpecialDay::class);
    }
}

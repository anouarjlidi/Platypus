<?php
namespace App\Repository;

use App\Entity\SchoolYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * SchoolYearRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchoolYearRepository extends ServiceEntityRepository
{
	/**
	 * SchoolYearRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		try {
		    parent::__construct($registry, SchoolYear::class);
        } catch (ConnectionException $e)
        {

        }
    }
}

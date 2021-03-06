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
 * Date: 31/07/2018
 * Time: 17:45
 */
namespace App\Manager;

use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StaffManager
 * @package App\Manager
 */
class StaffManager
{
    /**
     * @var EntityManagerInterface
     */
    private static $entityManager;

    /**
     * StaffManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        self::$entityManager = $entityManager;
    }

    /**
     * getStaffList
     *
     * @param array|null $select
     * @param null|string $where
     * @param array|null $order
     * @param array|null $join
     * @return array
     */
    public static function getStaffList(array $select = ['s, p'], string $where = '', array $order = [], array $join = []): array
    {
        $query = self::getEntityManager()->getRepository(Staff::class)->createQueryBuilder('s')
            ->leftJoin('s.person', 'p')
            ->select($select);
        if (! empty($where)) {
            $query->where($where);
            $query->andWhere('p.id IS NOT NULL');
        } else
            $query->where('p.id IS NOT NULL');
        $x=0;
        foreach($order as $name=>$direction)
            if ($x++ === 0)
                $query->orderBy($name, $direction);
            else
                $query->addOrderBy($name, $direction);

        foreach($join as $name=>$alias)
            $query->leftJoin($name, $alias);

        $results = $query->getQuery()->getArrayResult();

        return $results;
    }

    /**
     * @return EntityManagerInterface
     */
    public static function getEntityManager(): EntityManagerInterface
    {
        return self::$entityManager;
    }

    /**
     * getStaffListChoice
     *
     * @return array
     */
    public static function getStaffListChoice(): array
    {
        $result = [];
        foreach(self::getStaffList(['s','p'], '', ['p.surname' => 'ASC', 'p.preferredName' => 'ASC']) as $staff)
            $result[$staff['person']['surname'].': '.$staff['person']['preferredName']] = $staff['person']['id'];

        return $result;
    }
}
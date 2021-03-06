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
 * Date: 13/09/2018
 * Time: 15:12
 */
namespace App\Manager\Gibbon;

use App\Entity\SchoolYear;
use App\Entity\SchoolYearSpecialDay;
use Doctrine\Common\Persistence\ObjectManager;

class GibbonSchoolYearSpecialDayManager extends GibbonTransferManager
{
    /**
     * @var array
     */
    protected $entityName = [
        SchoolYearSpecialDay::class,
    ];

    /**
     * @var string
     */
    protected $gibbonName = 'gibbonSchoolYearSpecialDay';

    /**
     * @var array
     */

    protected $transferRules = [
        'gibbonSchoolYearSpecialDayID' => [
            'field' => 'id',
            'functions' => [
                'integer' => '',
            ],
        ],
        'gibbonSchoolYearTermID' => [
            'field' => '',
        ],
        'name' => [
            'field' => 'name',
            'functions' => [
                'length' => 20,
            ],
        ],
        'description' => [
            'field' => 'description',
            'functions' => [
                'length' => 255,
            ],
        ],
        'type' => [
            'field' => 'special_day_type',
            'functions' => [
                'length' => 16,
                'lowercase' => '',
            ],
        ],
        'date' => [
            'field' => 'day_date',
            'functions' => [
                'date' => '',
            ],
        ],
        'schoolOpen' => [
            'field' => 'school_open',
            'functions' => [
                'time' => '',
            ],
        ],
        'schoolStart' => [
            'field' => 'school_start',
            'functions' => [
                'time' => '',
            ],
        ],
        'schoolEnd' => [
            'field' => 'school_end',
            'functions' => [
                'time' => '',
            ],
        ],
        'schoolClose' => [
            'field' => 'school_close',
            'functions' => [
                'time' => '',
            ],
        ],
    ];

    /**
     * @var string
     */
    protected $nextGibbonName = 'gibbonSchoolYearTerm';

    /**
     * postRecord
     *
     * @param string $entityName
     * @param array $newData
     * @return array
     */
    public function postRecord(string $entityName, array $newData, ObjectManager $manager): array
    {
        // need to add School Year indicator.
        $schoolYear = $manager->getRepository(SchoolYear::class)->createQueryBuilder('y')
            ->select('y.id')
            ->where('y.firstDay <= :f_date')
            ->andWhere('y.lastDay >= :l_date')
            ->setParameter('f_date', new \DateTime($newData['day_date']))
            ->setParameter('l_date', new \DateTime($newData['day_date']))
            ->getQuery()
            ->getOneOrNullResult();
        if (! empty($schoolYear))
            $newData['school_year_id'] = $schoolYear['id'];
        else
            $newData = [];

        return $newData;
    }
}
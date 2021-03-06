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
 * Date: 11/06/2018
 * Time: 16:47
 */
namespace App\Entity;

use App\Entity\Extension\SchoolYearTermExtension;

class SchoolYearTerm extends SchoolYearTermExtension
{
    /**
     * @var integer|null
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return SchoolYearTerm
     */
    public function setId(?int $id): SchoolYearTerm
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var string|null
     */
    private $name;

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return SchoolYearTerm
     */
    public function setName(?string $name): SchoolYearTerm
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var SchoolYear|null
     */
    private $schoolYear;

    /**
     * @return SchoolYear|null
     */
    public function getSchoolYear(): ?SchoolYear
    {
        return $this->schoolYear;
    }

    /**
     * @param SchoolYear|null $schoolYear
     * @param bool $add
     * @return SchoolYear
     */
    public function setSchoolYear(?SchoolYear $schoolYear, $add = true): SchoolYearTerm
    {
        if ($add && $schoolYear)
            $schoolYear->addTerm($this, false);

        $this->schoolYear = $schoolYear;
        return $this;
    }

    /**
     * @var string|null
     */
    private $nameShort;

    /**
     * @return null|string
     */
    public function getNameShort(): ?string
    {
        return $this->nameShort;
    }

    /**
     * @param null|string $nameShort
     * @return SchoolYearTerm
     */
    public function setNameShort(?string $nameShort): SchoolYearTerm
    {
        $this->nameShort = $nameShort;
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $firstDay;

    /**
     * @return \DateTime
     */
    public function getFirstDay(): ?\DateTime
    {
        return $this->firstDay;
    }

    /**
     * @param \DateTime $firstDay
     * @return SchoolYearTerm
     */
    public function setFirstDay(?\DateTime $firstDay): SchoolYearTerm
    {
        $this->firstDay = $firstDay;
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $lastDay;

    /**
     * @return \DateTime
     */
    public function getLastDay(): ?\DateTime
    {
        return $this->lastDay;
    }

    /**
     * @param \DateTime $lastDay
     * @return SchoolYearTerm
     */
    public function setLastDay(?\DateTime $lastDay): SchoolYearTerm
    {
        $this->lastDay = $lastDay;
        return $this;
    }
}
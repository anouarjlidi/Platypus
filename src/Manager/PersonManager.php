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
 * User' => 'craig
 * Date' => '10/08/2018
 * Time' => '15:11
 */
namespace App\Manager;

use App\Entity\Person;
use App\Entity\PersonRole;
use App\Manager\Traits\EntityTrait;
use App\Util\PersonNameHelper;

/**
 * Class PersonManager
 * @package App\Manager
 */
class PersonManager extends TabManager
{
    use EntityTrait;

    /**
     * @var string
     */
    private $entityName = Person::class;

    /**
     * @var array
     */
    protected $tabs = [
        [
            'name' => 'basic.information',
            'label' => 'basic.information.tab',
            'include' => 'Person/basic_information.html.twig',
            'message' => 'basicInformationMessage',
            'translation' => 'Person',
        ],
        [
            'name' => 'system.access',
            'label' => 'system.access.tab',
            'include' => 'Person/system_access.html.twig',
            'message' => 'systemAccessMessage',
            'translation' => 'Person',
        ],
        [
            'name' => 'contact.information',
            'label' => 'contact.information.tab',
            'include' => 'Person/contact_information.html.twig',
            'message' => 'contactInformationMessage',
            'translation' => 'Person',
        ],
        [
            'name' => 'school.information',
            'label' => 'school.information.tab',
            'include' => 'Person/school_information.html.twig',
            'message' => 'schoolInformationMessage',
            'translation' => 'Person',
            'display' => 'isStudent',
        ],
        [
            'name' => 'background.information',
            'label' => 'background.information.tab',
            'include' => 'Person/background_information.html.twig',
            'message' => 'backgroundInformationMessage',
            'translation' => 'Person',
        ],
        [
            'name' => 'miscellaneous',
            'label' => 'miscellaneous.tab',
            'include' => 'Person/miscellaneous.html.twig',
            'message' => 'miscellaneousInformationMessage',
            'translation' => 'Person',
        ],
    ];

    /**
     * getFullName
     *
     * @param array $options
     * @return string
     */
    public function getFullName(array $options = []): string
    {
        return PersonNameHelper::getFullName($this->getEntity(), $options);
    }

    /**
     * isStudent
     *
     * @return bool
     */
    public function isStudent(?Person $person = null): bool
    {
        if (empty($person))
            $person = $this->getEntity();
        if (! $person instanceof Person )
            return false;
        $role = $person->getPrimaryRole();
        if (! $role instanceof PersonRole)
            return false;
        if ($role->getCategory() === 'student')
            return true;
        return false;
    }

    /**
     * isStaff
     *
     * @param Person|null $person
     * @return bool
     */
    public function isStaff(?Person $person = null): bool
    {
        if (empty($person))
            $person = $this->getEntity();
        if (! $person instanceof Person )
            return false;
        $role = $person->getPrimaryRole();
        dump($role);
        dump($role->getCategory());

        if (! $role instanceof PersonRole)
            return false;
        if (in_array($role->getCategory(), ['administrator','support_staff','teacher','staff']))
            return true;
        return false;
    }
}
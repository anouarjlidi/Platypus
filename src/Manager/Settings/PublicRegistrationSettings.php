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
 * Date: 16/09/2018
 * Time: 15:42
 */
namespace App\Manager\Settings;

use App\Manager\MessageManager;
use App\Manager\PersonRoleManager;
use App\Manager\SettingManager;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class PublicRegistrationSettings
 * @package App\Manager\Settings
 */
class PublicRegistrationSettings implements SettingCreationInterface
{
    /**
     * getName
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PublicRegistration';
    }

    /**
     * getSettings
     *
     * @param SettingManager $sm
     * @return SettingCreationInterface
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function getSettings(SettingManager $sm): SettingCreationInterface
    {
        $sections = [];
        $sections['header'] = 'public_registration_settings';
        $settings = [];

        $setting = $sm->createOneByName('person_admin.enable_public_registration');

        $setting->setName('people_admin.enable_public_registration')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('boolean')
            ->__set('displayName', 'Enable Public Registration')
            ->__set('choice', null)
            ->__set('description', 'Allows members of the public to register to use the system.');
        if (empty($setting->getValue())) {
            $setting->setValue(false)
                ->setValidators(null)
                ->setDefaultValue(false)
                ->__set('translateChoice', null)
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('person_admin.public_registration_minimum_age');

        $setting->setName('person_admin.public_registration_minimum_age')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('number')
            ->__set('displayName', 'Public Registration Minimum Age')
            ->__set('choice', null)
            ->__set('description', 'The minimum age, in years, permitted to register.');
        if (empty($setting->getValue())) {
            $setting->setValue(13)
                ->setValidators([
                    new Range(['max' => 30, 'min' => 5])
                ])
                ->setDefaultValue(13)
                ->__set('translateChoice', null)
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('person_admin.public_registration_default_status');

        $setting->setName('person_admin.public_registration_default_status')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('choice')
            ->__set('displayName', 'Public Registration Default Status')
            ->__set('choice', ['full', 'pending'])
            ->__set('description', 'Should new people be \'Full\' or \'Pending Approval\'?
');
        if (empty($setting->getValue())) {
            $setting->setValue('pending')
                ->setValidators(null)
                ->setDefaultValue('pending')
                ->__set('translateChoice', 'Setting')
            ;
        }
        $settings[] = $setting;

        $prm = new PersonRoleManager($sm->getEntityManager(), new MessageManager());

        $setting = $sm->createOneByName('person_admin.public_registration_default_role');

        $setting->setName('person_admin.public_registration_default_role')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('choice')
            ->__set('displayName', 'Public Registration Default Role')
            ->__set('choice', $prm->getPersonRoleList())
            ->__set('description', 'System role to be assigned to registering members of the public.');
        if (empty($setting->getValue())) {
            $setting->setValue('student')
                ->setValidators(null)
                ->setDefaultValue('student')
                ->__set('translateChoice', 'Person')
            ;
        }
        $settings[] = $setting;

        $section['name'] = 'General Settings';
        $section['description'] = '';
        $section['settings'] = $settings;

        $sections[] = $section;

        $sections[] = $section;
        $section = [];
        $settings = [];

        $setting = $sm->createOneByName('person_admin.public_registration_intro');

        $setting->setName('person_admin.public_registration_intro')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('html')
            ->__set('displayName', 'Public Registration Introductory Text')
            ->__set('choice', null)
            ->__set('description', 'HTML text that will appear above the public registration form.');
        if (empty($setting->getValue())) {
            $setting->setValue(null)
                ->setValidators(null)
                ->setDefaultValue(null)
                ->__set('translateChoice', null)
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('person_admin.public_registration_postscript');

        $setting->setName('person_admin.public_registration_postscript')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('html')
            ->__set('displayName', 'Public Registration Postscript')
            ->__set('choice', null)
            ->__set('description', 'HTML text that will appear underneath the public registration form.');
        if (empty($setting->getValue())) {
            $setting->setValue(null)
                ->setValidators(null)
                ->setDefaultValue(null)
                ->__set('translateChoice', null)
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('person_admin.public_registration_privacy_statement');

        $setting->setName('person_admin.public_registration_privacy_statement')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('html')
            ->__set('displayName', 'Public Registration Privacy Statement')
            ->__set('choice', null)
            ->__set('description', 'HTML text that will appear above the Submit button, explaining privacy policy.');
        if (empty($setting->getValue())) {
            $setting->setValue('By registering for this site you are giving permission for your personal data to be used and shared within this organisation and its websites. We will not share your personal data outside our organisation.')
                ->setValidators(null)
                ->setDefaultValue('By registering for this site you are giving permission for your personal data to be used and shared within this organisation and its websites. We will not share your personal data outside our organisation.')
                ->__set('translateChoice', null)
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('person_admin.public_registration_agreement');

        $setting->setName('person_admin.public_registration_agreement')
            ->__set('role', 'ROLE_ADMIN')
            ->setType('html')
            ->__set('displayName', 'Public Registration Agreement')
            ->__set('choice', null)
            ->__set('description', 'Agreement that people must confirm before joining. Blank for no agreement.');
        if (empty($setting->getValue())) {
            $setting->setValue(null)
                ->setValidators(null)
                ->setDefaultValue('In joining this site, and checking the box below, I agree to act lawfully, ethically and with respect for others. I agree to use this site for learning purposes only, and understand that access may be withdrawn at any time, at the discretion of the site\'s administrators.')
                ->__set('translateChoice', null)
            ;
        }
        $settings[] = $setting;

        $section['name'] = 'Interface Options';
        $section['description'] = '';
        $section['settings'] = $settings;

        $sections[] = $section;

        $this->sections = $sections;
        return $this;
    }

    /**
     * @var array
     */
    private $sections;

    /**
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }
}
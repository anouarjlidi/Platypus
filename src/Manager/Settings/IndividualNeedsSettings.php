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
 * Date: 18/06/2018
 * Time: 11:34
 */
namespace App\Manager\Settings;

use App\Manager\SettingManager;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class IndividualNeedsSettings
 * @package App\Manager\Settings
 */
class IndividualNeedsSettings implements SettingCreationInterface
{
    /**
     * getName
     *
     * @return string
     */
    public function getName(): string
    {
        return 'IndividualNeeds';
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
        $settings = [];

        $setting = $sm->createOneByName('individual_needs.targets_template');

        $setting->setName('individual_needs.targets_template')
            ->__set('role', 'ROLE_PRINCIPAL')
            ->setType('html')
            ->__set('displayName', 'Targets Template')
            ->__set('description', 'An HTML template to be used in the targets field.');
        if (empty($setting->getValue())) {
            $setting->setValue(null)
                ->__set('choice', null)
                ->setValidators(null)
                ->setDefaultValue(null)
                ->__set('translateChoice', 'Setting')
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('individual_needs.teaching_strategies_template');

        $setting->setName('individual_needs.teaching_strategies_template')
            ->__set('role', 'ROLE_PRINCIPAL')
            ->setType('html')
            ->__set('displayName', 'Teaching Strategies Template')
            ->__set('description', 'An HTML template to be used in the teaching strategies field.');
        if (empty($setting->getValue())) {
            $setting->setValue(null)
                ->__set('choice', null)
                ->setValidators(null)
                ->setDefaultValue(null)
                ->__set('translateChoice', 'Setting')
            ;
        }
        $settings[] = $setting;

        $setting = $sm->createOneByName('individual_needs.notes_review_template');

        $setting->setName('individual_needs.notes_review_template')
            ->__set('role', 'ROLE_PRINCIPAL')
            ->setType('html')
            ->__set('displayName', 'Notes & Review Template')
            ->__set('description', 'An HTML template to be used in the notes and review field.');
        if (empty($setting->getValue())) {
            $setting->setValue(null)
                ->__set('choice', null)
                ->setValidators(null)
                ->setDefaultValue(null)
                ->__set('translateChoice', 'Setting')
            ;
        }
        $settings[] = $setting;

        $sections = [];

        $section['name'] = 'Templates';
        $section['description'] = '';
        $section['settings'] = $settings;

        $sections[] = $section;
        $sections['header'] = 'individual_needs_descriptor_header';

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

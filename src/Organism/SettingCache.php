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
 * Date: 26/05/2018
 * Time: 08:05
 */
namespace App\Organism;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SettingCache
{
    public function __construct(?Setting $setting)
    {
        $this->setSetting($setting);
    }

    /**
     * @var Setting
     */
    private $setting;

    /**
     * @return Setting|null
     */
    public function getSetting(): ?Setting
    {
        return $this->setting;
    }

    /**
     * @param Setting|null $setting
     * @return SettingCache
     */
    public function setSetting(?Setting $setting): SettingCache
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return strtolower($this->name);
    }

    /**
     * @param string|null $name
     * @return SettingCache
     */
    public function setName(?string $name = null): SettingCache
    {
        $this->name = strtolower($name ?: $this->getSetting()->getName());
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $cacheTime;

    /**
     * @return null|\DateTime
     */
    public function getCacheTime(): ?\DateTime
    {
        return $this->cacheTime;
    }

    /**
     * @param \DateTime $cacheTime
     * @return SettingCache
     */
    public function setCacheTime(?\DateTime $cacheTime): SettingCache
    {
        $this->cacheTime = $cacheTime;
        return $this;
    }

    /**
     * @var mixed
     */
    private $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (empty($this->value) && $this->isBaseSetting()) {
            if (empty($this->getSetting()) || empty($this->getSetting()->getType()))
                return null;
            $method = 'get' . ucfirst($this->getSetting()->getType()) . 'Value';
            $this->value = $this->$method();
        }
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return SettingCache
     */
    public function setValue($value): SettingCache
    {
        $this->value = $value;
        if ($this->isBaseSetting()) {
            if (empty($this->getSetting()) || empty($this->getSetting()->getType()))
                return $this;
            $method = 'set' . ucfirst($this->getSetting()->getType()) . 'Value';
            return $this->$method();
        }
        return $this;
    }

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @var string|null
     */
    private $parent;

    /**
     * @return null|string
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param null|string $parent
     * @return SettingCache
     */
    public function setParent(?string $parent): SettingCache
    {
        $this->parent = $parent;
        if (! empty($parent))
            $this->setBaseSetting(false);
        return $this;
    }

    /**
     * @var string
     */
    private $parentKey;

    /**
     * @return string
     */
    public function getParentKey(): string
    {
        return $this->parentKey;
    }

    /**
     * @param string $parentKey
     * @return SettingCache
     */
    public function setParentKey(string $parentKey): SettingCache
    {
        $this->parentKey = $parentKey;
        return $this;
    }

    /**
     * getDefaultValue
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if (empty($this->defaultValue) && $this->isBaseSetting()) {
            $method = 'getDefault' . ucfirst($this->getSetting()->getType()) . 'Value';
            $this->defaultValue = $this->$method();
        }
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     * @return SettingCache
     */
    public function setDefaultValue($defaultValue): SettingCache
    {
        $this->defaultValue = $defaultValue;
        if ($this->isBaseSetting()) {
            if (empty($this->getSetting()) || empty($this->getSetting()->getType()))
                return $this;
            $method = 'setDefault' . ucfirst($this->getSetting()->getType()) . 'Value';
            return $this->$method();
        }
        return $this;
    }

    /**
     * getSystemValue
     *
     * @param null $default
     * @return mixed|null
     */
    private function getSystemValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * setSystemValue
     *
     * @return SettingCache
     */
    private function setSystemValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getArrayValue
     *
     * @param null $default
     * @return array
     */
    private function getArrayValue(): array
    {
        return self::convertDatabaseToArray($this->getSetting()->getValue());
    }

    /**
     * settArrayValue
     *
     * @return SettingCache
     */
    private function setArrayValue(): SettingCache
    {
        $this->getSetting()->setValue(Yaml::dump($this->value));
        return $this;
    }

    /**
     * getImageValue
     *
     * @return null|string
     */
    private function getImageValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * getTwigValue
     *
     * @param null $default
     * @param array $options
     * @return mixed
     */
    private function getTwigValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * setTwigValue
     *
     * @return SettingCache
     */
    private function setTwigValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getRegexValue
     *
     * @param null $default
     * @return mixed
     */
    private function getRegexValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * setRegexValue
     *
     * @return SettingCache
     */
    private function setRegexValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * setTextValue
     *
     * @return SettingCache
     */
    private function setTextValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getStringValue
     *
     * @param null $default
     * @return mixed
     */
    private function getStringValue(): ?string
    {
        return $this->getSetting()->getValue();
    }

    /**
     * setStringValue
     *
     * @return SettingCache
     */
    private function setStringValue(): SettingCache
    {
        $this->getSetting()->setValue($this->value);
        return $this;
    }

    /**
     * getEnumValue
     *
     * @param null $default
     * @return mixed
     */
    private function getEnumValue($default = null)
    {
        return $this->getStringValue($default);
    }

    /**
     * setEnumValue
     *
     * @return SettingCache
     */
    private function setEnumValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * setImageValue
     *
     * @return SettingCache
     */
    private function setImageValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getDateTimeValue
     *
     * @param null $default
     * @return \DateTime|null
     */
    private function getDateTimeValue($default = null): ?\DateTime
    {
        if ($this->value)
            return $this->value;
        return $this->value = unserialize($this->getSetting()->getValue() ?: $this->getDefaultValue($default));
    }

    /**
     * setDateTimeValue
     *
     * @return SettingCache
     */
    private function setDateTimeValue(): SettingCache
    {
        if ($this->value)
            $this->getSetting()->setValue(serialize($this->value));
        else
            $this->getSetting()->setValue(null);
        return $this;
    }

    /**
     * getTimeValue
     *
     * @param null $default
     * @return \DateTime|null
     */
    private function getTimeValue($default = null): ?\DateTime
    {
        return $this->getDateTimeValue($default);
    }

    /**
     * setTimeValue
     *
     * @return SettingCache
     */
    private function setTimeValue(): SettingCache
    {
        return $this->setDateTimeValue();
    }

    /**
     * getHtmlValue
     *
     * @return null|string
     */
    private function getHtmlValue(): ?string
    {

        return $this->getStringValue();
    }

    /**
     * setHtmlValue
     *
     * @return SettingCache
     */
    private function setHtmlValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getBooleanValue
     *
     * @return bool
     */
    private function getBooleanValue(): bool
    {

        return $this->getSetting()->getValue() ? true : false;
    }

    /**
     * setBooleanValue
     *
     * @return SettingCache
     */
    private function setBooleanValue(): SettingCache
    {
        $this->value = $this->value ? true : false;
        $this->getSetting()->setValue($this->value);
        return $this;
    }

    /**
     * getIntegerValue
     *
     * @return string
     */
    private function getIntegerValue(): string
    {
        return strval(intval($this->getSetting()->getValue()));
    }

    /**
     * setIntegerValue
     *
     * @return SettingCache
     */
    private function setIntegerValue(): SettingCache
    {
        $this->value = strval(intval($this->value));
        $this->getSetting()->setValue($this->value);
        return $this;
    }

    /**
     * getColourValue
     *
     * @return string
     */
    private function getColourValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * setColourValue
     *
     * @return SettingCache
     */
    private function setColourValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getChoiceValue
     *
     * @return null|string
     */
    private function getChoiceValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * setChoiceValue
     *
     * @return SettingCache
     */
    private function setChoiceValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * @var bool
     */
    private $baseSetting = true;

    /**
     * @return bool
     */
    public function isBaseSetting(): bool
    {
        return $this->baseSetting;
    }

    /**
     * @param bool $baseSetting
     * @return SettingCache
     */
    public function setBaseSetting(bool $baseSetting): SettingCache
    {
        $this->baseSetting = $baseSetting;
        return $this;
    }

    /**
     * convertDateTimeToDataBase
     *
     * @param $value
     * @return null|string
     */
    public static function convertDateTimeToDataBase($value): ?string
    {
        if (empty($value))
            return null;
        if ($value Instanceof \DateTime)
            return serialize($value);
        return $value;
    }

    /**
     * convertDateTimeFromDataBase
     *
     * @param $value
     * @return \DateTime|null
     */
    public static function convertDatabaseToDateTime($value): ?\DateTime
    {
        if (empty($value) || $value instanceof \DateTime)
            return $value;

        try {
            return unserialize($value);
        } catch (\ErrorException $e)
        {
            return null;
        }
    }

    /**
     * convertArrayToDatabase
     *
     * @param $value
     * @return null|string
     */
    public static function convertArrayToDatabase($value): ?string
    {
        if (empty($value))
            return null;
        if (is_array($value))
            return Yaml::dump($value);
        return $value;
    }

    /**
     * convertDatabaseToArray
     *
     * @param $value
     * @return array
     */
    public static function convertDatabaseToArray($value): array
    {
        if (empty($value))
            return [];
        if (is_array($value))
            return $value;

        try {
            $x = Yaml::parse($value);
        } catch (ParseException $e)
        {
            return [];
        }
        return is_array($x) ? $x : [];
    }

    /**
     * getDefaultImageValue
     *
     * @return null|string
     */
    private function getDefaultImageValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * getDefaultSystemValue
     *
     * @return null|string
     */
    private function getDefaultSystemValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    private function getDefaultArrayValue(): ?array
    {
        return self::convertDatabaseToArray($this->getSetting()->getDefaultValue());
    }

    /**
     * getDefaultStringValue
     *
     * @param $default
     * @return null|string
     */
    private function getDefaultStringValue(): ?string
    {
        $value = $this->getSetting()->getDefaultValue();
        if (is_null($value) || is_string($value))
            return $value;
        return null;
    }

    /**
     * getDefaultTwigValue
     *
     * @return null|string
     */
    private function getDefaultTwigValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * @var string
     */
    private $type;

    /**
     * getType
     *
     * @return string
     */
    public function getType(): ?string
    {
        if ($this->isBaseSetting())
            return $this->getSetting()->getType();
        return $this->type;
    }

    /**
     * setType
     *
     * @param $type
     * @return SettingCache
     */
    public function setType($type): SettingCache
    {
        $this->type = $type;
        if (! empty($this->getSetting()))
            $this->getSetting()->setType($type);
        return $this;
    }

    /**
     * convertImportValues
     *
     * @return Setting
     */
    public function convertImportValues(): SettingCache
    {
        switch ($this->getType()){
            case 'time':
                $this->value = $this->value ? new \DateTime('1970-01-01 ' . $this->value) : null ;
                $this->defaultValue = $this->defaultValue ? new \DateTime('1970-01-01 ' . $this->defaultValue) : null ;
                break;
            default:
        }
        $this->setValue($this->value);
        $this->setDefaultValue($this->defaultValue);
        return $this;
    }

    /**
     * importSetting
     *
     * @param array $values
     * @return bool
     * @throws TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function importSetting(array $values, EntityManagerInterface $entityManager): bool
    {
        $this->findOneByName($values['name'], $entityManager);
        $this->setting = $this->getSetting() instanceof Setting ? $this->getSetting() : new Setting();
        foreach ($values as $field => $value) {
            $func = 'set' . ucfirst($field);
            if ($field === 'value')
                $this->value = $value;
            elseif ($field === 'defaultValue')
                $this->defaultValue = $value;
            elseif ($field === 'validators')
                $this->validators = $this->convertValidators($value);
            else
                $this->getSetting()->$func($value);
        }
        $this->convertImportValues();
        $entityManager->persist($this->getSetting());
        $entityManager->flush();

        return true;
    }

    public function convertValidators($value): array
    {
        if (empty($value))
            return [];

        if (! is_array($value))
            $value = [$value];

        $results = [];

        foreach($value as $validator)
            if (class_exists($validator))
                $results[] = new $validator();
            else
                trigger_error('The validator ' .$validator . ' is not available', E_USER_ERROR);

        return $results;
    }

    /**
     * findOneByName
     *
     * @param string $name
     * @return SettingCache|null
     * @throws TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function findOneByName(string $name, EntityManagerInterface $entityManager): ?SettingCache
    {
        try {
            $this->setting = $this->getSettingRepository($entityManager)->findOneByName(strtolower($name));
            $this->setCacheTime(new \DateTime('now'));
            $this->setName($name);
        } catch (TableNotFoundException $e) {
            if (in_array($e->getErrorCode(), ['1146', '1045']))
                $this->setting = null;
            else
                throw $e;
        } catch (ConnectionException $e) {
            $this->setting = null;
        }
        return $this;
    }

    /**
     * getSettingRepository
     *
     * @param EntityManagerInterface $entityManager
     * @return SettingRepository
     */
    private function getSettingRepository(EntityManagerInterface $entityManager): SettingRepository
    {
        return $entityManager->getRepository(Setting::class);
    }

    /**
     * isValid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->isBaseSetting() && ! $this->getSetting() instanceof Setting)
            return false;

        if (empty($this->getCacheTime()) || empty($this->getName()))
            return false;

        if ($this->getCacheTime()->getTimestamp() < strtotime('-20 minutes'))
        {
            $this->setCacheTime(null);
            return false;
        }

        return true;
    }

    /**
     * setDefaultTwigValue
     *
     * @return SettingCache
     */
    private function setDefaultTwigValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * setDefaultStringValue
     *
     * @return SettingCache
     */
    private function setDefaultStringValue(): SettingCache
    {
        $this->getSetting()->setDefaultValue($this->defaultValue);
        return $this;
    }

    /**
     * setDefaultArrayValue
     *
     * @return SettingCache
     */
    private function setDefaultArrayValue(): SettingCache
    {
        $this->getSetting()->setDefaultValue(Yaml::dump($this->defaultValue));
        return $this;
    }

    /**
     * setDefaultRegexValue
     *
     * @return SettingCache
     */
    private function setDefaultRegexValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * setDefaultTextValue
     *
     * @return SettingCache
     */
    private function setDefaultTextValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * setDefaultEnumValue
     *
     * @return SettingCache
     */
    private function setDefaultEnumValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * setDefaultImageValue
     *
     * @return SettingCache
     */
    private function setDefaultImageValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * setDefaultTimeValue
     *
     * @return SettingCache
     */
    private function setDefaultTimeValue(): SettingCache
    {
        return $this->setDefaultDateTimeValue();
    }

    /**
     * setDefaultDateTimeValue
     *
     * @return SettingCache
     */
    private function setDefaultDateTimeValue(): SettingCache
    {
        if ($this->defaultValue)
            $this->getSetting()->setDefaultValue(serialize($this->defaultValue));
        else
            $this->getSetting()->setDefaultValue(null);
        return $this;
    }

    /**
     * setDefaultSystemValue
     *
     * @return SettingCache
     */
    private function setDefaultSystemValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * getDefaultHtmlValue
     *
     * @return null|string
     */
    private function getDefaultHtmlValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * setDefaultHtmlValue
     *
     * @return SettingCache
     */
    private function setDefaultHtmlValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * getDefaultIntegerValue
     *
     * @return string
     */
    private function getDefaultIntegerValue(): string
    {

        return strval(intval($this->getSetting()->getDefaultValue()));
    }

    /**
     * setDefaultIntegerValue
     *
     * @return SettingCache
     */
    private function setDefaultIntegerValue(): SettingCache
    {
        $this->defaultValue = strval(intval($this->defaultValue));
        $this->getSetting()->setDefaultValue($this->defaultValue);
        return $this;
    }

    /**
     * getDefaultBooleanValue
     *
     * @return bool
     */
    private function getDefaultBooleanValue(): bool
    {
        return $this->getSetting()->getDefaultValue() ? true : false;
    }

    /**
     * setDefaultBooleanValue
     *
     * @return SettingCache
     */
    private function setDefaultBooleanValue(): SettingCache
    {
        $this->defaultValue = $this->defaultValue ? true : false;
        $this->getSetting()->setDefaultValue($this->defaultValue);
        return $this;
    }

    /**
     * getDefaultColourValue
     *
     * @return null|string
     */
    private function getDefaultColourValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * setDefaultColourValue
     *
     * @return SettingCache
     */
    private function setDefaultColourValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * getDefaultChoiceValue
     *
     * @return null|string
     */
    private function getDefaultChoiceValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * setDefaultChoiceValue
     *
     * @return SettingCache
     */
    private function setDefaultChoiceValue(): SettingCache
    {
        return $this->setDefaultStringValue();
    }

    /**
     * getFinalValue
     *
     * @param $default
     * @return mixed|null
     */
    public function getFinalValue($default)
    {
        $value = null;
        $value = $this->getValue();

        if (empty($value))
            $value = $this->getDefaultValue();
        if (empty($value))
            $value = $default;

        return $value;
    }

    /**
     * writeSetting
     *
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param array $constraints
     * @return boolean|ConstraintViolationListInterface
     */
    public function writeSetting(EntityManagerInterface $entityManager, ValidatorInterface $validator, array $constraints)
    {
        if (! empty($this->getValidators()))
            $constraints = $this->getValidators();

        if (! empty($constraints)) {
            $errors = $validator->validate($this->getSetting()->getValue(), $constraints);
            $errors->addAll($validator->validate($this->getSetting()->getDefaultValue(), $constraints));
            if ($errors->count() > 0)
                return $errors;
        }

        if ($this->isBaseSetting()) {
            $entityManager->persist($this->getSetting());
            $entityManager->flush();
        }

        return true;
    }

    /**
     * getTranslateChoice
     *
     * @return null|string
     */
    public function getTranslateChoice(): ?string
    {
        if ($this->getSetting())
            return $this->getSetting()->getTranslateChoice();
        return null;
    }

    /**
     * getId
     *
     * @return null|integer
     */
    public function getId(): ?int
    {
        if ($this->getSetting())
            return $this->getSetting()->getId();
        return null;
    }

    /**
     * getValidators
     *
     * @return null|array
     */
    public function getValidators(): ?array
    {
        if ($this->getSetting())
            return $this->getSetting()->getValidators();
        return null;
    }

    /**
     * getValidators
     *
     * @return SettingCache
     */
    public function setValidators($value): SettingCache
    {
        if ($this->getSetting())
            $this->getSetting()->setValidators($value);
        return $this;
    }

    /**
     * getDisplayName
     *
     * @return null|string
     */
    public function getDisplayName(): ?string
    {
        if ($this->getSetting())
            return $this->getSetting()->getDisplayName();
        return null;
    }

    /**
     * __set
     *
     * @param string $name
     * @param $value
     * @return SettingCache
     */
    public function __set(string $name, $value): SettingCache
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method($value);

        if ($this->getSetting())
        {
            $this->getSetting()->$method($value);
            return $this;
        }

        trigger_error('The setting field "'.$name.'"" does not seem to exist.', E_USER_ERROR);
    }


    /**
     * __get
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();

        if ($this->getSetting())
        {
            return $this->getSetting()->$method();
        }

        trigger_error('The setting field "'.$name.'"" does not seem to exist.', E_USER_ERROR);
    }
    /**
     * getDescription
     *
     * @return null|string
     */
    public function getDescription(): ?string
    {
        if ($this->getSetting())
            return $this->getSetting()->getDescription();
        return null;
    }

    /**
     * createOneByName
     *
     * @param string $name
     * @return SettingCache
     */
    public function createOneByName(string $name): SettingCache
    {
        $setting = new Setting();
        $name = strtolower($name);
        return $this->setSetting($setting->setName($name))
            ->setName($name);
    }
}
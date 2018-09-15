<?php
namespace App\Pagination;

use App\Entity\FamilyMemberAdult;
use App\Entity\FamilyMemberChild;
use App\Entity\Person;

class PersonPagination extends PaginationReactManager
{
	/**
	 * @var string
	 */
	protected $name = 'Person';

	/**
	 * @var string
	 */
	protected $alias = 'p';

	/**
	 * @var int
	 */
	protected $limit = 25;

    /**
	 * @var string
	 */
	protected $entityName = Person::class;

	/**
	 * @var array
	 */
	protected $join =
		[
			'p.user' => [
				'type'  => 'leftJoin',
				'alias' => 'u',
			],
			'p.primaryRole' => [
				'type' => 'leftJoin',
				'alias' =>'r',
			],
		];

	/**
	 * @var string
	 */
	protected $transDomain = 'Person';

    /**
     * @var array|null
     */
    protected $searchDefinition = [
        'surname',
        'firstName',
        'familyName',
    ];

    /**
     * getAllResults
     *
     * @return array
     */
    public function getAllResults(): array
    {
        $results = $this->buildQuery()
            ->getQuery()
            ->getArrayResult();

        $people = $this->getRepository(FamilyMemberAdult::class)->createQueryBuilder('m')
            ->select('p.id, f.name')
            ->leftJoin('m.family', 'f')
            ->leftJoin('m.person', 'p')
            ->getQuery()
            ->getArrayResult();
        $people = array_merge($people, $this->getRepository(FamilyMemberChild::class)->createQueryBuilder('m')
            ->select('p.id, f.name')
            ->leftJoin('m.family', 'f')
            ->leftJoin('m.person', 'p')
            ->getQuery()
            ->getArrayResult());

        foreach($results as $q=>$w)
            foreach($people as $e=>$r)
                if ($w['id'] === $r['id']) {
                    $results[$q]['familyName'] = $r['name'];
                    break;
                }
        dump($results);
        dump($people);

        return $results;
    }

    /**
     * @var array
     */
    protected $headerDefinition = [
        'title' => 'person.pagination.title',
    ];

    /**
     * @var array
     */
    protected $sortByList = [
        'person.full_name.label' => [
            'surname',
            'firstName'
        ],
    ];

    /**
     * @var array
     */
    protected $columnDefinitions = [
        'p.photo' => [
            'label' => 'person.photo.label',
            'name' => 'photo',
            'style' => 'photo',
            'class' => 'text-center',
            'options' => [
                'width' => '75',
                'default' => 'build/static/images/DefaultPerson.png'
            ],
        ],
        'fullName' => [
            'label' => 'person.full_name.label',
            'name' => 'fullName',
            'size' => 3,
            'style' => 'combine',
            'options' => [
                'combine' => ['surname' => [], 'firstName' => ['join' => ': ']],
            ],
            'select' => ['p.surname', 'p.firstName'],
        ],
        'p.id' => [
            'label' => false,
            'display' => false,
            'name' => 'id',
        ],
        'userId' => [
            'label' => false,
            'display' => false,
            'name' => 'userId',
            'select' => 'u.id',
        ],
        'details' => [
            'label' => 'person.details.label',
            'name' => 'details',
            'style' => 'combine',
            'options' => [
                'combine' => ['status' => ['translate' => 'person.status.'], 'primaryRole' => ['join' => '<br />'], 'familyName' => ['join' => '<br />']],
            ],
            'class' => 'text-center',
            'select' => ['p.status', 'r.name AS primaryRole'],
        ],
        'u.username' => [
            'label' => 'person.user_name.label',
            'name' => 'username',
        ],
    ];

    /**
     * @var array|null
     */
    protected $specificTranslations;

    /**
     * setSpecificTranslations
     *
     * @return PaginationInterface
     */
    protected function setSpecificTranslations(): PaginationInterface
    {
        if (empty($this->specificTranslations))
            $this->specificTranslations = [];

        foreach(Person::getTitleList() as $title)
            $this->specificTranslations[] = 'person.title.' . $title;
        foreach(Person::getStatusList() as $value)
            $this->specificTranslations[] = 'person.status.' . $value;
        $this->specificTranslations[] = 'person.action.edit';
        $this->specificTranslations[] = 'person.action.delete';
        $this->specificTranslations[] = 'person.action.password.change' ;

        return $this;
    }

    /**
     * @var array
     */
    protected $actions = [
        'size' => 3,
        'buttons' => [
            [
                'label' => 'person.action.password.change',
                'url' => '/person/__id__/password/change/',
                'url_options' => [
                    '__id__' => 'id',
                ],
                'icon' => ['fas', 'unlock-alt'],
                'colour' => 'light',
            ],
            [
                'label' => 'person.action.delete',
                'url' => '/person/__id__/delete/',
                'url_options' => [
                    '__id__' => 'id',
                ],
                'type' => 'delete',
            ],
            [
                'label' => 'person.action.edit',
                'url' => '/person/__id__/edit/',
                'url_options' => [
                    '__id__' => 'id',
                ],
                'type' => 'edit',
                'response_type' => 'redirect',
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $filter = [
        [
            'group_style' => 'one_only',
            'name' => 'role',
            'label' => 'person_pagination.filter.group.role',
            'fields' => [
                [
                    'name' => 'role::student',
                    'value' => ['Student'],
                    'field' => 'primaryRole',
                    'label' => 'person_pagination.filter.role.student',
                ],
                [
                    'name' => 'role::parent',
                    'value' => ['Parent'],
                    'field' => 'primaryRole',
                    'label' => 'person_pagination.filter.role.parent',
                ],
                [
                    'name' => 'role::staff',
                    'value' => ['Staff','Administrator'],
                    'field' => 'primaryRole',
                    'label' => 'person_pagination.filter.role.staff',
                ],
            ],
        ],
        [
            'group_style' => 'one_only',
            'name' => 'status',
            'label' => 'person_pagination.filter.group.status',
            'fields' => [
                [
                    'name' => 'status::full',
                    'value' => ['full'],
                    'field' => 'status',
                    'label' => 'person_pagination.filter.status.full',
                ],
                [
                    'name' => 'status::left',
                    'value' => ['left'],
                    'field' => 'status',
                    'label' => 'person_pagination.filter.status.left',
                ],
            ],
        ],
    ];
}
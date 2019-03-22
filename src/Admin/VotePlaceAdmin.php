<?php

namespace AppBundle\Admin;

use AppBundle\Form\UnitedNationsCountryType;
use AppBundle\Intl\UnitedNationsBundle;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VotePlaceAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 32,
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    ];

    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept('list');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse postale',
                'filter_emojis' => true,
            ])
            ->add('postalCode', null, [
                'label' => 'Code postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'filter_emojis' => true,
            ])
            ->add('country', UnitedNationsCountryType::class, [
                'label' => 'Pays',
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('address', null, [
                'label' => 'Adresse postale',
                'filter_emojis' => true,
            ])
            ->add('postalCode', null, [
                'label' => 'Code postal',
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'filter_emojis' => true,
            ])
            ->add('country', null, [
                'label' => 'Pays',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => array_flip(UnitedNationsBundle::getCountries()),
                ],
            ])
        ;
    }
}

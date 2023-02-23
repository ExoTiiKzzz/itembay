<?php

namespace App\Controller\Admin;

use App\Entity\ItemType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ItemTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemType::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Name'),
            AssociationField::new('playerClasses', 'Restricted to')
                ->setFormTypeOption('by_reference', false)
                ->formatValue(function ($value, $entity) {
                    return implode(", ",$entity->getPlayerClasses()->toArray());
                }),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('playerClasses');
    }

}

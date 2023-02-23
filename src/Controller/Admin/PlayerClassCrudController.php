<?php

namespace App\Controller\Admin;

use App\Entity\ItemType;
use App\Entity\PlayerClass;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlayerClassCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlayerClass::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Name'),
            AssociationField::new('canBuy', 'Can buy')
                ->setFormTypeOption('by_reference', false)
                ->formatValue(function ($value, $entity) {
                    return implode(", ",$entity->getCanBuy()->toArray());
                }),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('canBuy');
    }
}

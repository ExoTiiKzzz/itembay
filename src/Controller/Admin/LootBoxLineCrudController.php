<?php

namespace App\Controller\Admin;

use App\Entity\LootBoxLine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class LootBoxLineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LootBoxLine::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('defaultItem','Item')->autocomplete(),
            AssociationField::new('lootBox','Loot box'),
            NumberField::new('probability','Probability'),
        ];
    }

    //set search fields
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['defaultItem.name','lootBox.name']);
    }

}

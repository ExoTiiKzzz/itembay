<?php

namespace App\Controller\Admin;

use App\Entity\LootBox;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LootBoxCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LootBox::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

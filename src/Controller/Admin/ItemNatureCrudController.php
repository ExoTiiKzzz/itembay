<?php

namespace App\Controller\Admin;

use App\Entity\ItemNature;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ItemNatureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemNature::class;
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

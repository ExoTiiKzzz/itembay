<?php

namespace App\Controller\Admin;

use App\Entity\BugReport;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BugReportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BugReport::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextareaField::new('description'),
            AssociationField::new('status'),
            DateTimeField::new('createdAt'),
            DateTimeField::new('updatedAt'),
        ];
    }

}

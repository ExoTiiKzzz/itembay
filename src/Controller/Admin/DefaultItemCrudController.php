<?php

namespace App\Controller\Admin;

use App\Entity\DefaultItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DefaultItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DefaultItem::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Name'),
            TextareaField::new('description', 'Description'),
            NumberField::new('buy_price', 'Buy price'),
            NumberField::new('sell_price', 'Sell price'),
            ImageField::new('image_url', 'Image')->setBasePath('uploads/images/items/')->setUploadDir('public/uploads/images/items/')->setUploadedFileNamePattern('[randomhash].[extension]'),
            AssociationField::new('itemType', 'Item type'),
        ];
    }

}

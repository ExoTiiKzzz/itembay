<?php

namespace App\DataFixtures;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemType;
use App\Entity\PlayerClass;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $itemTypeCollection = [];
        // create 5 item types
        for ($i = 0; $i < 5; $i++) {
            $itemType = new ItemType();
            $itemType->setName($faker->word);
            $manager->persist($itemType);
            $itemTypeCollection[] = $itemType;
        }

        //create 5 classes
        for ($i = 0; $i < 5; $i++) {
            $class = new PlayerClass();
            $class->setName($faker->word);
            $manager->persist($class);
            for ($j = 0; $j < 3; $j++) {
                $class->addCanBuy($itemTypeCollection[rand(0, 4)]);
            }
        }

        $itemCollection = [];
        // create 20 default items
        for ($i = 0; $i < 20; $i++) {
            $buyPrice = rand(0, 1000);
            $item = new DefaultItem();
            $item->setName($faker->word);
            $item->setBuyPrice($buyPrice);
            $item->setSellPrice((int) $buyPrice * 0.8);
            $item->setDescription($faker->text);
            $item->setImageUrl($faker->imageUrl(640, 480, 'cats', true, 'Faker'));
            $item->setItemType($itemTypeCollection[rand(0, 4)]);
            $manager->persist($item);
            $itemCollection[] = $item;
        }

        //create 20 items
        for ($i = 0; $i < 20; $i++) {
            $item = new Item();
            $item->setDefaultItem($itemCollection[rand(0, 19)]);
            $manager->persist($item);
        }

        $manager->flush();
    }
}

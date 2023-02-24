<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('itemPrice', [$this, 'formatItemPrice']),
        ];
    }

    public function formatItemPrice($price)
    {
        //1 gold piece = 10 silver pieces = 100 bronze pieces
        $gold = floor($price / 100);
        $silver = floor(($price - ($gold * 100)) / 10);
        $bronze = $price - ($gold * 100) - ($silver * 10);

        $result = '';
        if ($gold > 0) {
            $result .= $gold . ' <i class="fa-brands fa-bitcoin gold"></i> ';
        }
        if ($silver > 0) {
            $result .= $silver . ' <i class="fa-brands fa-bitcoin silver"></i> ';
        }
        if ($bronze > 0) {
            $result .= $bronze . ' <i class="fa-brands fa-bitcoin bronze"></i> ';
        }

        return $result;
    }
}
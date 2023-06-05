<?php

namespace App\Twig;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemType;
use App\Service\ItemNatureService;
use App\Service\ItemSetService;
use App\Service\ItemTypeService;
use App\Service\ProfessionService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('itemPrice', [$this, 'formatItemPrice']),
            new TwigFilter('itemSlug', [$this, 'formatItemSlug']),
            new TwigFilter('randomId', [$this, 'randomId']),
            new TwigFilter('itemStock', [$this, 'getItemStock']),
            new TwigFilter('itemRating', [$this, 'getitemRating']),
            new TwigFilter('reviewRating', [$this, 'getreviewRating']),
            new TwigFilter('professionLevelFromExp', [$this, 'getProfessionLevelFromExp']),
            new TwigFilter('professionActualLevelMinExp', [$this, 'getProfessionActualLevelMinExp']),
            new TwigFilter('professionNextLevelMinExp', [$this, 'getProfessionNextLevelMinExp']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getItemNaturesData', [$this, 'getItemNaturesData']),
            new TwigFunction('getItemTypesData', [$this, 'getItemTypesData']),
            new TwigFunction('getItemSetsData', [$this, 'getItemSetsData']),
        ];
    }

    public function formatItemPrice($price): string
    {
        $price = (int) $price;

        if ($price === 0) {
            return '0 <i class="fa-brands fa-bitcoin gold"></i>' .
                ' 0 <i class="fa-brands fa-bitcoin silver"></i>' .
                ' 0 <i class="fa-brands fa-bitcoin bronze"></i>';
        }

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

    public function formatItemSlug($name)
    {
        return mb_strtolower(preg_replace(array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'),
            array('', '-', ''), $this->remove_accent($name)));
    }

    function remove_accent($str): array|string
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
            'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
            'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
            'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
            'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
            'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
            'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
            'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
            'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť',
            'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ',
            'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
            'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');

        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
            'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
            'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
            'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
            'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
            'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
            'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
            'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
            's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
            'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
            'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

    public function randomId(): string
    {
        $id = '';
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 10; $i++) {
            $id .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $id;
    }

    public function getItemStock($defaultItemId): int
    {
        /** @var DefaultItem $defaultItem */
       $defaultItem = $this->em->getRepository(DefaultItem::class)->find($defaultItemId);

       return count($this->em->getRepository(Item::class)->findBy(['defaultItem' => $defaultItem, 'account' => null, 'isDefaultItem' => true, 'batch' => null ]));
    }

    public function getItemRating(int $defaultItemId): string
    {
        /** @var DefaultItem $defaultItem */
        $defaultItem = $this->em->getRepository(DefaultItem::class)->find($defaultItemId);

        if (!$defaultItem) {
            return '';
        }

        $ratingCount = $defaultItem->getReviews()->count();

        if ($ratingCount === 0) {
            return 'Pas encore évalué';
        }

        $rating = $defaultItem->getAverageRating();
        $evaluations = $ratingCount > 1 ? 'évaluations' : 'évaluation';

        $roundedRating = round($rating);

        $str = '<div class="d-flex align-items-center">';
            $str .= str_repeat('<i class="fa-solid fa-star gold"></i>', $roundedRating);
            $str .= str_repeat('<i class="fa-regular fa-star gold"></i>', 5 - $roundedRating);
            $str .= '<div class="ms-1">&nbsp&nbsp' . $roundedRating . ' sur ' . $ratingCount . ' ' . $evaluations . '.</div>';
        $str .= '</div>';

        return $str;
    }

    public function getReviewRating(int $rating): string
    {
        $roundedRating = round($rating);

        $str = '<div class="d-flex align-items-center">';
            $str .= str_repeat('<i class="fa-solid fa-star gold"></i>', $roundedRating);
            $str .= str_repeat('<i class="fa-regular fa-star gold"></i>', 5 - $roundedRating);
        $str .= '</div>';

        return $str;
    }

    public function getProfessionLevelFromExp(int $exp): string
    {
        return ProfessionService::getProfessionLevelFromExp($exp, $this->em);
    }

    public function getProfessionActualLevelMinExp(int $exp): string
    {
        return ProfessionService::getProfessionActualLevelMinExp($exp, $this->em);
    }

    public function getProfessionNextLevelMinExp(int $exp): string
    {
        return ProfessionService::getProfessionNextLevelMinExp($exp, $this->em);
    }

    public function getItemNaturesData(): array
    {
        return ItemNatureService::getItemNaturesForSelect($this->em);
    }

    public function getItemTypesData(): array
    {
        return ItemTypeService::getItemTypesForSelect($this->em, $this->getItemNaturesData());
    }

    public function getItemSetsData(): array
    {
        return ItemSetService::getItemSetsForSelect($this->em);
    }
}
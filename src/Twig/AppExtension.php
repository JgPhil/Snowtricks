<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;



class AppExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('twig_json_decode', [$this, 'parsedJSON'])
        ];
    }

    public function parsedJSON($json)
    {
        $parsedJSON = json_decode($json, true, 512, 'JSON_OBJECT_AS_ARRAY');

        return $parsedJSON;
    }
}

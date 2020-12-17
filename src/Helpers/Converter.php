<?php

namespace App\Helpers;

use Symfony\Component\Yaml\Yaml;


class Converter
{

    private $yaml;
    private  $result;
    private $file = 'config/figures_data.php';

    public function __construct()
    {
        $this->yaml = new \Symfony\Component\Yaml\Yaml;
    }

    public function convert(){
        return yaml::parse($this->file);
    }

}

<?php

namespace App\Helpers;

use Symfony\Component\Yaml\Yaml;

$yaml = new \Symfony\Component\Yaml\Yaml;

$result = yaml::parse('config/figures_data.php');


var_dump($result);
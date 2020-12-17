<?php

use Symfony\Component\Yaml\Yaml;

require('vendor/autoload.php');
require('config/figures_data.php');


$yaml = Yaml::dump($categoryArray, 4, 2);

file_put_contents('config/figures.yml', $yaml);

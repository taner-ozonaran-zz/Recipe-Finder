<?php

namespace Recipefinder;

include_once 'Models/Ingredient.php';
include_once 'Models/Recipe.php';
include_once 'Libs/Recipefinder.php';

$Recipefinder = new \Libs\Recipefinder\Recipefinder($argv);
$Recipefinder->findRecipes();
Print($Recipefinder->getResults());
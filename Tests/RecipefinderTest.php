<?php
namespace Recipefinder;

include_once 'Models/Ingredient.php';
include_once 'Models/Recipe.php';
include_once 'Libs/Recipefinder.php';

class RecipefinderTest extends \PHPUnit_Framework_TestCase
{

    public function testRecipefinderValid1() {
        $commandLineArguments = array(
            1 => "Data/ingredients.csv",
            2 => "Data/recipes.json"
        );
        $Recipefinder = new \Libs\Recipefinder\Recipefinder($commandLineArguments);
        $Recipefinder->findRecipes();
        $this->assertEquals('saladsandwich', $Recipefinder->getResults());
    }
    
    public function testRecipefinderValid2() {
        $commandLineArguments = array(
            1 => "Data/ingredients_2.csv",
            2 => "Data/recipes.json"
        );
        $Recipefinder = new \Libs\Recipefinder\Recipefinder($commandLineArguments);
        $Recipefinder->findRecipes();
        $this->assertEquals('grilledcheeseontoast', $Recipefinder->getResults());
    }
    
    public function testRecipefinderNoValidRecipes() {
        $commandLineArguments = array(
            1 => "Data/ingredients_3.csv",
            2 => "Data/recipes.json"
        );
        $Recipefinder = new \Libs\Recipefinder\Recipefinder($commandLineArguments);
        $Recipefinder->findRecipes();
        $this->assertEquals('Order Takeaway', $Recipefinder->getResults());
    }
    
}
?>
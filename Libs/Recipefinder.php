<?php

namespace Libs\Recipefinder;

class MyException extends \Exception {
    
}

/**
 * Recipefinder
 *
 * Recipefinder class is used to suggest what recipes to cook based on
 * matching ingedients in your fridge and the ingredients of a recipes.
 * 
 * This class is designed to be used from the command line (however with slight
 * alterations to the code can be accessable as a universal library)
 * 
 * @name Libs\Recipefinder\Recipefinder
 * @version    1.0.0
 * @
 * @author Taner Ozonaran <t.ozonaran@gmail.com>
 */
class Recipefinder {

    /**
     * ingredientsInFridge
     * 
     * Used to store the ingredients available to make recipes
     * 
     * @var array of ingredients
     * @access private
     */
    private $ingredientsInFridge = array();

    /**
     * recipes
     * 
     * Used to store the recipes which we will attempt to make
     * 
     * @var array of recipes
     * @access private
     */
    private $recipes = array();

    /**
     * validRecipes
     * 
     * Used to store the valid recipes we have found
     * 
     * @var array of recipes
     * @access private
     */
    private $validRecipes = array();

    /**
     * __construct
     * 
     * Accepts command line arguments and load ingredients and recipes
     *
     * @param array $commandLineArguments 
     * array(1 => path to ingredients csv file,
     *       2 => path to recipes json file
     */
    public function __construct($commandLineArguments = NULL) {

        try {
            // Check if we have valid command line arguments
            if (!isset($commandLineArguments[1]) || !isset($commandLineArguments[2])) {
                throw new MyException('Invalid command line arguments'
                . PHP_EOL
                . 'Usage: php hunger.php <ingredients.csv> <recipes.json>');
            }

            $this->loadIngredients($commandLineArguments[1]);
            $this->loadRecipe($commandLineArguments[2]);
        } catch (MyException $e) {
            echo 'Application Error: ', $e->getMessage(), "\n";
        }
    }

    /**
     * loadIngredients
     * 
     * Open the csv file for processing, then create an array of ingredients
     *
     * @param string $csvFilePath file path to ingredients csv file
     */
    private function loadIngredients($csvFilePath) {
        // Check if we can read ingredients.csv
        if (!is_readable($csvFilePath)) {
            throw new MyException('Can not open file ingredients.csv for reading');
        }
        $file = fopen($csvFilePath, "r");
        // Read the csv file line by line
        while (!feof($file)) {
            $csvRow = fgetcsv($file);
            /**
             * @todo add data validation for csv ingestion
             */
            $this->ingredientsInFridge[] = new \Models\Recipefinder\Ingredient($csvRow[0], $csvRow[1], $csvRow[2], $csvRow[3]);
        }
        fclose($file);
    }

    /**
     * loadRecipe
     * 
     * Open the file containing json data for processing, then create an array of recipes
     *
     * @param string $csvFilePath file path to ingredients csv file
     * 
     */
    private function loadRecipe($jsonFilePath) {
        // Check if we can read ingredients.csv
        if (!is_readable($jsonFilePath)) {
            throw new MyException('Can not open file recipes.json for reading');
        }

        $json = json_decode(file_get_contents($jsonFilePath), true);
        foreach ($json as $recipe) {
            $newRecipe = new \Models\Recipefinder\Recipe($recipe['name']);
            foreach ($recipe['ingredients'] as $ingredient) {
                /**
                 * @todo add data validation here
                 */
                $newIngredient = new \Models\Recipefinder\Ingredient($ingredient['item'], $ingredient['amount'], $ingredient['unit']);
                $newRecipe->addIngredient($newIngredient);
            }
            $this->recipes[] = $newRecipe;
        }
    }

    /**
     * findRecipes
     * 
     * Itterate through all of our recipes and check if we have valid ingredients
     * to create each recipe. Store each valid recipe found in $this->validRecipes[]
     */
    public function findRecipes() {
        foreach ($this->recipes as $recipe) {
            if ($this->checkIngredients($recipe)) {
                $this->validRecipes[] = $recipe;
            }
        }
    }

    /**
     * getResults
     * 
     * @return string name of the recipe which has valid ingredients and which 
     * has the closest useby, if no valid recipe is found then return "Order Takeaway" 
     */
    public function getResults() {
        //Check if there are and recipes found
        if (!count($this->validRecipes)) {
            //if not then
            return "Order Takeaway";
        } else {
            // Apply sorting to the result set
            usort($this->validRecipes, array($this, 'usedBySort'));
            // Get the first recipe in the result set
            $result = reset($this->validRecipes);
            return $result->getName();
        }
    }

    /**
     * usedBySort
     * 
     * Utilized by usort($this->validRecipes, array($this, 'usedBySort')); to sort
     * recipes by useby time
     *
     * @param recipe $a
     * @param recipe $b 
     */
    private static function usedBySort($a, $b) {
        return $a->getUsedBy() - $b->getUsedBy();
    }

    /**
     * checkIngredients
     * 
     * For the recipe provided check that we have the ingredients to create the
     * recipe. Also checking if the ingredients have a valid quantity and 
     * useby date is valid.
     *
     * @param recipe &$recipe a single recipe passed by reference so we can update 
     * the recipes useby time
     */
    private function checkIngredients(\Models\Recipefinder\Recipe &$recipe) {

        $recipeHasAllIngredients = TRUE;

        // Itterate the recipes ingredients and check if they are in the fridge
        foreach ($recipe->getIngredients() as $recipeIngredient) {
            $ingredientFound = FALSE;
            // Itterate through the ingredients in the fridge array
            foreach ($this->ingredientsInFridge as $fridgeIngredient) {
                // Check if the ingredients match and useby is not past
                if (($fridgeIngredient->getItem() === $recipeIngredient->getItem()) && ($fridgeIngredient->getAmount() >= $recipeIngredient->getAmount()) && ($fridgeIngredient->getUnit() === $recipeIngredient->getUnit()) && ($fridgeIngredient->isUseby())) {

                    $ingredientFound = TRUE;
                    if ($fridgeIngredient->getUseby() < $recipe->getUsedBy()) {
                        $recipe->setUsedBy($fridgeIngredient->getUseby());
                    }
                    // If the ingredient is found and is valid then break the
                    // inner foreach loop so the next ingredient can be checked
                    break;
                }
            }
            // If an ingredient is not found then flag $recipeHasAllIngredients as false
            // then break from outer foreach to stop checking for other ingredients
            if (!$ingredientFound) {
                $recipeHasAllIngredients = FALSE;
                break;
            }
        }
        return $recipeHasAllIngredients;
    }

}

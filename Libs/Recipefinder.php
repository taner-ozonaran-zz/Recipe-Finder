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
     * __construct
     * 
     * Accepts command line arguments and load ingredients and recipes
     *
     * @param array $commandLineArguments 
     * array(1 => path to ingredients csv file,
     *       2 => path to recipes json file
     * 
     */
    public function __construct($commandLineArguments = NULL) {

        try {
            // Check if we have valid command line arguments
            if (!isset($commandLineArguments[1]) || !isset($commandLineArguments[2])) {
                throw new MyException('Invalid command line arguments'
                . PHP_EOL
                . 'Usage: php Recipefinder.php <ingredients.csv> <recipes.json>');
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
     * 
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

}

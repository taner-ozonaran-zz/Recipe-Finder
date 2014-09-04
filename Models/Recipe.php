<?php

namespace Models\Recipefinder;

/**
 * Recipe
 * 
 * This data model is used to store Recipes
 * 
 * @name Models\Recipefinder\Recipe
 * @version    1.0.0
 * @author Taner Ozonaran <t.ozonaran@gmail.com>
 */

class Recipe {
    
    /**
     * name
     * 
     * The name of the Recipe
     * 
     * @var string
     * @access private
     */
    private $name;
    
    /**
     * ingredients
     * 
     * Stores any array of Ingredients for the Recipe
     * 
     * @var array of Models\Recipefinder\Ingredient
     * @access private
     */
    private $ingredients = array();
    
    /**
     * usedBy
     * 
     * Stores the used by date and time of the recipe. This value will be based on
     * the lowest used by date of the ingredients used 
     * 
     * @var time
     * @access private
     */
    private $usedBy;
    
    public function __construct($name = '') {
        $this->name = $name;
    }

    public function getIngredients() {
        return $this->ingredients;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function addIngredient(Ingredient $ingredient) {
        $this->ingredients[] = $ingredient;
    }
    
    public function getUsedBy() {
        return $this->usedBy;
    }
    
    public function setUsedBy($usedBy) {
        $this->usedBy = $usedBy;
    }
}
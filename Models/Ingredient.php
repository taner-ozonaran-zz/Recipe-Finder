<?php

namespace Models\Recipefinder;

/**
 * Ingredient
 * 
 * This data model is used to store ingredients
 * 
 * @name Models\Recipefinder\Ingredient
 * @version    1.0.0
 * @author Taner Ozonaran <t.ozonaran@gmail.com>
 */
class Ingredient {

    /**
     * item
     * 
     * @var string
     * @access private
     */
    private $item;
    
    /**
     * ammount
     * 
     * @var int
     * @access private
     */
    private $amount;
    
    /**
     * unit
     * 
     * @var enum [of, grams, ml, slices]
     * @access private
     */
    private $unit;
    
    /**
     * useby
     * 
     * @var time
     * @access private
     */
    private $useby;

    public function __construct($item = '', $amount = '', $unit = '', $useby = '') {
        $this->item = $item;
        $this->amount = $amount;
        $this->unit = $unit;
        $this->useby = strtotime(str_replace('/', '-', $useby));
    }

    public function getItem() {
        return $this->item;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function getUseby() {
        return $this->useby;
    }

    /**
     * isUseby
     * 
     * @access public
     * @return Boolean If use by date of this ingredient is past the current time
     * then return FALSE, else return TRUE
     */
    public function isUseby() {
        if ($this->useby < time()) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

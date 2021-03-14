<?php
/**
 * State Active Record
 * @author  <your-name-here>
 */
class State extends TRecord
{
    const TABLENAME = 'state';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $country;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('country_id');
        parent::addAttribute('name');
        parent::addAttribute('slug');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_country
     * Sample of usage: $state->country = $object;
     * @param $object Instance of Coutry
     */
    public function set_country(Country $object)
    {
        $this->coutry = $object;
        $this->coutry_id = $object->id;
    }
    
    /**
     * Method get_country
     * Sample of usage: $state->country->attribute;
     * @returns Coutry instance
     */
    public function get_country()
    {
        // loads the associated object
        if (empty($this->country))
            $this->country = new Country($this->country_id);
    
        // returns the associated object
        return $this->country;
    }
    


}

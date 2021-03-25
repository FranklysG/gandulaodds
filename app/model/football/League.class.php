<?php
/**
 * League Active Record
 * @author  <your-name-here>
 */
class League extends TRecord
{
    const TABLENAME = 'league';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('slug');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

  
}

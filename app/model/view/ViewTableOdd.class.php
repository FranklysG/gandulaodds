<?php
/**
 * ViewTableOdd Active Record
 * @author  <your-name-here>
 */
class ViewTableOdd extends TRecord
{
    const TABLENAME = 'view_table_odd';
    const PRIMARYKEY= 'soccer_team_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('slug');
        parent::addAttribute('acron');
        parent::addAttribute('pts');
        parent::addAttribute('j');
        parent::addAttribute('v');
        parent::addAttribute('e');
        parent::addAttribute('d');
        parent::addAttribute('gp');
        parent::addAttribute('gc');
        parent::addAttribute('sg');
        parent::addAttribute('ap');
        parent::addAttribute('odds');
    }


}

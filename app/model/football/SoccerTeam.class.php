<?php
/**
 * SoccerTeam Active Record
 * @author  <your-name-here>
 */
class SoccerTeam extends TRecord
{
    const TABLENAME = 'soccer_team';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $state;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('state_id');
        parent::addAttribute('name');
        parent::addAttribute('slug');
        parent::addAttribute('acron');
        parent::addAttribute('shield');
        parent::addAttribute('stadium');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_state
     * Sample of usage: $soccer_team->state = $object;
     * @param $object Instance of State
     */
    public function set_state(State $object)
    {
        $this->state = $object;
        $this->state_id = $object->id;
    }
    
    /**
     * Method get_state
     * Sample of usage: $soccer_team->state->attribute;
     * @returns State instance
     */
    public function get_state()
    {
        // loads the associated object
        if (empty($this->state))
            $this->state = new State($this->state_id);
    
        // returns the associated object
        return $this->state;
    }
    


}

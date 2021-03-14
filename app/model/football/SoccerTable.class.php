<?php
/**
 * SoccerTable Active Record
 * @author  <your-name-here>
 */
class SoccerTable extends TRecord
{
    const TABLENAME = 'soccer_table';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $soccer_team;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('soccer_team_id');
        parent::addAttribute('win');
        parent::addAttribute('draw');
        parent::addAttribute('los');
        parent::addAttribute('pro_goal');
        parent::addAttribute('own_goal');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_soccer_team
     * Sample of usage: $soccer_table->soccer_team = $object;
     * @param $object Instance of SoccerTeam
     */
    public function set_soccer_team(SoccerTeam $object)
    {
        $this->soccer_team = $object;
        $this->soccer_team_id = $object->id;
    }
    
    /**
     * Method get_soccer_team
     * Sample of usage: $soccer_table->soccer_team->attribute;
     * @returns SoccerTeam instance
     */
    public function get_soccer_team()
    {
        // loads the associated object
        if (empty($this->soccer_team))
            $this->soccer_team = new SoccerTeam($this->soccer_team_id);
    
        // returns the associated object
        return $this->soccer_team;
    }
    


}

<?php
/**
 * SoccerGame Active Record
 * @author  <your-name-here>
 */
class SoccerGame extends TRecord
{
    const TABLENAME = 'soccer_game';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $soccer_game_team;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('football_league_id');
        parent::addAttribute('hour');
        parent::addAttribute('date');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_soccer_game_team
     * Sample of usage: $soccer_game->soccer_game_team = $object;
     * @param $object Instance of SoccerGameTeam
     */
    public function set_soccer_game_team(SoccerGameTeam $object)
    {
        $this->soccer_game_team = $object;
        $this->soccer_game_team_id = $object->id;
    }
    
    /**
     * Method get_soccer_game_team
     * Sample of usage: $soccer_game->soccer_game_team->attribute;
     * @returns SoccerGameTeam instance
     */
    public function get_soccer_game_team()
    {
        // loads the associated object
        if (empty($this->soccer_game_team))
            $this->soccer_game_team = new SoccerGameTeam($this->soccer_game_team_id);
    
        // returns the associated object
        return $this->soccer_game_team;
    }
    


}

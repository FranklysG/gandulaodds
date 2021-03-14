<?php
/**
 * SoccerMatch Active Record
 * @author  <your-name-here>
 */
class SoccerMatch extends TRecord
{
    const TABLENAME = 'soccer_match';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $soccer_team_master;
    private $soccer_team_visiting;
    private $foot_ball_league;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('football_league_id');
        parent::addAttribute('soccer_team_master_id');
        parent::addAttribute('soccer_team_visiting_id');
        parent::addAttribute('hour');
        parent::addAttribute('date_game');
        parent::addAttribute('score_master');
        parent::addAttribute('score_visiting');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_soccer_team_master
     * Sample of usage: $soccer_match->soccer_team_master = $object;
     * @param $object Instance of SoccerTeam
     */
    public function set_soccer_team_master(SoccerTeam $object)
    {
        $this->soccer_team_master = $object;
        $this->soccer_team_master_id = $object->id;
    }
    
    /**
     * Method get_soccer_team_master
     * Sample of usage: $soccer_match->soccer_team_master->attribute;
     * @returns SoccerTeam instance
     */
    public function get_soccer_team_master()
    {
        // loads the associated object
        if (empty($this->soccer_team_master))
            $this->soccer_team_master = new SoccerTeam($this->soccer_team_master_id);
    
        // returns the associated object
        return $this->soccer_team_master;
    }
    
    
    /**
     * Method set_soccer_team_visiting
     * Sample of usage: $soccer_match->soccer_team_visiting = $object;
     * @param $object Instance of SoccerTeam
     */
    public function set_soccer_team_visiting(SoccerTeam $object)
    {
        $this->soccer_team_visiting = $object;
        $this->soccer_team_visiting_id = $object->id;
    }
    
    /**
     * Method get_soccer_team_visiting
     * Sample of usage: $soccer_match->soccer_team_visiting->attribute;
     * @returns SoccerTeam instance
     */
    public function get_soccer_team_visiting()
    {
        // loads the associated object
        if (empty($this->soccer_team_visiting))
            $this->soccer_team_visiting = new SoccerTeam($this->soccer_team_visiting_id);
    
        // returns the associated object
        return $this->soccer_team_visiting;
    }
    
    
    /**
     * Method set_foot_ball_league
     * Sample of usage: $soccer_match->foot_ball_league = $object;
     * @param $object Instance of FootBallLeague
     */
    public function set_foot_ball_league(FootBallLeague $object)
    {
        $this->foot_ball_league = $object;
        $this->foot_ball_league_id = $object->id;
    }
    
    /**
     * Method get_foot_ball_league
     * Sample of usage: $soccer_match->foot_ball_league->attribute;
     * @returns FootBallLeague instance
     */
    public function get_foot_ball_league()
    {
        // loads the associated object
        if (empty($this->foot_ball_league))
            $this->foot_ball_league = new FootBallLeague($this->foot_ball_league_id);
    
        // returns the associated object
        return $this->foot_ball_league;
    }
    


}

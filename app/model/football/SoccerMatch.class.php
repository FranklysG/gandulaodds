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
    
    
    private $football_league;
    private $soccer_team_master;
    private $soccer_team_visiting;
    private $soccer_tables;

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
        parent::addAttribute('date');
        parent::addAttribute('score_master');
        parent::addAttribute('score_visiting');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_football_league
     * Sample of usage: $soccer_match->football_league = $object;
     * @param $object Instance of FootballLeague
     */
    public function set_football_league(FootballLeague $object)
    {
        $this->football_league = $object;
        $this->football_league_id = $object->id;
    }
    
    /**
     * Method get_football_league
     * Sample of usage: $soccer_match->football_league->attribute;
     * @returns FootballLeague instance
     */
    public function get_football_league()
    {
        // loads the associated object
        if (empty($this->football_league))
            $this->football_league = new FootballLeague($this->football_league_id);
    
        // returns the associated object
        return $this->football_league;
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
     * Method addSoccerTable
     * Add a SoccerTable to the SoccerMatch
     * @param $object Instance of SoccerTable
     */
    public function addSoccerTable(SoccerTable $object)
    {
        $this->soccer_tables[] = $object;
    }
    
    /**
     * Method getSoccerTables
     * Return the SoccerMatch' SoccerTable's
     * @return Collection of SoccerTable
     */
    public function getSoccerTables()
    {
        return $this->soccer_tables;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->soccer_tables = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related SoccerTable objects
        $repository = new TRepository('SoccerTable');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('soccer_match_id', '=', $id));
        $this->soccer_tables = $repository->load($criteria);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related SoccerTable objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('soccer_match_id', '=', $this->id));
        $repository = new TRepository('SoccerTable');
        $repository->delete($criteria);
        // store the related SoccerTable objects
        if ($this->soccer_tables)
        {
            foreach ($this->soccer_tables as $soccer_table)
            {
                unset($soccer_table->id);
                $soccer_table->soccer_match_id = $this->id;
                $soccer_table->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related SoccerTable objects
        $repository = new TRepository('SoccerTable');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('soccer_match_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
<?php
/**
 * FootballLeague Active Record
 * @author  <your-name-here>
 */
class FootballLeague extends TRecord
{
    const TABLENAME = 'football_league';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $soccer_matchs;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('league_id');
        parent::addAttribute('shield');
        parent::addAttribute('season');
        parent::addAttribute('date_ini');
        parent::addAttribute('date_end');
        parent::addAttribute('continent');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

      
   /**
     * Method set_league
     * Sample of usage: $soccer_match->league = $object;
     * @param $object Instance of League
     */
    public function set_league(League $object)
    {
        $this->league = $object;
        $this->league_id = $object->id;
    }
    
    /**
     * Method get_league
     * Sample of usage: $soccer_match->league->attribute;
     * @returns League instance
     */
    public function get_league()
    {
        // loads the associated object
        if (empty($this->league))
            $this->league = new League($this->league_id);
    
        // returns the associated object
        return $this->league;
    }
    

    
    /**
     * Method addSoccerMatch
     * Add a SoccerMatch to the FootballLeague
     * @param $object Instance of SoccerMatch
     */
    public function addSoccerMatch(SoccerMatch $object)
    {
        $this->soccer_matchs[] = $object;
    }
    
    /**
     * Method getSoccerMatchs
     * Return the FootballLeague' SoccerMatch's
     * @return Collection of SoccerMatch
     */
    public function getSoccerMatchs()
    {
        return $this->soccer_matchs;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->soccer_matchs = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related SoccerMatch objects
        $repository = new TRepository('SoccerMatch');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('football_league_id', '=', $id));
        $this->soccer_matchs = $repository->load($criteria);
    
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
    
        // delete the related SoccerMatch objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('football_league_id', '=', $this->id));
        $repository = new TRepository('SoccerMatch');
        $repository->load($criteria);
        // store the related SoccerMatch objects
        if ($this->soccer_matchs)
        {
            foreach ($this->soccer_matchs as $soccer_match)
            {
                unset($soccer_match->id);
                $soccer_match->football_league_id = $this->id;
                $soccer_match->store();
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
        // delete the related SoccerMatch objects
        $repository = new TRepository('SoccerMatch');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('football_league_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}

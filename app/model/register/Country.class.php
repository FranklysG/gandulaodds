<?php
/**
 * Country Active Record
 * @author  <your-name-here>
 */
class Country extends TRecord
{
    const TABLENAME = 'country';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $states;

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

    
    /**
     * Method addState
     * Add a State to the Country
     * @param $object Instance of State
     */
    public function addState(State $object)
    {
        $this->states[] = $object;
    }
    
    /**
     * Method getStates
     * Return the Country' State's
     * @return Collection of State
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->states = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related State objects
        $repository = new TRepository('State');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('country_id', '=', $id));
        $this->states = $repository->load($criteria);
    
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
    
        // delete the related State objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('country_id', '=', $this->id));
        $repository = new TRepository('State');
        $repository->load($criteria);
        
        // store the related State objects
        if ($this->states)
        {
            foreach ($this->states as $state)
            {
                unset($state->id);
                $state->country_id = $this->id;
                $state->store();
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
        // delete the related State objects
        $repository = new TRepository('State');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('country_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}

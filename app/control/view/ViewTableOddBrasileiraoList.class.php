<?php
/**
 * ViewTableOddBrasileiraoList Listing
 * @author  <your name here>
 */
class ViewTableOddBrasileiraoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_ViewTableOdd');
        $this->form->setFormTitle('Cotação de cada time Brasileirão');
        

        // create the form fields
        $slug = new TDBUniqueSearch('slug','app','SoccerTeam','id','slug');
        $slug->setMinLength(1);
        $acron = new TDBUniqueSearch('acron','app','SoccerTeam','id','acron');
        $acron->setMinLength(1);


        // add the fields
        $this->form->addFields( [ new TLabel('TIME'), $slug ], [ new TLabel('SIGLA'), $acron ] );


        // set sizes
        $slug->setSize('100%');
        $acron->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addActionLink(_t('New'), new TAction(['', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_soccer_team_id = new TDataGridColumn('soccer_team_id', 'ID', 'right');
        $column_slug = new TDataGridColumn('slug', 'TIME', 'left');
        $column_acron = new TDataGridColumn('acron', 'SIGLA', 'left');
        $column_pts = new TDataGridColumn('pts', 'PTS', 'center');
        $column_j = new TDataGridColumn('j', 'J', 'center');
        $column_v = new TDataGridColumn('v', 'V', 'center');
        $column_e = new TDataGridColumn('e', 'E', 'center');
        $column_d = new TDataGridColumn('d', 'D', 'center');
        $column_gp = new TDataGridColumn('gp', 'GP', 'center');
        $column_gc = new TDataGridColumn('gc', 'GC', 'center');
        $column_sg = new TDataGridColumn('sg', 'SG', 'center');
        $column_ap = new TDataGridColumn('ap', 'AP', 'center');
        $column_odds = new TDataGridColumn('odds', 'Odds', 'center');


        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_soccer_team_id);
        $this->datagrid->addColumn($column_slug);
        $this->datagrid->addColumn($column_acron);
        $this->datagrid->addColumn($column_pts);
        $this->datagrid->addColumn($column_j);
        $this->datagrid->addColumn($column_v);
        $this->datagrid->addColumn($column_e);
        $this->datagrid->addColumn($column_d);
        $this->datagrid->addColumn($column_gp);
        $this->datagrid->addColumn($column_gc);
        $this->datagrid->addColumn($column_sg);
        $this->datagrid->addColumn($column_ap);
        $this->datagrid->addColumn($column_odds);


        // $action1 = new TDataGridAction(['', 'onEdit'], ['soccer_team_id'=>'{soccer_team_id}']);
        // $action2 = new TDataGridAction([$this, 'onDelete'], ['soccer_team_id'=>'{soccer_team_id}']);
        
        // $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        // $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('app'); // open a transaction with database
            $object = new ViewTableOdd($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue(__CLASS__.'_filter_slug',   NULL);
        TSession::setValue(__CLASS__.'_filter_acron',   NULL);

        if (isset($data->slug) AND ($data->slug)) {
            $filter = new TFilter('slug', 'like', "%{$data->slug}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_slug',   $filter); // stores the filter in the session
        }


        if (isset($data->acron) AND ($data->acron)) {
            $filter = new TFilter('acron', 'like', "%{$data->acron}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_acron',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'app'
            TTransaction::open('app');
            
            // creates a repository for ViewTableOdd
            $repository = new TRepository('ViewTableOddBrasileirao');
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_slug')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_slug')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_acron')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_acron')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('app'); // open a transaction with database
            $object = new ViewTableOdd($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}

<?php
/**
 * SoccerTeamList Listing
 * @author  <your name here>
 */
class SoccerTeamList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_SoccerTeam');
        $this->form->setFormTitle('Listagem de Times');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $slug = new TEntry('slug');
        $acron = new TEntry('acron');
        $stadium = new TEntry('stadium');
        $state_id = new TDBUniqueSearch('state_id', 'app', 'State', 'id', 'name');
        $state_id->setMinLength(1);
        $state_id->setMask('{name} ({country->slug})');
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');


        // add the fields
        $this->form->addFields( [ new TLabel('Slug'),$slug ],
                                [ new TLabel('Sigla'),$acron ],
                                [ new TLabel('Estadio'),$stadium ],
                                [ new TLabel('Estado'),$state_id ],
                                [ new TLabel('Criado em'),$created_at ],
                                [ new TLabel('Ultima atualização'),$updated_at ] );


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['SoccerTeamForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_name = new TDataGridColumn('name', 'Nome', 'left');
        $column_slug = new TDataGridColumn('slug', 'Slug', 'left');
        $column_acron = new TDataGridColumn('acron', 'Sigla', 'left');
        $column_shield = new TDataGridColumn('shield', 'Escudo', 'left');
        $column_stadium = new TDataGridColumn('stadium', 'Estadio', 'left');
        $column_state_id = new TDataGridColumn('state->slug', 'Estado', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'Criado', 'right');
        $column_updated_at = new TDataGridColumn('updated_at', 'Atualização', 'right');

        $column_created_at->setTransformer(function($value){
            return Convert::toDate($value, 'd/m/Y');
        });
        $column_updated_at->setTransformer(function($value){
            return Convert::toDate($value, 'd/m/Y');
        });

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_slug);
        $this->datagrid->addColumn($column_acron);
        $this->datagrid->addColumn($column_shield);
        $this->datagrid->addColumn($column_stadium);
        $this->datagrid->addColumn($column_state_id);
        $this->datagrid->addColumn($column_created_at);
        // $this->datagrid->addColumn($column_updated_at);


        $action1 = new TDataGridAction(['SoccerTeamForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        if(TSession::getValue('userid') == 1)
            $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
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
            $object = new SoccerTeam($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_stadium',   NULL);
        TSession::setValue(__CLASS__.'_filter_state_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);
        TSession::setValue(__CLASS__.'_filter_updated_at',   NULL);

        if (isset($data->slug) AND ($data->slug)) {
            $filter = new TFilter('slug', 'like', "%{$data->slug}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_slug',   $filter); // stores the filter in the session
        }


        if (isset($data->acron) AND ($data->acron)) {
            $filter = new TFilter('acron', 'like', "%{$data->acron}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_acron',   $filter); // stores the filter in the session
        }


        if (isset($data->stadium) AND ($data->stadium)) {
            $filter = new TFilter('stadium', 'like', "%{$data->stadium}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_stadium',   $filter); // stores the filter in the session
        }


        if (isset($data->state_id) AND ($data->state_id)) {
            $filter = new TFilter('state_id', '=', $data->state_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_state_id',   $filter); // stores the filter in the session
        }


        if (isset($data->created_at) AND ($data->created_at)) {
            $filter = new TFilter('created_at', 'like', "%{$data->created_at}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_created_at',   $filter); // stores the filter in the session
        }


        if (isset($data->updated_at) AND ($data->updated_at)) {
            $filter = new TFilter('updated_at', 'like', "%{$data->updated_at}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_updated_at',   $filter); // stores the filter in the session
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
            
            // creates a repository for SoccerTeam
            $repository = new TRepository('SoccerTeam');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_slug')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_slug')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_acron')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_acron')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_stadium')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_stadium')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_state_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_state_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_created_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_created_at')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_updated_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_updated_at')); // add the session filter
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
                    if (isset($object->shield)) {
                        $image = new TImage("tmp/shield_soocer_team/{$object->shield}");
                        $image->style = 'width: 30px';
                        $object->shield = $image;
                    }
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
        new TQuestion('Verifique se o time não jogou com outro time antes de deleta-lo', $action);
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
            $object = new SoccerTeam($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('warning', 'Time tem um ou mais jogos registrados'); // shows the exception error message
            // new TMessage('error', $e->getMessage()); // shows the exception error message
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

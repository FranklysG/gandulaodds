<?php
/**
 * SoccerTableList Listing
 * @author  <your name here>
 */
class SoccerTableList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_SoccerTable');
        $this->form->setFormTitle('Tabela');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $soccer_team_id = new TDBUniqueSearch('soccer_team_id', 'app', 'SoccerTeam', 'id', 'state_id');
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');


        // add the fields
        $row = $this->form->addFields( [ new TLabel('Time'), $soccer_team_id ],
                                [ new TLabel('Criado em'), $created_at ],
                                [ new TLabel('Ultima atualização'), $updated_at ] );

        $row->layout = ['col-sm-3','col-sm-3','col-sm-3'];
        
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
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_soccer_team_id = new TDataGridColumn('soccer_team->slug', 'Time', 'left');
        $column_pts = new TDataGridColumn('pts', 'Pts', 'center');
        $column_game = new TDataGridColumn('game', 'J', 'center');
        $column_win = new TDataGridColumn('win', 'V', 'center');
        $column_draw = new TDataGridColumn('draw', 'E', 'center');
        $column_los = new TDataGridColumn('los', 'D', 'center');
        $column_pro_goal = new TDataGridColumn('pro_goal', 'GP', 'center');
        $column_own_goal = new TDataGridColumn('own_goal', 'GS', 'center');
        $column_sg = new TDataGridColumn('sg', 'SG', 'center');
        $column_mod = new TDataGridColumn('mod', '%', 'center');
        $column_odd = new TDataGridColumn('odd', 'odd', 'center');
        $column_created_at = new TDataGridColumn('created_at', 'Criado em', 'right');
        $column_updated_at = new TDataGridColumn('updated_at', 'Ultima atualização', 'right');

        $column_created_at->setTransformer(function($value){
            return Convert::toDateBR($value);
        });
        $column_updated_at->setTransformer(function($value){
            return Convert::toDateBR($value);
        });


        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_soccer_team_id);
        $this->datagrid->addColumn($column_pts);
        $this->datagrid->addColumn($column_game);
        $this->datagrid->addColumn($column_win);
        $this->datagrid->addColumn($column_draw);
        $this->datagrid->addColumn($column_los);
        $this->datagrid->addColumn($column_pro_goal);
        $this->datagrid->addColumn($column_own_goal);
        $this->datagrid->addColumn($column_sg);
        $this->datagrid->addColumn($column_mod);
        $this->datagrid->addColumn($column_odd);
        // $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);


        // $action1 = new TDataGridAction(['', 'onEdit'], ['id'=>'{id}']);
        // $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
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
            $object = new SoccerTable($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_soccer_team_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);
        TSession::setValue(__CLASS__.'_filter_updated_at',   NULL);

        if (isset($data->soccer_team_id) AND ($data->soccer_team_id)) {
            $filter = new TFilter('soccer_team_id', '=', $data->soccer_team_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_soccer_team_id',   $filter); // stores the filter in the session
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
            
            // creates a repository for SoccerTable
            $repository = new TRepository('SoccerTable');
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
            

            if (TSession::getValue(__CLASS__.'_filter_soccer_team_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_soccer_team_id')); // add the session filter
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
                    // add the object inside the datagrid
                    $object->pts = ($object->win*3)+($object->draw*1); // pontos no campeonato
                    $object->game = ($object->win+$object->draw+$object->los); // numero de jogos
                    $object->sg = ($object->pro_goal-$object->own_goal); // saldo de gols
                    
                    $mod = ($object->pts/($object->game*3))*100; // calculando chance do time vencer
                    $object->mod = round($mod, 1); // arredondando
                   
                    if($object->mod > 0){
                        $odd = (1/$object->mod)*100; // calculo valor da odd
                        $object->odd = round($odd, 2); // arredondando 
                    }
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
            $object = new SoccerTable($key, FALSE); // instantiates the Active Record
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

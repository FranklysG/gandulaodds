<?php
/**
 * SoccerMatchList Listing
 * @author  <your name here>
 */
class SoccerMatchList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_SoccerMatch');
        $this->form->setFormTitle('Todos os jogos');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status','=',1));
        $football_league_id = new TDBUniqueSearch('football_league_id','app','FootballLeague','id','slug',null,$criteria);
        $football_league_id->setMinLength(1);
        $soccer_team_master_id = new TDBUniqueSearch('soccer_team_master_id','app','SoccerTeam','id','slug');
        $soccer_team_master_id->setMinLength(1);
        $soccer_team_visiting_id = new TDBUniqueSearch('soccer_team_visiting_id','app','SoccerTeam','id','slug');
        $soccer_team_visiting_id->setMinLength(1);
        $hour = new TTime('hour');
        $date = new TDate('date');
        $status = new TCombo('status');
        $status->setDefaultOption(false);
        $status->addItems([
            '0' => 'Em espera',
            '1' => 'Iniciado',
            '2' => 'Suspenso',
            '3' => 'Adiado',
            '4' => 'Finalizado',
            '5' => 'Cancelado'
        ]);
        $ini = new TDate('ini');
        $end = new TDate('end');


        // add the fields
        $row = $this->form->addFields( [ new TLabel('Liga'), $football_league_id ],
                                [ new TLabel('Time Mandante'), $soccer_team_master_id ],
                                [ new TLabel('Time Visitante'), $soccer_team_visiting_id ],
                                [ new TLabel('Horario '), $hour ],
                                [ new TLabel('Status'), $status ],
                                [],
                                [ new TLabel('Jogos de'), $ini ],
                                [ new TLabel('até'), $end ] );

        $row->layout = ['col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-12','col-sm-2','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['SoccerMatchForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->disableDefaultClick();
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_football_league_id = new TDataGridColumn('football_league->league->slug', 'Liga', 'left');
        $column_soccer_team_master_id = new TDataGridColumn('soccer_team_master->slug', 'Mandante', 'left');
        $column_soccer_team_visiting_id = new TDataGridColumn('soccer_team_visiting->slug', 'Visitante', 'left');
        $column_hour = new TDataGridColumn('hour', 'Horario ', 'right');
        $column_date = new TDataGridColumn('date', 'Data', 'right');
        $column_score_master = new TDataGridColumn('score_master', 'P Mand', 'center');
        $column_score_visiting = new TDataGridColumn('score_visiting', 'P Visit', 'center');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'Criado em', 'right');
        $column_updated_at = new TDataGridColumn('updated_at', 'Ultima atualização', 'right');

        $column_status->setTransformer(function($value){
            switch ($value) {
                case 1:
                    $class = 'success';
                    $label = 'Iniciado';
                    break;
                case 2:
                    $class = 'warning';
                    $label = 'Suspenso';
                    break;
                case 3:
                    $class = 'primary';
                    $label = 'Adiado';
                    break;
                case 4:
                    $class = 'danger';
                    $label = 'Finalizado';
                    break;
                case 5:
                    $class = 'danger';
                    $label = 'Cancelado';
                    break;
                
                default:
                    $class = 'secondary';
                    $label = 'Em espera';
                    break;
            }

            $div = new TElement('span');
            $div->class = "btn btn-{$class}";
            $div->style = "text-shadow:none; font-size:12px; font-weight:bold;width:80px;";
            $div->add($label);
            return $div;
        });

        $column_date->setTransformer(function($value){
            return Convert::toDateBR($value);
        });
        
        $column_created_at->setTransformer(function($value){
            return Convert::toDateBR($value);
        });

        $column_updated_at->setTransformer(function($value){
            return Convert::toDateBR($value);
        });
        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_football_league_id);
        $this->datagrid->addColumn($column_soccer_team_master_id);
        $this->datagrid->addColumn($column_soccer_team_visiting_id);
        $this->datagrid->addColumn($column_hour);
        $this->datagrid->addColumn($column_date);
        $this->datagrid->addColumn($column_score_master);
        $this->datagrid->addColumn($column_score_visiting);
        $this->datagrid->addColumn($column_status);
        // $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);


        $action1 = new TDataGridAction(['SoccerMatchForm', 'onEdit'], ['id'=>'{id}']);
        $action1->setDisplayCondition( array($this, 'displayEditColumn') );
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        $action2->setDisplayCondition( array($this, 'displayDelColumn') );
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
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
     * Define when the action can be displayed
     */
    public function displayEditColumn( $object )
    {
        $array = TSession::getValue('usergroupids');
        if(!in_array(1,$array)){
            switch ($object->status) {
                case 1:
                    return true;
                    break;
                default:
                    return false;
                    break;
            }
        }else{
            return true;
        }
    }

    /**
     * Define when the action can be displayed
     */
    public function displayDelColumn( $object )
    {
        $array = TSession::getValue('usergroupids');
        if(!in_array(1,$array)){
            switch ($object->status) {
                case 0:
                    return true;
                    break;
                default:
                    return false;
                    break;
            }
        }else{
            return true;
        }
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
            $object = new SoccerMatch($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_football_league_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_soccer_team_master_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_soccer_team_visiting_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_date',   NULL);
        TSession::setValue(__CLASS__.'_filter_status',   NULL);

        if (isset($data->football_league_id) AND ($data->football_league_id)) {
            $filter = new TFilter('football_league_id', 'like', "%{$data->football_league_id}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_football_league_id',   $filter); // stores the filter in the session
        }


        if (isset($data->soccer_team_master_id) AND ($data->soccer_team_master_id)) {
            $filter = new TFilter('soccer_team_master_id', 'like', "%{$data->soccer_team_master_id}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_soccer_team_master_id',   $filter); // stores the filter in the session
        }


        if (isset($data->soccer_team_visiting_id) AND ($data->soccer_team_visiting_id)) {
            $filter = new TFilter('soccer_team_visiting_id', 'like', "%{$data->soccer_team_visiting_id}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_soccer_team_visiting_id',   $filter); // stores the filter in the session
        }


        if (isset($data->hour) AND ($data->hour)) {
            $filter = new TFilter('hour', 'like', "%{$data->hour}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_hour',   $filter); // stores the filter in the session
        }

        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_status',   $filter); // stores the filter in the session
        }

        if ((isset($data->ini) AND ($data->ini)) AND (isset($data->end) AND ($data->end))) {
            $filter = new TFilter('date', 'between', "{$data->ini}", "{$data->end}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_date',   $filter); // stores the filter in the session
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
            
            // creates a repository for SoccerMatch
            $repository = new TRepository('SoccerMatch');
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
            

            if (TSession::getValue(__CLASS__.'_filter_football_league_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_football_league_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_soccer_team_master_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_soccer_team_master_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_soccer_team_visiting_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_soccer_team_visiting_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_hour')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_hour')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_date')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_date')); // add the session filter
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
                    if($object->football_league->status == 1)
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
            $object = new SoccerMatch($key, FALSE); // instantiates the Active Record
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

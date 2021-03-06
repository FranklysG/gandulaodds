<?php
/**
 * FootballLeagueList Listing
 * @author  <your name here>
 */
class FootballLeagueList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_FootballLeague');
        $this->form->setFormTitle('Listagem de Campeonatos '.date('Y'));
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $slug = new TEntry('slug');
        $continent = new TEntry('continent');
        $status = new TCombo('status');
        $status->setDefaultOption(false);
        $status->addItems([
            '0' => 'Em espera',
            '1' => 'Iniciado',
            '2' => 'Suspenso',
            '3' => 'Finalizado',
            '4' => 'Cancelado'
        ]);
        $ini = new TDate('ini');
        $end = new TDate('end');


        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( [ new TLabel('Nome'), $name ],
                                [ new TLabel('Slug'), $slug ],
                                [ new TLabel('Continente'), $continent ],
                                [ new TLabel('de'), $ini ],
                                [ new TLabel('até'), $end ] );

        $row->layout = ['col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FootballLeagueForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_name = new TDataGridColumn('league->name', 'Nome', 'left');
        $column_slug = new TDataGridColumn('league->slug', 'Slug', 'left');
        $column_continent = new TDataGridColumn('continent', 'Continente', 'left');
        $column_shield = new TDataGridColumn('shield', 'Escudo', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'Criado em', 'left');
        $column_updated_at = new TDataGridColumn('updated_at', 'Atualização', 'left');

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
                    $class = 'danger';
                    $label = 'Finalizado';
                    break;
                case 3:
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
        $this->datagrid->addColumn($column_continent);
        $this->datagrid->addColumn($column_shield);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_created_at);
        // $this->datagrid->addColumn($column_updated_at);


        $action1 = new TDataGridAction(['FootballLeagueForm', 'onEdit'], ['id'=>'{id}']);
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
            $object = new FootballLeague($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_name',   NULL);
        TSession::setValue(__CLASS__.'_filter_slug',   NULL);
        TSession::setValue(__CLASS__.'_filter_continent',   NULL);
        TSession::setValue(__CLASS__.'_filter_date',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_name',   $filter); // stores the filter in the session
        }


        if (isset($data->slug) AND ($data->slug)) {
            $filter = new TFilter('slug', 'like', "%{$data->slug}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_slug',   $filter); // stores the filter in the session
        }


        if (isset($data->continent) AND ($data->continent)) {
            $filter = new TFilter('continent', 'like', "%{$data->continent}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_continent',   $filter); // stores the filter in the session
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
            
            // creates a repository for FootballLeague
            $repository = new TRepository('FootballLeague');
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
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_name')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_name')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_slug')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_slug')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_continent')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_continent')); // add the session filter
            }

            if (TSession::getValue(__CLASS__.'_filter_date')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_date')); // add the session filter
            }

            $criteria->add(new TFilter('status','<=',2));
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
                        $image = new TImage("tmp/shield_football_league/{$object->shield}");
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
            $object = new FootballLeague($key, FALSE); // instantiates the Active Record
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

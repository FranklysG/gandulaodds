<?php
/**
 * SoccerMatchForm Form
 * @author  <your name here>
 */
class SoccerMatchForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_SoccerMatch');
        $this->form->setFormTitle('Jogo');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new THidden('id');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status','=',1));
        $football_league_id = new TDBUniqueSearch('football_league_id','app','FootballLeague','id','slug',null,$criteria);
        $football_league_id->addValidation('Campeonato', new TRequiredValidator);
        $football_league_id->setMinLength(1);
        $soccer_team_master_id = new TDBUniqueSearch('soccer_team_master_id','app','SoccerTeam','id','slug');
        $soccer_team_master_id->addValidation('Time Mandante', new TRequiredValidator);
        $soccer_team_master_id->setMinLength(1);
        $soccer_team_visiting_id = new TDBUniqueSearch('soccer_team_visiting_id','app','SoccerTeam','id','slug');
        $soccer_team_visiting_id->addValidation('Time Visitante', new TRequiredValidator);
        $soccer_team_visiting_id->setMinLength(1);
        $hour = new TTime('hour');
        $hour->addValidation('Horario', new TRequiredValidator);
        $date = new TDate('date');
        $date->addValidation('Data Jogo', new TRequiredValidator);
        $score_master = new TEntry('score_master');
        $score_master->setMask('9');
        $score_master->addValidation('Placar Mandante', new TRequiredValidator);
        $score_visiting = new TEntry('score_visiting');
        $score_visiting->setMask('9');
        $score_visiting->addValidation('Placar Visitante', new TRequiredValidator);
        $status = new TCombo('status');
        $status->addValidation('Status', new TRequiredValidator);
        $status->setDefaultOption(false);
        $status->addItems([
            '0' => 'Em espera',
            '1' => 'Iniciado',
            '2' => 'Suspenso',
            '3' => 'Adiado',
            '4' => 'Finalizado',
            '5' => 'Cancelado'
        ]);
        $created_at = new TDate('created_at');
        $updated_at = new TEntry('updated_at');


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ new TLabel('Campeonato'),$football_league_id ] );
        $row = $this->form->addFields( [ new TLabel('Time Mandante'),$soccer_team_master_id ],
                                        [ new TLabel('Time Visitante'),$soccer_team_visiting_id ] );
        $row->layout = ['col-sm-6','col-sm-6'];
        $row = $this->form->addFields( [ new TLabel('Placar Mandante'),$score_master ],
                                    [ new TLabel('Placar Visitante'),$score_visiting ],
                                    [ new TLabel('Horario'),$hour ],
                                    [ new TLabel('Data do jogo'),$date ]);
        $row->layout = ['col-sm-6','col-sm-6','col-sm-6','col-sm-6'];
        $this->form->addFields( [ new TLabel('Status'),$status ] );

        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary'; 
        $this->form->addHeaderActionLink( _t('Close'), new TAction(array($this, 'onClose')), 'fa:times red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            
            TTransaction::open('app'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            $object = new SoccerMatch;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            if($data->soccer_team_master_id != $data->soccer_team_visiting_id){
                $object->store();

                $score_table_master = SoccerTable::where('soccer_match_id','=',$data->id)->where('soccer_team_id','=',$data->soccer_team_master_id)->load();
                $score_table_master = array_shift($score_table_master);
                if(!$score_table_master)
                    $score_table_master = new SoccerTable;
                
                $score_table_visiting = SoccerTable::where('soccer_match_id','=',$data->id)->where('soccer_team_id','=',$data->soccer_team_visiting_id)->load();
                $score_table_visiting = array_shift($score_table_visiting);
                if(!$score_table_visiting)
                    $score_table_visiting = new SoccerTable;
                
                $score_table_master->soccer_team_id = $data->soccer_team_master_id;
                $score_table_master->soccer_match_id = $object->id;
                $score_table_visiting->soccer_team_id = $data->soccer_team_visiting_id;
                $score_table_visiting->soccer_match_id = $object->id;

                if($data->score_master == $data->score_visiting){
                    $score_table_master->win = 0;
                    $score_table_master->draw = 1;
                    $score_table_master->los = 0;
                    $score_table_master->pro_goal = $data->score_master;
                    $score_table_master->own_goal = $data->score_visiting;          
    
                    $score_table_visiting->win = 0;
                    $score_table_visiting->draw = 1;
                    $score_table_visiting->los = 0;
                    $score_table_visiting->pro_goal = $data->score_visiting;
                    $score_table_visiting->own_goal = $data->score_master;
                }
                if($data->score_master > $data->score_visiting){
                    $score_table_master->win = 1;
                    $score_table_master->draw = 0;
                    $score_table_master->los = 0;
                    $score_table_master->pro_goal = $data->score_master;
                    $score_table_master->own_goal = $data->score_visiting;          
    
                    $score_table_visiting->win = 0;
                    $score_table_visiting->draw = 0;
                    $score_table_visiting->los = 1;
                    $score_table_visiting->pro_goal = $data->score_visiting;
                    $score_table_visiting->own_goal = $data->score_master;
                }
                if($data->score_master < $data->score_visiting){
                    $score_table_master->win = 0;
                    $score_table_master->draw = 0;
                    $score_table_master->los = 1;
                    $score_table_master->pro_goal = $data->score_master;
                    $score_table_master->own_goal = $data->score_visiting;          
    
                    $score_table_visiting->win = 1;
                    $score_table_visiting->draw = 0;
                    $score_table_visiting->los = 0;
                    $score_table_visiting->pro_goal = $data->score_visiting;
                    $score_table_visiting->own_goal = $data->score_master;
                }

                $score_table_master->store();
                $score_table_visiting->store();

                // get the generated id
                $data->id = $object->id;
                
                $this->form->setData($data); // fill form data
                TTransaction::close(); // close the transaction
                
                
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'),new TAction(['SoccerMatchList','onReload']));
       
            }else{
                new TMessage('warning','Times Iguais não competem'); // shows the exception error message
            }
           
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
           
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('app'); // open a transaction
                $object = new SoccerMatch($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}

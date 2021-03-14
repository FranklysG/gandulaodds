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
        $football_league_id = new TDBUniqueSearch('football_league_id','app','FootballLeague','id','slug');
        $football_league_id->setMinLength(1);
        $soccer_team_master_id = new TDBUniqueSearch('soccer_team_master_id','app','SoccerTeam','id','slug');
        $soccer_team_master_id->setMinLength(1);
        $soccer_team_visiting_id = new TDBUniqueSearch('soccer_team_visiting_id','app','SoccerTeam','id','slug');
        $soccer_team_visiting_id->setMinLength(1);
        $hour = new TDateTime('hour');
        $date_game = new TDate('date_game');
        $score_master = new TEntry('score_master');
        $score_visiting = new TEntry('score_visiting');
        $status = new TCombo('status');
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
                                    [ new TLabel('Data do jogo'),$date_game ]);
        $row->layout = ['col-sm-6','col-sm-6','col-sm-6','col-sm-6'];
        $this->form->addFields( [ new TLabel('Status'),$status ] );

        if (!isset($id))
        {
            $id->setEditable(FALSE);
            $football_league_id->setEditable(FALSE);
            $soccer_team_master_id->setEditable(FALSE);
            $soccer_team_visiting_id->setEditable(FALSE);
            $hour->setEditable(FALSE);
            $date_game->setEditable(FALSE);
        }
        
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
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'),new TAction(['SoccerMatchList','onReload']));
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

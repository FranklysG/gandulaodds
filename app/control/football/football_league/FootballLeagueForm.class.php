<?php
/**
 * FootballLeagueForm Form
 * @author  <your name here>
 */
class FootballLeagueForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_FootballLeague');
        $this->form->setFormTitle('Campeonatos de Fotebol');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $league = new TDBUniqueSearch('league_id','app','League','id','slug');
        $league->addValidation('Campeonato', new TRequiredValidator);
        $league->setMinLength(1);
        $season = new TEntry('season');
        $shield = new TFile('shield');
        $shield->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );
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
        $date_ini = new TDate('date_ini');
        $date_end = new TDate('date_end');

        $this->frame = new TElement('div');
        $this->frame->id = 'shield_frame';
        $this->frame->style = 'width:100px;height:auto;;border:1px solid gray;padding:4px;';



        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( [ new TLabel('Tipo campeonato'), $league ], [ new TLabel('Temporada'), $season ] );
        $row->layout = ['col-sm-8','col-sm-4'];
        $row = $this->form->addFields( [ new TLabel('Inicio'), $date_ini ], [ new TLabel('Final'), $date_end ] );
        $row->layout = ['col-sm-6','col-sm-6'];
        $this->form->addFields( [ new TLabel('Continente'), $continent ] );
        $this->form->addFields( [ new TLabel('status'), $status ] );
        $this->form->addFields( [ new TLabel('Escudo'), $shield ] );
        $this->form->addFields( [ new TLabel(''), $this->frame ] );


        if (!empty($id))
        {
            $id->setEditable(FALSE);
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
            
            $object = new FootballLeague;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // paste img in another folter
            AppUtil::paste_another_folder($data->shield, 'shield_football_league');
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['FootballLeagueList', 'onReload']));
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
                $object = new FootballLeague($key); // instantiates the Active Record
                if (isset($object->shield)) {
                    $image = new TImage("tmp/shield_football_league/{$object->shield}");
                    $image->style = 'width: 100%';
                    $this->frame->add($image);
                }
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

    public static function onComplete($param)
    {
        // refresh photo_frame
        $shield = PATH."/tmp/shield_football_league/{$param['shield']}";
        TScript::create("$('#shield_frame').html('')");
        TScript::create("$('#shield_frame').append(\"<img style='width:100%' src='$shield'>\");");
    }
}

<?php
/**
 * PublicView
 *
 * @version    1.0
 * @package    service
 * @subpackage 
 * @author     Franklys Guimaraes
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd.
 * @license    http://www.adianti.com.br/framework-license
 */


class SetGameState extends TPage
{
    public function __construct()
    {
        parent::__construct();
               
    }

    // muda o status do jogo de esperando para iniciado
    public function setGameState(){
        try {
            TTransaction::open('app');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('status','=',0));
            $criteria->add(new TFilter('date_format(date, "%Y-%m-%d")','=',date('Y-m-d')));
            $criteria->add(new TFilter('date_format(hour, "%H:%i")','<=',date('H:i')));
            $repository = new TRepository('SoccerMatch');
            $objects = $repository->load($criteria);
            if(isset($objects)){
                foreach ($objects as $object) {
                    $object->status = 1;
                    $object->store();
                }
            }
            TTransaction::close();
        } catch (Exeption $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

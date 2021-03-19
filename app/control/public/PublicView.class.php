<?php
/**
 * PublicView
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class PublicView extends TPage
{
    public function __construct()
    {
        parent::__construct();
        try {
            TTransaction::open('app');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('date_format(date, "%Y-%m-%d")','=',date('Y-m-d')));
            $criteria->add(new TFilter('date_format(hour, "%H")','=',date('H:i')));
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
            new TMessage('warning', $e->getMessage());
        }
        
    }
}

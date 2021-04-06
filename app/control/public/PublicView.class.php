<?php
require_once('request.php');
Class PublicView extends TPage {
    
    public function __construct(){

        parent::__construct();

        try
        {
            $location = 'http://localhost/AdiantiProjects/api.gandulaodds.com/soccer-match/1';
            print_r(request($location, 'GET') );
        }
        catch (Exception $e)
        {
            new TMessage('warning', $e->getMessage());
        }
    }
}
?>
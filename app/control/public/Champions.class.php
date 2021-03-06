<?php
/**
 * Champion
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class Champions extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        try {
            TTransaction::open('app');
            $preference = SystemPreference::getAllPreferences();
            TTransaction::close();
     
            $url = "{$preference['url_base']}/campeonatos/";
            $token = $preference['token_teste'];
            $champions = AppUtil::url_get_contents($url, $token);
            
            $html = new THtmlRenderer('app/resources/app/champion.html');
            $card = new THtmlRenderer('app/resources/app/card-champion.html');  
            
            $data = [];
            foreach ($champions as $value) {
                $data['card'][] = [
                    'title' => $value->nome,
                    'img' => $value->logo,
                    'background' => 'orange',
                    'value' => $value->campeonato_id
                ];
            }
            
            $card->enableSection('main', $data);
            $html->enableSection('main', ['card' => $card] );           
            parent::add($html);
        } catch (Exeption $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

}

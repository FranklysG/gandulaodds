<?php
/**
 * GameMatchToDay
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */


class GameMatchToDay extends TPage
{
    public function __construct()
    {
        parent::__construct();
        try {
            TTransaction::open('app');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('status','=',1));
            $repository = new TRepository('FootballLeague');

            $objects = $repository->load($criteria);
            
            $football_league = array();
            if(isset($objects)){
                foreach ($objects as $object) {
                    $data = array();
                    foreach ($object->getSoccerMatchs() as $value) {
                        switch ($value->status) {
                            case 1:
                                $class = 'success';
                                $label = 'Ao vivo';
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
                        $data[] = array(
                            'soccer_team_master' => $value->soccer_team_master->slug,
                            'soccer_team_master_shield' => $value->soccer_team_master->shield,
                            'soccer_team_master_score' => $value->score_master,
                            'soccer_team_visiting' => $value->soccer_team_visiting->slug,
                            'soccer_team_visiting_shield' => $value->soccer_team_visiting->shield,
                            'soccer_team_visiting_score' => $value->score_visiting,
                            'soccer_match_hour' => Convert::toHourBR($value->hour),
                            'soccer_match_date' => Convert::toDateBR($value->date),
                            'soccer_match_status' => [[
                                'class' => $class,
                                'label' => $label
                            ]],
                        ); 
                    }
                    $football_league['football_league'][] = array(
                        'id' => $object->id,
                        'football_league_slug' => $object->slug,
                        'match' => $data
                    );

                }
            }

            TTransaction::close();
            $header = new THtmlRenderer('app/resources/app/soccer_match.html');
            $header->enableSection('main', $football_league);
            parent::add($header);
        } catch (Exeption $e) {
            new TMessage('warning', $e->getMessage());
        }
        
    }
}
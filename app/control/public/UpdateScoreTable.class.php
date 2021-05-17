<?php
Class UpdateScoreTable extends TPage {
    
    public function __construct(){

        parent::__construct();

        try
        {
            TTransaction::open('app');

            $object = SoccerMatch::getObjects();
            if(!empty($object)){
                foreach ($object as $data) {
                    $score_table_master = SoccerTable::where('soccer_match_id','=',$data->id)->where('soccer_team_id','=',$data->soccer_team_master_id)->load();
                    $score_table_master = array_shift($score_table_master);
                    if(!$score_table_master)
                        $score_table_master = new SoccerTable;
                    
                    $score_table_visiting = SoccerTable::where('soccer_match_id','=',$data->id)->where('soccer_team_id','=',$data->soccer_team_visiting_id)->load();
                    $score_table_visiting = array_shift($score_table_visiting);
                    if(!$score_table_visiting)
                        $score_table_visiting = new SoccerTable;
                    
                    $score_table_master->soccer_team_id = $data->soccer_team_master_id;
                    $score_table_master->soccer_match_id = $data->id;
                    $score_table_visiting->soccer_team_id = $data->soccer_team_visiting_id;
                    $score_table_visiting->soccer_match_id = $data->id;

                    if($data->score_master == $data->score_visiting){
                        $score_table_master->draw = 1;
                        $score_table_visiting->draw = 1;    
                    }else if($data->score_master > $data->score_visiting){
                        $score_table_master->win = 1;
                        $score_table_visiting->los = 1;

                    }else if($data->score_master < $data->score_visiting){
                        $score_table_master->los = 1;     
                        $score_table_visiting->win = 1;
                    }

                    $score_table_master->pro_goal = $data->score_master;
                    $score_table_master->own_goal = $data->score_visiting; 
                    $score_table_visiting->pro_goal = $data->score_visiting;
                    $score_table_visiting->own_goal = $data->score_master;
                    $score_table_master->store();
                    $score_table_visiting->store();
                }
            }
            
            new TMessage('info', 'ScoreTable Atualizada', new TAction(['SoccerMatchList','onReload']));
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('warning', $e->getMessage());
        }
    }
}
?>
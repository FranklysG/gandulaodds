<?php

class LocationNotify {

    public function __construct(){

    }

    public function sendNotify(){
        try {
            TTransaction::open('appmobile');

            $repository = new TRepository('Travel');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('date(created_at)','=',date('Y-m-d')));
            // $criteria->setProperty('limit',1);

            $objects = $repository->load($criteria);

            $lat_ini = ''; 
            $lat_end = ''; 
            $profile_travel = [];
            foreach ($objects as $object) {
                $profile_travel[$object->profile_id] = [
                    'profile_id' => $object->profile_id,
                    'profile_user_id' => Profile::find($object->profile_id)->user_id,
                    'final_lat' => (float)$object->final_lat,
                    'final_lat_sum' => (float)$object->final_lat + 0.1,
                    'final_long' => (float)$object->final_long,
                    'final_long_sum' => (float)$object->final_long + 0.1
                ];
            }
            
            $repository = new TRepository('Profile');
            $criteria = new TCriteria;
            // var_dump($profile_travel);
            foreach ($profile_travel as $value) {
                $criteria->add(new TFilter('profile_type_id','=','23bdaf88-0fff-4c0e-9cda-93dc180ac6b9'));
                $criteria->add(new TFilter('lat','between',$value['final_lat'] , $value['final_lat_sum']));
                $criteria->add(new TFilter('long','between',$value['final_long'] , $value['final_long_sum']));
                // $criteria->setProperty('limit',1);
                $objects = $repository->load($criteria);
                $profile_to_notify = [];
                if($objects){
                    $name = '';
                    foreach ($objects as $object) {
                        $name = (empty($object->alias))?: $object->name;   
                    }

                    $get_token = AppFirebase::getUserToken();
                    $token = array_search($value['profile_user_id'], $get_token);
                    $title = "Você passou por {$name} ? ✅";
                    $body = "📝 Não esqueça da sua avaliação ela muito importante para nós";
                    // por fim, se tiver token ele envia a notificação pro dono da postagem
                    if(!empty($token)){
                        // echo 'token ás '.date('H:i:s').'<br>';
                        $param = [
                            'title' => $title,
                            'body' => $body,
                            'image' => 'https://elogbr.com/wp-content/uploads/2020/06/cropped-logo-1.png',
                            'post_id' => '',
                            // 'token' => 'fi_5WSbptPg:APA91bEJ3Gt6L_FS3KM583E_9mxeLCf_96Z4McF6Gp6rtrttjUNVNbE3FP7BPS_NGVUe3T6HOgmd5C3XCmrCORVfJanOfKNuzupAFThfq4rrFnomrx0xevQ1-G7Fgnkqc9VZwmZnU-6i' //tunele
                            'token' => $token // token do usuario que fez a postagem
                        ];
                        AppFirebase::sendNotificationSpecific($param);
                    };
                }
            }

            TTransaction::close();
            
        } catch (Exeption $e) {
            new TMessage('erro', $e->getMessage());
        }
    }
}

?>
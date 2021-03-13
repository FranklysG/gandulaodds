<?php 

class NotificationElog extends TElement
{

    public static function __contruct(){
        
    }

    public function sendCountPostNotify(){
        try {
            TTransaction::open('appmobile');

            $repository = new TRepository('Post');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('date(created_at)','=', date('Y-m-d',strtotime("-1 days"))));
            $numPost = $repository->count($criteria);;
            
            $param = [
                'title' => 'Nova postagem 🚛 🚜 !',
                'body' => "Tivemos {$numPost} Nova(s) postagens enquanto você esteve fora 🤭 😁",
                'image' => '',
                'topic' => 'all'
            ];
            if($numPost > 0){
                AppFirebase::sendNotificationForAll($param);
            }
            TTRansaction::close();
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    public function sendNewActivityNotify(){
       try{
            TTransaction::open('appmobile');

            // chama o repositorio dos like
            $repository = new TRepository('Activity');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('notified','=', false));
            $criteria->add(new TFilter('date(created_at)','=', date('Y-m-d')));
            
            $objects = $repository->load($criteria);
            
            if(isset($objects)){
                // echo 'objects ás '.date('H:i:s').'<br>';
                $activity = [];
                foreach($objects as $object){
                    $activity[] = [
                        'activity_id' => $object->id,
                        'from_id' => $object->from_id,
                        'to_id' => $object->to_id,
                        'post_id' => $object->post_id,
                        'type' => $object->type,
                    ];
                }
            
                if(!empty($activity)){

                    $get_token = AppFirebase::getUserToken();
                    // echo 'if ás '.date('H:i:s').'<br>';
                    foreach ($activity as $object) {
                        // escreve os dois ou 3 primeiros nome de quem curtiu a postagem
                        $profile_from = Profile::find($object['from_id']);
                        $profile_to = Profile::find($object['to_id']);
                        
                        // pega o id do usuario dono da postagem
                        // e o user_id pra ir buscar o token dele lá no firebase
                        
                        switch ($object['type']) {
                            case 'LK':
                                $title = 'Nova curtida 👍 no seu post 🤭';
                                $body = "{$profile_from->alias} acabou de curtir seu post";
                                $url = "/posts/post/{$object['post_id']}";
                                break;
                            case 'FR':
                                $title = 'Uma nova amizade 🤝 foi formada 👏';
                                $body = "{$profile_from->alias} adicionou você na lista de amigos";
                                $url = "/profile/user/{$object['from_id']}";    
                            break;
                            case 'CT':
                                $title = 'Um novo comentario ✍ no seu post';
                                $body = "{$profile_from->alias} adicionou um comentario no seu post";
                                $url = "/posts/comment/{$object['post_id']}/{$object['to_id']}";
                                break;
                            case 'CR':
                                $title = 'Resposta ao seu comentario ✍ ';
                                $body = "{$profile_from->alias} adicionou uma resposta no seu comentario";    
                                $url = "/posts/comment/{$object['post_id']}/{$object['to_id']}";
                                break;
                        }

                        
                        // por fim, se tiver token ele envia a notificação pro dono da postagem
                        if($profile_from->id != $profile_to->id){
                            
                            $token = array_search($profile_to->user_id, $get_token);
                            
                            if(!empty($token)){
                                // echo 'token ás '.date('H:i:s').'<br>';
                                $param = [
                                    'title' => $title,
                                    'body' => $body,
                                    'image' => 'https://elogbr.com/wp-content/uploads/2020/06/cropped-logo-1.png',
                                    'url' => $url,
                                    // 'token' => 'fi_5WSbptPg:APA91bEJ3Gt6L_FS3KM583E_9mxeLCf_96Z4McF6Gp6rtrttjUNVNbE3FP7BPS_NGVUe3T6HOgmd5C3XCmrCORVfJanOfKNuzupAFThfq4rrFnomrx0xevQ1-G7Fgnkqc9VZwmZnU-6i' //tunele
                                    'token' => $token // token do usuario que fez a postagem
                                ];

                                AppFirebase::sendNotificationSpecific($param);
                                
                                $activity = Activity::find($object['activity_id']);
                                $activity->notified = true;
                                $activity->store();
                            }; 
                        }else{
                            $activity = Activity::find($object['activity_id']);
                            $activity->notified = true;
                            $activity->store();
                        } 
                    }  
                }     
            }
            TTRansaction::close();
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }
    
    // enviar notificação para todos os perfis que não tem descrição 
    public function sendNotifyToNoDesc(){
        try {
            
            TTransaction::open('appmobile');
            $repository = new TRepository('Profile');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('profile_type_id','=', 'ff9d7200-c90e-4d4b-8693-fe46c592ece1'));
            $criteria->add(new TFilter('alias','is not', null));
            $criteria->add(new TFilter('description','is', null));
            // $criteria->add(new TFilter('user_id','=', 'pAOgKfF4rwXNiskQqjJ7FTUgalm2'));
            // $criteria->setProperty('limit',4);
            $objects = $repository->load($criteria);
            TTRansaction::close();
            
            if($objects){
                $get_token = AppFirebase::getUserToken();
                foreach ($objects as $object) {
                    if(!empty($object->alias)){
                        $token = array_search($object->user_id, $get_token);
                        if(isset($token)){
                            $param = [
                                'title' => "Olá {$object->alias} !",
                                'body' => "Que tal completar a descrição do seu perfil 🤭 😁",
                                'image' => 'https://elogbr.com/wp-content/uploads/2020/06/cropped-logo-1.png',
                                'url' => "/profile/user/{$object->id}",
                                'token' => $token
                            ];

                            AppFirebase::sendNotificationSpecific($param);
                        }
                    }

            }
        }
            
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    // enviar notificação para todos os perfis que não tem descrição 
    public function sendNotifyToUserNoPost(){
        try {
            TTransaction::open('appmobile');
            // creates a repository for Post
            $repository = new TRepository('ViewProfileNoPost');
            $limit = 10;
            
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperty('limit', $limit);

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            if($objects){
                $get_token = AppFirebase::getUserToken();
                foreach ($objects as $object) {
                    $token = array_search($object->user_id, $get_token);
                    if(isset($token)){
                        $param = [
                            'title' => "Olá {$object->alias} !",
                            'body' => "Que tal adicionar uma foto no seu perfil 🤭 😁",
                            'image' => 'https://elogbr.com/wp-content/uploads/2020/06/cropped-logo-1.png',
                            'url' => '/posts/add',
                            'token' => $token
                        ];
    
                        AppFirebase::sendNotificationSpecific($param);
                    }
        
                }
            }
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('erro',$e->getMessage());
        }
    }

    /**
     * enviar as notificações que estão listadas na tabela de notification_app
     * na pagina ManualNotification
     * passando como parametro a data de hoje como dia da semana
     * */
    public function sendNotifyToTableNotification(){
        try {
            TTransaction::open('appweb');
            // creates a repository for Post
            $repository = new TRepository('NotificationApp');
            $limit = 10;
            
            $data = date('Y-m-d');
            $dia_semana_numero = date('w', strtotime($data));

            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('time','=', date('H:00')));

            $criteria2 = new TCriteria; 
            $criteria2->add(new TFilter('remand','=', $dia_semana_numero)); 
            $criteria2->add(new TFilter('remand','=', 8)); 

            $criteria = new TCriteria;     
            $criteria->add($criteria1, TExpression::OR_OPERATOR); 
            $criteria->add($criteria2, TExpression::OR_OPERATOR); 
            $criteria->setProperty('limit', $limit);

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            if($objects){
                foreach ($objects as $object) {
                    $param = [
                        'title' => $object->title,
                        'body' => $object->body,
                        'image' => 'https://elogbr.com/wp-content/uploads/2020/06/cropped-logo-1.png',
                        'url' => $object->url.$object->filter,
                        'topic' => $object->topic
                    ];
    
                    if($param['topic'] != 'all'){
                        AppFirebase::sendNotificationSpecific($param);
                    }else{
                        AppFirebase::sendNotificationForAll($param);
                    }
                }
            }
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('erro',$e->getMessage());
        }
    }

    /**
     * enviar as notificações para os aniversariantes da elog
     * */
    public function sendNotifyToBirthDay(){
        try {
            TTransaction::open();
            // creates a repository for Post
            $repository = new TRepository('ViewBirthDay');
        
            // load the objects according to criteria
            $objects = $repository->load();
            
            if($objects){
                $get_token = AppFirebase::getUserToken();
                foreach ($objects as $object) {
                
                    $token = array_search($object->user_id, $get_token);
                    if(isset($token)){
                        
                        $param = [
                            'title' => "🎂 {$object->alias} Hoje é o seu dia 🎁",
                            'body' => "Nós da ELOG 🎉, desejamos um feliz aniversario 🥳",
                            'image' => 'https://elogbr.com/wp-content/uploads/2020/06/cropped-logo-1.png',
                            'url' => '/posts/add',
                            'token' => $token
                        ];
    
                        AppFirebase::sendNotificationSpecific($param);
                    }
                }
            }
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('erro',$e->getMessage());
        }
    }

}

?>
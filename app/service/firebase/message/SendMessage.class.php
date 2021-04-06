<?php

class SendMessage{
    
    // verifica o banco e salva os dados na tabela de mensagem
    public static function setMessage(){

        try {
            TTransaction::open('appmobile');
            
            $repository = new TRepository('ViewSendEmail');
            $viewSendEmail = [];
            $ProfileServiceRating_id = [];
            $Rating_id = [];
            $criteria = new TCriteria;
            $criteria->add(new TFilter('profile_id','is not',null));
            $criteria->add(new TFilter('profile_service_rating_id','is not',null));
            // $criteria->add(new TFilter('rating_id','is not',null));
            // $criteria->setProperty('limit', 33);
            
            $objects = $repository->load($criteria, FALSE);
            
            foreach ($objects as $value) {
                $ProfileServiceRating_id[$value->profile_service_rating_rated_id][$value->profile_service_rating_rater_id] = [
                    'profile_id' => $value->profile_id,
                    'rated_id' => $value->profile_service_rating_rated_id,
                    'rater_id' => $value->profile_service_rating_rater_id,
                    'name' => $value->name,
                    'email' => $value->email
                ];
            }
            
            self::sendMail($ProfileServiceRating_id);
            TTransaction::close();
        } 
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }


    // enviar o email 
    public static function sendMail($ProfileServiceRating_id){
        
        try {

            TTransaction::open('permission');
            $preferences = SystemPreference::getAllPreferences();
            $logo = $preferences['logo'];
            TTransaction::close();

            TTransaction::open('appmobile');

            
            foreach ($ProfileServiceRating_id as $key) {
                foreach ($key as $value) {
                    
                    $nome = '';
                    $email = '';
                    $rating = '';
                    $coment = '';
                    $subject = 'Você teve uma nova avaliação no app Elog Brasil';
                    $url = "https://app.elogbr.com";
            
                    $nome = $value['name'];
                    $email = $value['email'];
                    $id = $value['profile_id'];
                    
                    $rating = new THtmlRenderer('app/resources/rating_services_emails.html');        
                    $profile_service = ProfileService::where('profile_id','=',$id)->load();
                    $replace = [];
                    foreach($profile_service as $object){
                        $service = Service::find($object->service_id);
                        $positivo = ProfileServiceRating::where('rated_id','=',$id)->where('service_id','=',$object->service_id)->where('value','=',1)->count();
                        $meio = ProfileServiceRating::where('rated_id','=',$id)->where('service_id','=',$object->service_id)->where('value','=',0)->count();
                        $negativo = ProfileServiceRating::where('rated_id','=',$id)->where('service_id','=',$object->service_id)->where('value','=',-1)->count();
                        $replace['rating'][] = [
                            'title' => $service->title,
                            'pos' => $positivo,
                            'meio' => $meio,
                            'neg' => $negativo
                        ];
                    }

                    $rating->enableSection('main', $replace);// $nome = Profile::find($value['profile_service_rating_id'])->name;
                    
                    $rating_coment = Rating::where('rated_id','=',$value['rated_id'])->where('rater_id','=',$value['rater_id'])->where('sended','=','false')->load();
                    if(isset($rating_coment)){
                        foreach($rating_coment as $check){
                            $check->sended = true;
                            $check->store();

                            // salvo o pessoal na tabela de mesnsagem pra depois enviar os email 
                            $mes = new Message;
                            $mes->type = 'email';
                            $mes->sended_at = date('Y-m-d H:i:s');
                            $mes->profile_service_rating_id = '';
                            $mes->rating_id = $check->id;
                            $mes->store();

                            $coment = $check->content;

                        }
                    }

                    // $email = 'franklys@appmake.com.br';               
                    
                    if((isset($preferences['smtp_auth'])) and (!empty($nome)) and (!empty($email))){
                        $replaces = [];
                        $replaces['EMPRESA'] = $nome;
                        $replaces['EMAIL'] = $email;
                        $replaces['URL'] = $url;
                        $replaces['LOGO'] = (isset($logo))? "https://www.elogbr.com/admin/tmp/$logo": " ";
                        $replaces['RATING'] = $rating;
                        $replaces['COMENT'] = $coment;
                        $html = new THtmlRenderer('app/resources/info_new_rating.html');
                        $html->enableSection('main', $replaces);
                        MailService::send($email, $subject , $html->getContents(), 'html' );

                        // precisa marcar a caixa de enviado de todos do profile service que tem esse id do cara
                        
                        $ProfileServiceRating = ProfileServiceRating::where('rated_id','=',$value['rated_id'])->where('rater_id','=',$value['rater_id'])->where('sended','=','false')->load();
                        if(isset($ProfileServiceRating)){
                            foreach($ProfileServiceRating as $check){
                                $check->sended = true;
                                $check->store();

                                // salvo o pessoal na tabela de mesnsagem pra depois enviar os email 
                                $mes = new Message;
                                $mes->type = 'email';
                                $mes->sended_at = date('Y-m-d H:i:s');
                                $mes->profile_service_rating_id = $check->id;
                                $mes->rating_id = '';
                                $mes->store();
                            }
                        }
                    }
                }
            }

            


            TTransaction::close();
            // new TMessage('info', "Chamado em andamento enviamos um Email com seu numero de Protocolo <strong>$protocolo</strong>", new TAction(['VerChamadoForm','onClear']));
            
        } catch(Exception $e) {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage() );
        }
    }
}
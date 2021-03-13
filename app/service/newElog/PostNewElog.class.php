<?php 

class PostNewElog extends TElement
{

    public static function __contruct(){
        
    }

    // faz a postagem diariamente pelo cron no perfil da elog
    public static function sendPostApp(){
        try {
            TTransaction::open('appweb');
            
            $news = NewElog::where('status','=',1)->load();
            $news = array_shift($news);
            if(!isset($news)){
                $news = NewElog::getObjects();
                foreach($news as $new){
                    $new->status = 1;
                    $new->store();
                }
            }else{
                $news->qtd += 1;
                $news->status = 0;
                $news->store();
            }

            TTransaction::close();
           
            if(isset($new->url)){
                $url = "{$new->url}/feed";
                $path = "tmp/noticias";
                
                $args = (object) WriteXML::getNewElog($url);
                $fonte = $args->fonte;
                $legenda = $args->title;
                $link = $args->link;
                $img = $args->img;
            
                if(empty($img)){
                    $img = 'https://elogbr.com/wp-content/uploads/2020/11/Aplicayivo-de-frete-2-930x620.jpg';
                }

                $ext = pathinfo($img, PATHINFO_EXTENSION);
                $name_firebase_photo = AppUtil::guid().'.'.$ext;

                // copia a imagem pra um caminho especifico
                if( !@copy( $img, "{$path}/{$name_firebase_photo}" ) ) {
                    $errors = error_get_last();
                    echo "COPY ERROR: ".$errors['type'];
                    echo "<br />\n".$errors['message'];
                } else {

                    // configurações de caminho
                    $bucketName = 'elog-social.appspot.com';
                    $objectName = "post-images/{$name_firebase_photo}";
                    $source = "{$path}/{$name_firebase_photo}";

                    // Classe pra redimencionar imagem app/lib/util
                    $resizeObj = new ResizeImg($source);
                    $resizeObj->resizeImage(600, 600, 'crop');
                    $resizeObj->saveImage($source, 100);

                    // Envia a imagem para pasta especifica do storage do firebase
                    AppFirebase::upload_image($bucketName, $objectName, $source);
                }      

                $profile_id = '4b62fde9-c968-4e1f-aab8-dfb777a7727b'; // Perfil Elog
                $user_id = 'emP9GlCPBuYbbf68o5U4c9oAAqG3';  // Perfil Elog
                // $profile_id = '2f1e5716-d812-442e-ae53-05672711bb07';  // Perfil tunele
                // $user_id = 'pAOgKfF4rwXNiskQqjJ7FTUgalm2';  // Perfil tunele


                $content = "{$legenda}\n\nFonte: {$fonte}\nLeia mais: {$link}";

                TTransaction::open('appmobile');
                $post = new Post;
                $post->content = $content;
                $post->profile_id = $profile_id;
                $post->type = 'P';
                $post->store();

                $media = new Media;
                $media->url = $name_firebase_photo;
                $media->type = 'P';
                $media->store();

                $post_media = new PostMedia;
                $post_media->post_id = $post->id;
                $post_media->media_id = $media->id;
                $post_media->store();

                $param = [
                    'title' => 'ELOG NOTICIAS 🗞️ 🤭 !',
                    'body' => "{$legenda} : Leia mais",
                    'image' => '',
                    'url' => "/posts/post/{$post->id}",
                    'topic' => 'all',
                    // 'token' => 'fi_5WSbptPg:APA91bEJ3Gt6L_FS3KM583E_9mxeLCf_96Z4McF6Gp6rtrttjUNVNbE3FP7BPS_NGVUe3T6HOgmd5C3XCmrCORVfJanOfKNuzupAFThfq4rrFnomrx0xevQ1-G7Fgnkqc9VZwmZnU-6i' //tunele
                                
                ];
                AppFirebase::sendNotificationForAll($param);
                // AppFirebase::sendNotificationSpecific($param);

                TTransaction::close();
                   
            }else{
                var_dump('nenhuma url');
            }
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }
    
    // faz a postagem manual de acordo com a pagina New Elog Urls
    public static function sendPostAppManual($id){
        try {
            TTransaction::open('appweb');
            
            $object = NewElog::find($id);

            if(isset($object->url)){
                $url = "{$object->url}/feed";
                $path = "tmp/noticias";
                
                $args = (object) WriteXML::getNewElog($url);
                $fonte = $args->fonte;
                $legenda = $args->title;
                $link = $args->link;
                $img = $args->img;
            
                if(empty($img)){
                    $img = 'https://elogbr.com/wp-content/uploads/2020/11/Aplicayivo-de-frete-2-930x620.jpg';
                }

                $ext = pathinfo($img, PATHINFO_EXTENSION);
                $name_firebase_photo = AppUtil::guid().'.'.$ext;

                // copia a imagem pra um caminho especifico
                if( !@copy( $img, "{$path}/{$name_firebase_photo}" ) ) {
                    $errors = error_get_last();
                    echo "COPY ERROR: ".$errors['type'];
                    echo "<br />\n".$errors['message'];
                } else {

                    // configurações de caminho
                    $bucketName = 'elog-social.appspot.com';
                    $objectName = "post-images/{$name_firebase_photo}";
                    $source = "{$path}/{$name_firebase_photo}";

                    // Classe pra redimencionar imagem app/lib/util
                    $resizeObj = new ResizeImg($source);
                    $resizeObj->resizeImage(600, 600, 'crop');
                    $resizeObj->saveImage($source, 100);

                    // Envia a imagem para pasta especifica do storage do firebase
                    AppFirebase::upload_image($bucketName, $objectName, $source);
                }   
                   
                // $profile_id = '4b62fde9-c968-4e1f-aab8-dfb777a7727b'; // Perfil Elog
                // $user_id = 'emP9GlCPBuYbbf68o5U4c9oAAqG3';  // Perfil Elog
                // $profile_id = '2f1e5716-d812-442e-ae53-05672711bb07';  // Perfil tunele
                // $user_id = 'pAOgKfF4rwXNiskQqjJ7FTUgalm2';  // Perfil tunele

                $content = "{$legenda}\n\nFonte: {$fonte}\nLeia mais: {$link}";

                TTransaction::open('appmobile');
                $post = new Post;
                $post->content = $content;
                $post->profile_id = $object->profile_id;
                $post->type = 'P';
                $post->store();

                $media = new Media;
                $media->url = $name_firebase_photo;
                $media->type = 'P';
                $media->store();

                $post_media = new PostMedia;
                $post_media->post_id = $post->id;
                $post_media->media_id = $media->id;
                $post_media->store();

                $param = [
                    'title' => 'ELOG NOTICIAS 🗞️ 🤭 !',
                    'body' => "{$legenda} : Leia mais",
                    'image' => '',
                    'url' => "/posts/post/{$post->id}",
                    'topic' => 'all',
                    // 'token' => 'fi_5WSbptPg:APA91bEJ3Gt6L_FS3KM583E_9mxeLCf_96Z4McF6Gp6rtrttjUNVNbE3FP7BPS_NGVUe3T6HOgmd5C3XCmrCORVfJanOfKNuzupAFThfq4rrFnomrx0xevQ1-G7Fgnkqc9VZwmZnU-6i' //tunele
                                
                ];
                // AppFirebase::sendNotificationForAll($param);
                // AppFirebase::sendNotificationSpecific($param);

                TTransaction::close();
            
            }
            
            
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }
}

?>
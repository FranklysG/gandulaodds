<?php

use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Storage;

use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Firestore\Query;


define('ACCESS_KEY', 'AIzaSyA8wjEIQz2IbHlogVSYY6h2Qfw5LqNbSUU');
define('API_ACCESS_KEY', 'AAAAAAlmy3c:APA91bG-czFNU9ck1M-zh0cj8Rm9xaiZ1zNKjcGbOVgZfE1IUhzfcknU0sD-xhNFZVDsSEt10_xIrj_QX4LqPb9mehK4QA8QKLkLEqFrgfYN-1tA_sFJP62KWtD6c-rXfds7CzFq54Ng');
define('FIREBASE_FILE',PATH.'/app/database/elog-social-service.json');
define('FIREBASE_URL', 'https://elog-social.firebaseio.com');

class AppFirebase
{

    public static $db;

    private static function getDB()
    {
        if (!self::$db) {
            self::$db = new FirestoreClient([
                'keyFilePath' => FIREBASE_FILE
            ]);
        }
    }

    // verifica o email e devolve o usuario que tem aquele email
    public static function verifyEmailAccount($email)
    {
        try {
    
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $auth = $factory->createAuth();
            if($auth->getUserByEmail($email)){
                return $auth->getUserByEmail($email);
            }else{
                return '';
            }
           
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    // verifica o uiser_id do usuario e devolve o que ele achar
    public static function verifyUserId($user)
    {
        try {
    
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $auth = $factory->createAuth();
            if($auth->getUser($user)){
                return $auth->getUser($user);
            }else{
                return '';
            }
            
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    // verifica o uiser_id do usuario e devolve o que ele achar
    public static function updateUser($uid, $properties)
    {
        try {
    
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $auth = $factory->createAuth();
            $updatedUser = $auth->updateUser($uid, $properties);
            
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /*
        Cria usuario passando as propirdades
        $userProperties = [
            'email' => 'email do cara',
            'emailVerified' => false,
            'phoneNumber' => 'numero dele',
            'password' => '',
            'displayName' => 'nome do cara',
            'photoUrl' => 'foto do perfil',
            'disabled' => false,
        ];
    */
    public static function createUser($userProperties){
        
        try{
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $auth = $factory->createAuth();
            
            $createdUser = $auth->createUser($userProperties);
            return $createdUser;
           
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }
    
    // delete usuario passando o user_id valido
    public static function deleteUser($uid){
        
        try{
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $auth = $factory->createAuth();
            
            $deletedUser = $auth->deleteUser($uid);
           
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /* 
        envia uma imagem pra uma pasta especifica
        $bucketName = url da pagina onde fica as pasta de iamgens do firebase ex. elog-social.appspot.com';
        $objectName = onde o arquivo vai ser upado ;
        $source = caminho do arquivo local;
    */
    public static function upload_image($bucketName, $objectName, $source){
        
        try {

            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $storage = $factory->createStorage();

            $file = fopen($source, 'r');
            $bucket = $storage->getBucket($bucketName);
            $object = $bucket->upload($file, [
                'name' => $objectName
            ]);
        
        } catch (Exeption $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /* 
        move a imagem pra um local especifico
        $bucketName = url da pagina onde fica as pasta de iamgens do firebase ex. elog-social.appspot.com';
        $objectName = onde o arquivo vai ser upado ;
        $newobjectName = pode ser o msm $bucketName se tiver só um bucket ;
        $newobjectName = pode ser o msm $objectName caso não queira mudar o nome ;
    */
    public static function move_image($bucketName, $objectName, $newBucketName, $newObjectName)
    {
        try{
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $storage = $factory->createStorage();

            $bucket = $storage->getBucket($bucketName);
            $object = $bucket->object($objectName);
            $object->copy($newBucketName, ['name' => $newObjectName]);
            $object->delete();
    } catch (Exeption $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /**
     * função chamada pra pegar os dados do documento indicado 
     * pelo usuario na hora de criar o perfil 
     */
    public static function getEspecificIndicationDocument($doc){
        
        try{
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $firestore = $factory->createFirestore();
            $db = $firestore->database();
           
            $docRef = $db->collection('indications')->document($doc);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                return $snapshot->data();
            } else {
                new TMessage('erro', 'Documento não encontrato');
            }
        //    return $indicated_profile;
        } catch (Exeption $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /**
     * deleta o documento do perfil indicado pelo usuario
     * recomendado usar depois que cadastra o usuario indicado
     * ou quando quer excluir se for local fake
     */
    public static function delEspecificDocument($doc){
            
            try{
                $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
                $firestore = $factory->createFirestore();
                $db = $firestore->database();

                $docRef = $db->collection('indications')->document($doc)->delete();
            } catch (Exeption $e) {
                new TMessage('erro', $e->getMessage());
            }
        }

    /**
     * lista todos os perfis indicados 
     */
    public static function getProfileIdicator(){
        
        try{
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $firestore = $factory->createFirestore();
            $db = $firestore->database();
           
            $usersRef = $db->collection('indications');
            $snapshot = $usersRef->documents();
            $indicated_profile = [];
            foreach ($snapshot as $value) {
                $indicated_profile[$value->id()] = [
                    'doc' => $value->id(),
                    'contact' => $value['contact'],
                    'created_at' => $value['created_at'],
                    'file' => $value['file'],
                    'lat' => $value['lat'],
                    'long' => $value['long'],
                    'name' => $value['name'],
                    'services' => $value['services'],
                    'uid' => $value['uid']
                ];
            }
           
           return $indicated_profile;
        } catch (Exeption $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /**
     * pega todos os tokens ativos dos usuarios
     */
    public static function getUserToken()
    {
        try {
    
            $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
            $firestore = $factory->createFirestore();
            $db = $firestore->database();
           
            $usersRef = $db->collection('usuarios');
            $snapshot = $usersRef->documents();
            $data_user_token = [];
            foreach ($snapshot as $user) {
                $data_user_token[$user['token']] = $user->id();
            }
            
            return $data_user_token;
        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }
    }

    /**
     * encia uma notificação pra um usuario especifico
     */
    public static function sendNotificationSpecific($param)
    {
        try {

            if (isset($param['token']) and $param['token']) {
                $notification = [
                    'title'	=> $param['title'],
                    'body' 	=> $param['body'],
                    'image' => $param['image']
                ];

                $data = [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'url' => $param['url'],
                    'sound' => 'alerta.mp3'
                ];
                
                $androidConfig = [
                    'ttl' => '43200s',
                    'priority' => 'high', // normal or high
                ];
                
                $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
                $messaging = $factory->createMessaging();
                

                $message = CloudMessage::withTarget('token', $param['token'])
                ->withNotification($notification)
                ->withAndroidConfig($androidConfig)
                ->withData($data);

                $messaging->send($message);
                // echo 'sended user specific '.date('Y-m-d H:i:s').'<br>';
                // new TMessage('info','Mensagem enviada a um usuario especifico');
                return true;
            }

            // return 'DADOS FALTANDO';
        } catch (Exception $e) {
            return 'TOKEN NÃO ENCONTRADO';
            new TMessage('erro', 'Token não encontrado');
        }
    }

    /**
     * envia notifcação para todos os usuarios
     */
    public static function sendNotificationForAll($param)
    {
        try {

            if (isset($param['topic']) and $param['topic']) {
                $notification = [
                    'title'	=> $param['title'],
                    'body' 	=> $param['body'],
                    'image' => $param['image']
                ];

        
                $data = [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'url' => $param['url'],
                    'sound' => 'alerta.mp3'
                ];
                
                $androidConfig = [
                    'ttl' => '43200s',
                    'priority' => 'high', // normal or high
                ];

                $factory = (new Factory())->withServiceAccount(FIREBASE_FILE);
                $messaging = $factory->createMessaging();

                $message = CloudMessage::withTarget('topic', $param['topic'])
                ->withNotification($notification)
                ->withAndroidConfig($androidConfig)
                ->withData($data);

                $messaging->send($message);
                // new TMessage('info','Mensagem enviada a todos o usuarios');
                return true;
            }

            // return 'DADOS FALTANDO';
        } catch (Exception $e) {
            // return 'TOKEN NÃO ENCONTRADO';
            new TMessage('erro', $e->getMessage());
        }
    }
    
}

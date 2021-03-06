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
            $client = HttpClient::create();
            // $request = $client->request('GET','https://www.cbf.com.br/futebol-brasileiro/jogosdehoje/campeonato-brasileiro-serie-b');
            $request = $client->request('GET','https://loretobets.com/simulador/jogos.aspx?idesporte=102&idcampeonato=574588');
            $statusCode = $request->getStatusCode();
            // $contentType = $request->getHeaders()['content-type'][0];
            $content = $request->getContent();
            
            if (200 !== $request->getStatusCode()) {
                throw new \Exception('Url da api não especificada');
            }

            /*
            $filename = "https://www.cbf.com.br/futebol-brasileiro/jogosdehoje/campeonato-brasileiro-serie-b";
	        $content = file_get_contents($filename);

            $pattern ='/<div class="maincounter-number".*?>\s<span.*?>\s?([0-9,]+)\s?<\/span>\s?<\/div>/';
            preg_match_all($pattern, $content, $match);
            
            echo "<br>================Coronavirus stats================";
            echo "<br>Cases........: ".$match[1][0];
            echo "<br>Deaths.......: ".$match[1][1];
            echo "<br>Recovered....: ".$match[1][2];
            echo "<br>============================================";
            */
            var_dump($content);
        } catch (Exeption $e) {
            new TMessage('warning', $e->getMessage());
        }
        
    }
}

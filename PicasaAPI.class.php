<?php 
require_once('BasicAPI.class.php');
require_once('MediaREST.class.php');

class PicasaAPI extends BasicAPI{
    
		/**
		 * association de la base de chemin avec une classe
		 */
		protected $routes = array(
							'media' => 'MediaREST',
							// 'user' => 'UserREST',
							);
							

    public function __construct($request, $origin){
        parent::__construct($request);

				//// pour l'API key
				/* 
        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) &&
             !$User->get('token', $this->request['token']))

            throw new Exception('Invalid User Token');
        }
				*/
    }
		
		
		protected function createResourceAPI($classname, $data){
			$res = null;
			
			if(class_exists($classname))
				$res = new $classname($data);
	
			return $res;
		}
 }
?>
<?php

/**
 * API core basé sur le code http://coreymaynard.com/blog/creating-a-restful-api-with-php/ 
 */
abstract class BasicAPI{
    /**
     * Propriété: method
     * Valeur possible GET, POST, PUT ou DELETE
     */
    protected $method = '';
		/**
     * Propriété: path
     * Contient découpage du chemin
     */
		protected $path = array();
		/**
		 * Classique
		 */
		protected $queryParams = array();
		protected $formParams = array();
		protected $headerParams = array();
		
    protected $routes = array();
		
    protected $file = null;
    protected $request = null;
		
		
    public function __construct($request) {
			$this->header('Access-Control-Allow-Origin', '*');
			$this->header('Access-Control-Allow-Methods', '*');
			$this->header('Content-Type', 'application/json');
			
			$path = $request;

			//queryParams
			if(strpos($request, '?') !== false):
				list($path, $query) = explode('?', $request);
				$query = ltrim($query, '&');
				parse_str($query, $this->queryParams);
			endif;
			
			
			//pathParams
			$this->path = explode('/', rtrim($path, '/'));


			$this->source = array_shift($this->path);
			
			//method
			$this->method = $_SERVER['REQUEST_METHOD'];
			if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
					if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
							$this->method = 'DELETE';
					} else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
							$this->method = 'PUT';
					} else {
							throw new Exception("Unexpected Header");
					}
			}
			
			//remplissage des paramètres
			$this->queryParams = $this->_cleanInputs($_GET);
			$this->formParams = $this->_cleanInputs($_POST);
			switch($this->method) {
				case 'DELETE':
				case 'POST':
						$this->request = $this->_cleanInputs($_POST);
						break;
				case 'GET':
						$this->request = $this->_cleanInputs($_GET);
						break;
				case 'PUT':
						$this->request = $this->_cleanInputs($_GET);
						$this->file = file_get_contents("php://input");
						break;
				default:
						$this->_response('Invalid Method', 405);
						break;
			}
			
    }
		
		abstract protected function createResourceAPI($classname, $data);

		
		/**
		 * lance l'action à effectuer
		 */
		public function process() {
			$data = array(
						'method' => $this->method,
						'path' => $this->path,
						'query' => $this->getQueryParameters(),
						'form' => $this->formParams,
						);
			
			
			if(array_key_exists($this->source, $this->routes)){
					$classname = $this->routes[$this->source];
					
					$resource = $this->createResourceAPI($classname, $data);
					
					if(empty($resoure))
						return $this->_response('Not Found', 404);
					
					$data = $resource->process($status);
					return $this->_response($data, $status);
			}
      return $this->_response('Not Found', 404);
    }
		
		/**
		 * Accesseur des paramètres de chemin
		 */
		public function getParameters($key = ''){
			if(empty($key))
				return $this->pathParams;
			
			if(array_key_exists($key, $this->pathParams))
				return $this->pathParams[$key];
			
			return '';
		}
		/**
		 * Accesseur des paramètres de requête
		 */
		public function getQueryParameters(){
			if(empty($key))
				return $this->queryParams;
			
			if(array_key_exists($key, $this->queryParams))
				return $this->queryParams[$key];
			
			return '';
		}
		/**
		 * Accesseurs/Modificateur des en-tête
		 */
		public function header($key, $value =''){
			$key = strtolower($key);
			if(!array_key_exists($key, $this->headerParams) && empty($value))
				return false;
				
			if(empty($value))
				return $this->headerParams[$key];
			$this->headerParams[$key] = $value;
		}
		
		/**********
		 * Core Methods
		 **********/
		 
		/**
		 * Génère les en-tete
		 */
		private function _header() {
        foreach($this->headerParams as $key => $value):
					header($key.': '.$value);
				endforeach;
    }
			
    private function _response($data, $status = 200) {
			$this->_header();
      header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
			
			return $this->_content($data);
    }
		
		private function _content($data){
			$content = '';
			
			switch($this->header('Content-Type')){
				default:
				case 'text/plain':
					$content = $data;
					break;
				case 'text/json':
				case 'application/json':
					$content = json_encode($data);
					break;
			}
			
			return $content;
		}

		/**
		 * Nettoie tableau
		 */
    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
		
    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return (array_key_exists($code, $status))?$status[$code]:$status[500]; 
    }
}
?>
<?php 

/**
 * Classe de référence pour les ressources
 */
abstract class BasicResource{
    
		/**
		 * Propriété : routes
		 * Contient un tableau de chemin accepté par l'API
		 * Tableau de la forme :
		 *  array(...
		 *	'{param_name1}/{param_name2}/comment' => array(
		 *											'GET' => 'nom_methode',
		 *											'POST' => 'nom_methode2',
		 *										)
		 *	)
		 */
		protected $routes = array();
		
		protected $method = '';
		/**
		 * méthode de la ressource à utiliser
		 */
		protected $_function = '';
		
    protected $path = array();
    protected $pathParams = array();
    protected $queryParams = array();
    protected $formParams = array();

 
		
    public function __construct($request){
			$defaults = array(
									'method' => 'GET',
									'path' => array(),
									'query' => array(),
									'form' => array(),
									);
			$options = array_merge($defaults, $request);
			
			$this->method = $options['method'];
			$this->path = $options['path'];
			$this->queryParams = $options['query'];
			$this->formParams = $options['form'];

			$this->build();
    }
		
		private function parameters($key, $value){
			$this->pathParams[$key] = $value;
		}
		
		protected function getParameters($key = ''){
			if(empty($key))
				return $this->pathParams;
			
			if(array_key_exists($key, $this->pathParams))
				return $this->pathParams[$key];
			
			return '';
		}
		
		protected function getQueryParameters($key = ''){
			if(empty($key))
				return $this->queryParams;
			
			if(array_key_exists($key, $this->queryParams))
				return $this->queryParams[$key];
			
			return '';
		}
		
		protected function getFormParameters($key = ''){
			if(empty($key))
				return $this->formParams;
			
			if(array_key_exists($key, $this->formParams))
				return $this->formParams[$key];
			
			return '';
		}
		
		protected function getMethod(){
			return $this->method;
		}
		
		
		public function process(&$status = 200) {
			if(empty($this->_function)):
				$status = 404;
				return array();
			endif;
			
      return $this->{$this->_function}($status);
    }
		
		/**
		 * détermine méthode à utiliser pour la ressource en fonction du chemin et du type de méthode(POST,...)
		 */
		protected function build(){
			$path = $this->path;
			$action = array();
			
			foreach($this->routes as $route => $action){
				if(empty($route) && empty($path))
					break;
					
				if(empty($route) || empty($path)){
					$action = array();
					continue;
				}
				
				$route = explode('/', $route);
				$this->pathParams = array();

				for($i = 0; $i < count($route) && (preg_match('#^\{(.*)\}$#i', $route[$i], $matches) || $route[$i]==$path[$i]); $i++){
					$this->parameters($matches[1], $path[$i]);
				}
				
				if($i>=count($route))
					break;
				
				$action = array();
			}
			
			if(!empty($action) && array_key_exists($this->method, $action))
				$this->_function = $action[$this->method];
		}
 }
 ?>
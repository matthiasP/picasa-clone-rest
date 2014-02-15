<?php 
require_once('BasicResource.class.php');
require_once('File.class.php');

//@Path('media')
class MediaREST extends BasicResource{
    
		
		protected $routes = array(
							'' => array(
												'GET' => 'getAll',
												),
							'{user}' => array(
												'GET' => 'getAll',
												'POST' => 'uploadMedia',
												),
							'{user}/{media_id}' => array(
												'GET' => 'getMedia',
												'PUT' => 'update',
												'DELETE' => 'delete',
												),
							'{user}/{media_id}/comment' => array(
												'GET' => 'getAllComments',
												'POST' => 'addComment',
												),
							'{user}/{media_id}/comment/{comment_id}' => array(
												'GET' => 'getComment',
												),
							);
		
    public function __construct($request){
			parent::__construct($request);
    }
		
		public function getAll(&$status){
			$status = 200;
			return array('t' => 'Test');
		}
		
		
		//params : media
		public function uploadMedia(&$status){
			
			$dst_folder = './uploads/';
			$data = File::uploadFile($_FILES['media'], $dst_folder);
			
			if($data['upload']){
				$status = 200;
				
				$data['links'] = array(
											array(
												'rel' => 'self',
												'href' => $_SERVER['SERVER_NAME'].'/media/shigeru/1',
												'method' => 'GET',
												),
											array(
												'rel' => 'edit',
												'href' => $_SERVER['SERVER_NAME'].'/media/shigeru/1',
												'method' => 'PUT',
												),
											array(
												'rel' => 'related',
												'href' => $_SERVER['SERVER_NAME'].'/media/shigeru/1/comment',
												'method' => 'GET',
												),
											);
			}
			else 
				$status = 500;
			
			
			return $data;
		}
 }
?>
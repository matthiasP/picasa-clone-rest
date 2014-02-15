<?php

class File{

	public static function uploadFile($file, $destination, $args = array()){
			$defaults = array(
									'filename' => '',
									'maxsize' => 0,
									'allowed' => array(),
									);
									
			$options = array_merge($defaults, $args);
			$data = array();
			$data['upload'] = false;
			
			if($file['error']>0):
				$data['msg'] = File::upload_message($file);
				return $data;
			elseif($options['maxsize']>0 && $file['size']>$options['maxsize']):
				$data['msg'] = "Fichier trop volumineux";
				return $data;
			endif;
			
			if($options['filename'] == '')
				$options['filename'] = $file['name'];
			else{
				$extension = pathinfo($file['name'],PATHINFO_EXTENSION);
				$options['filename'].'.'.$extension;
			}
			
			if(!is_dir($destination))
				mkdir($destination);
				
			if (move_uploaded_file($file['tmp_name'], $destination.$options['filename'])){
			
				//$mime = get_MIME($destination.$options['filename']);
				//list($data['type'], $data['ext']) = explode('/', $mime);
				$data['ext'] = pathinfo($file['name'],PATHINFO_EXTENSION);
				
				if(!empty($options['allowed']) && !in_array($data['ext'], $options['allowed'])):
					$data['msg'] = "type de fichier non autorisé";
					unlink($destination.$options['filename']);
				else:
					$info = pathinfo($destination.$options['filename']);
					$data['msg'] = "fichier uploadé avec succès";
					$data['basename'] = $info['basename'];
					$data['filename'] = $info['filename'];
					$data['size'] = $file['size'];
					$data['upload'] = true;
				endif;
			}
			else{
			
				$data['msg'] = "erreur à l'upload";
			}
			
			return $data;
		}
		
		
		private static function upload_message($file){
			switch($file['error']):
				default:
					$msg = "upload correct";
					break;
				case UPLOAD_ERR_NO_FILE:
					$msg = "Fichier manquant";
					break;
				case UPLOAD_ERR_INI_SIZE:
					return "Fichier dépassant la taille maximale autorisée par PHP";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$msg = "Fichier dépassant la taille maximale autorisée par le formulaire";
					break;
				case UPLOAD_ERR_PARTIAL:
					$msg = "Fichier transféré partiellement";
					break;
			endswitch;

			return $msg;
		}
}
?>
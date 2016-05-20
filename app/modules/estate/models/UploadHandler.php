<?php

class estate_models_UploadHandler
{
	private $_options;

	function __construct($options=null, $id = 0)
	{
		$_basepath = realpath(Zend_Registry::get('config')->file->path->storage);

		$path = "/estate/gallery/$id/";

		if (!file_exists($_basepath.'/estate')) mkdir($_basepath.'/estate');
		if (!file_exists($_basepath.'/estate/gallery')) mkdir($_basepath.'/estate/gallery');
		if (!file_exists($_basepath.'/estate/gallery/'.$id)) mkdir($_basepath.'/estate/gallery/'.$id);

		if (!file_exists($_basepath.'/thumb')) mkdir($_basepath.'/thumb');
		if (!file_exists($_basepath.'/thumb/estate')) mkdir($_basepath.'/thumb/estate');
		if (!file_exists($_basepath.'/thumb/estate/gallery')) mkdir($_basepath.'/thumb/estate/gallery');
		if (!file_exists($_basepath.'/thumb/estate/gallery/'.$id)) mkdir($_basepath.'/thumb/estate/gallery/'.$id);

		$this->_options = array(
			'script_url' => "/estate/admin/blueimp/id/$id/",
			'upload_dir' => $_basepath.urldecode($path),
			'upload_url' => '/file/index/get/?id=$id&path='.urldecode($path),
			'param_name' => 'files',
			// The php.ini settings upload_max_filesize and post_max_size
			// take precedence over the following max_file_size setting:
			'max_file_size' => 5*1024*1024,
			'min_file_size' => 1,
			'accept_file_types' => '/.+$/i',
			'max_number_of_files' => null,
			// Set the following option to false to enable non-multipart uploads:
			'discard_aborted_uploads' => true,
			// Set to true to rotate images based on EXIF meta data, if available:
			'orient_image' => false,
			'image_versions' => array(
				// Uncomment the following version to restrict the size of
				// uploaded images. You can also add additional versions with
				// their own upload directories:
				'large' => array(
					'upload_dir' => $_basepath.urldecode($path),
					'upload_url' => '/file/index/get/?path='.urldecode($path),
					'max_width' => 1920,
					'max_height' => 1200
				),
				'thumbnail' => array(
					'upload_dir' => $_basepath.'/thumb'.urldecode($path),
					'upload_url' => '/file/index/get/?path=/thumb'.urldecode($path),
					'max_width' => 80,
					'max_height' => 60
				)
			)
		);

		if ($options) $this->_options = array_replace_recursive($this->_options, $options);
	}

	private function get_file_object($file_name) {
		$file_path = $this->_options['upload_dir'].$file_name;
		if (is_file($file_path) && $file_name[0] !== '.') {
			$file = new stdClass();
			$file->name = $file_name;
			$file->size = filesize($file_path);
			$file->url = $this->_options['upload_url'].rawurlencode($file->name);
			foreach($this->_options['image_versions'] as $version => $options) {
				if (is_file($options['upload_dir'].$file_name)) {
					$file->{$version.'_url'} = $options['upload_url']
						.rawurlencode($file->name);
				}
			}
			$file->delete_url = $this->_options['script_url']
				.'?file='.rawurlencode($file->name);
			$file->delete_type = 'DELETE';
			return $file;
		}
		return null;
	}

	private function get_file_objects() {
		return array_values(array_filter(array_map(
			array($this, 'get_file_object'),
			scandir($this->_options['upload_dir'])
		)));
	}

	private function create_scaled_image($file_name, $options) {
		$file_path = $this->_options['upload_dir'].$file_name;
		$new_file_path = $options['upload_dir'].$file_name;
		list($img_width, $img_height) = @getimagesize($file_path);
		if (!$img_width || !$img_height) {
			return false;
		}
		$scale = min(
			$options['max_width'] / $img_width,
			$options['max_height'] / $img_height
		);
		if ($scale > 1) {
			$scale = 1;
		}
		$new_width = $img_width * $scale;
		$new_height = $img_height * $scale;
		$new_img = @imagecreatetruecolor($new_width, $new_height);
		switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
			case 'jpg':
			case 'jpeg':
				$src_img = @imagecreatefromjpeg($file_path);
				$write_image = 'imagejpeg';
				break;
			case 'gif':
				@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
				$src_img = @imagecreatefromgif($file_path);
				$write_image = 'imagegif';
				break;
			case 'png':
				@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
				@imagealphablending($new_img, false);
				@imagesavealpha($new_img, true);
				$src_img = @imagecreatefrompng($file_path);
				$write_image = 'imagepng';
				break;
			default:
				$src_img = $image_method = null;
		}
		$success = $src_img && @imagecopyresampled(
			$new_img,
			$src_img,
			0, 0, 0, 0,
			$new_width,
			$new_height,
			$img_width,
			$img_height
		) && $write_image($new_img, $new_file_path);
		// Free up memory (imagedestroy does not delete files):
		@imagedestroy($src_img);
		@imagedestroy($new_img);
		return $success;
	}

	private function has_error($uploaded_file, $file, $error) {
		if ($error) {
			return $error;
		}
		if (!preg_match($this->_options['accept_file_types'], $file->name)) {
			return 'acceptFileTypes';
		}
		if ($uploaded_file && is_uploaded_file($uploaded_file)) {
			$file_size = filesize($uploaded_file);
		} else {
			$file_size = $_SERVER['CONTENT_LENGTH'];
		}
		if ($this->_options['max_file_size'] && (
				$file_size > $this->_options['max_file_size'] ||
				$file->size > $this->_options['max_file_size'])
			) {
			return 'maxFileSize';
		}
		if ($this->_options['min_file_size'] &&
			$file_size < $this->_options['min_file_size']) {
			return 'minFileSize';
		}
		if (is_int($this->_options['max_number_of_files']) && (
				count($this->get_file_objects()) >= $this->_options['max_number_of_files'])
			) {
			return 'maxNumberOfFiles';
		}
		return $error;
	}

	private function trim_file_name($name, $type) {
		// Remove path information and dots around the filename, to prevent uploading
		// into different directories or replacing hidden system files.
		// Also remove control characters and spaces (\x00..\x20) around the filename:
		$file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
		// Add missing file extension for known image types:
		if (strpos($file_name, '.') === false &&
			preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
			$file_name .= '.'.$matches[1];
		}
		return $file_name;
	}

	private function orient_image($file_path) {
	  	$exif = exif_read_data($file_path);
	  	$orientation = intval(@$exif['Orientation']);
	  	if (!in_array($orientation, array(3, 6, 8))) {
	  		return false;
	  	}
	  	$image = @imagecreatefromjpeg($file_path);
	  	switch ($orientation) {
			  case 3:
		  		$image = @imagerotate($image, 180, 0);
		  		break;
			  case 6:
		  		$image = @imagerotate($image, 270, 0);
		  		break;
			  case 8:
		  		$image = @imagerotate($image, 90, 0);
		  		break;
		  	default:
		  		return false;
	  	}
	  	$success = imagejpeg($image, $file_path);
	  	// Free up memory (imagedestroy does not delete files):
	  	@imagedestroy($image);
	  	return $success;
	}

	private function handle_file_upload($uploaded_file, $name, $size, $type, $error) {
		$file = new stdClass();
		$file->name = $this->trim_file_name($name, $type);
		$file->size = intval($size);
		$file->type = $type;
		$error = $this->has_error($uploaded_file, $file, $error);
		if (!$error && $file->name) {
			$file_path = $this->_options['upload_dir'].$file->name;
			$append_file = !$this->_options['discard_aborted_uploads'] &&
				is_file($file_path) && $file->size > filesize($file_path);
			clearstatcache();
			if ($uploaded_file && is_uploaded_file($uploaded_file)) {
				// multipart/formdata uploads (POST method uploads)
				if ($append_file) {
					file_put_contents(
						$file_path,
						fopen($uploaded_file, 'r'),
						FILE_APPEND
					);
				} else {
					move_uploaded_file($uploaded_file, $file_path);
				}
			} else {
				// Non-multipart uploads (PUT method support)
				file_put_contents(
					$file_path,
					fopen('php://input', 'r'),
					$append_file ? FILE_APPEND : 0
				);
			}
			$file_size = filesize($file_path);
			if ($file_size === $file->size) {
					if ($this->_options['orient_image']) {
						$this->orient_image($file_path);
					}
				$file->url = $this->_options['upload_url'].rawurlencode($file->name);
				foreach($this->_options['image_versions'] as $version => $options) {
					if ($this->create_scaled_image($file->name, $options)) {
						$file->{$version.'_url'} = $options['upload_url']
							.rawurlencode($file->name);
					}
				}
			} else if ($this->_options['discard_aborted_uploads']) {
				unlink($file_path);
				$file->error = 'abort';
			}
			$file->size = $file_size;
			$file->delete_url = $this->_options['script_url']
				.'?file='.rawurlencode($file->name);
			$file->delete_type = 'DELETE';
		} else {
			$file->error = $error;
		}
		return $file;
	}

	public function get() {
		$file_name = isset($_REQUEST['file']) ?
			basename(stripslashes($_REQUEST['file'])) : null;
		if ($file_name) {
			$info = $this->get_file_object($file_name);
		} else {
			$info = $this->get_file_objects();
		}
		header('Content-type: application/json');
		echo json_encode($info);
	}

	public function post() {
		if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
			return $this->delete();
		}
		$upload = isset($_FILES[$this->_options['param_name']]) ?
			$_FILES[$this->_options['param_name']] : null;
		$info = array();
		if ($upload && is_array($upload['tmp_name'])) {
			foreach ($upload['tmp_name'] as $index => $value) {
				$info[] = $this->handle_file_upload(
					$upload['tmp_name'][$index],
					isset($_SERVER['HTTP_X_FILE_NAME']) ?
						$_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
					isset($_SERVER['HTTP_X_FILE_SIZE']) ?
						$_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
					isset($_SERVER['HTTP_X_FILE_TYPE']) ?
						$_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
					$upload['error'][$index]
				);
			}
		} elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
			$info[] = $this->handle_file_upload(
				isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
				isset($_SERVER['HTTP_X_FILE_NAME']) ?
					$_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
						isset($upload['name']) : null),
				isset($_SERVER['HTTP_X_FILE_SIZE']) ?
					$_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ?
						isset($upload['size']) : null),
				isset($_SERVER['HTTP_X_FILE_TYPE']) ?
					$_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
						isset($upload['type']) : null),
				isset($upload['error']) ? $upload['error'] : null
			);
		}
		header('Vary: Accept');
		$json = json_encode($info);
		$redirect = isset($_REQUEST['redirect']) ?
			stripslashes($_REQUEST['redirect']) : null;
		if ($redirect) {
			header('Location: '.sprintf($redirect, rawurlencode($json)));
			return;
		}
		if (isset($_SERVER['HTTP_ACCEPT']) &&
			(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
			header('Content-type: application/json');
		} else {
			header('Content-type: text/plain');
		}
		echo $json;
	}

	public function delete() {
		$file_name = isset($_REQUEST['file']) ?
			basename(stripslashes($_REQUEST['file'])) : null;
		$file_path = $this->_options['upload_dir'].$file_name;
		$success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
		if ($success) {
			foreach($this->_options['image_versions'] as $version => $options) {
				$file = $options['upload_dir'].$file_name;
				if (is_file($file)) {
					unlink($file);
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($success);
	}

}
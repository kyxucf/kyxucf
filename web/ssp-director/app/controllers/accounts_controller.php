<?php

class AccountsController extends AppController {
    var $name = 'Accounts';
	var $helpers = array('Html', 'Javascript', 'Ajax');
	
	var $non_ajax_actions = array('settings', 'info', '_data', 'theme', 'language', 'activate', 'news');
	
	// Only logged in users should see this controller's actions
 	function beforeFilter() {
		// Protect ajax actions
		if (!in_array($this->action, $this->non_ajax_actions)) {
			$this->verifyAjax();
		}
		// Check session
		$this->checkSession();
		$this->verifyRight(3);
	}
	
	////
	// Manage account preferences
	////
	function info() {
		$this->pageTitle = __('System info', true);
		$account = $this->Account->find();
		$this->set('account', $account);
		DIR_GD_VERSION > 0 ? $gd = true : $gd = false;
		$this->set('gd', $gd);
		$this->set('curl', extension_loaded('curl'));
		
		$image_lib_status = 'yes';
		if (DIR_GD_VERSION >= 3) {
			$image_lib = 'ImageMagick';
		} elseif (DIR_GD_VERSION == 2) {
			$image_lib = 'GD2';
		} else if (DIR_GD_VERSION == 1) {
			$image_lib = 'GD1';
		} else {
			$image_lib_status = 'no';
			$image_lib = __('No image processing library found.', true);
		}

		$ffmpeg = $this->Director->ffmpeg();
		
		if ($ffmpeg) {
			$ffmpeg_status = 'yes';
			$ffmpeg = __('[#Conveys whether or not a server supports certain functionality.#]Supported', true);
		} else {
			$ffmpeg_status = 'no';
			$ffmpeg = __('Not supported', true);
		}
		
		list($connect_status, $connect_detail) = $this->Pigeon->test();
		
		if ($connect_status == 0) {
			$connect_detail = __('Communicating normally', true);
			$connect_status = 'yes';
		} else {
			$connect_status = 'no';
			$connect_detail = __('Not communicating.', true) . '<br /><span class="context">' . __('Error', true) . ': ' . $connect_detail . '</span>';
		}
		
		if ($connect_status == 'yes') {
			$cur_version = trim($this->Pigeon->version(true));
			if ($cur_version != DIR_VERSION_FULL && !empty($cur_version)) {
				$parts = explode('.', $cur_version);
				$this->set('new_version', $parts[0] . '.' . $parts[1] . '.' . $parts[2] . ' (Build ' . $parts[3] . ')');
			}
		}
		
		list($max, $post_max_broken) = $this->Director->uploadLimit();
			
		$memory_limit = ini_get('memory_limit');
		$memory_limit_status = 'yes';
		if (empty($memory_limit)) {
			$memory_limit_note = __('No memory limit is set in PHP', true);
		} else {
			$memory_limit_note = $memory_limit . 'B';
			$n = (int) r('M', '', $memory_limit);
			if (is_numeric($n) && $n < 64) {
				$memory_limit_note .= '<br/><span class="context">' . __('May be too low for some tasks.', true) . '</span>';
				$memory_limit_status = 'maybe';
			}
		}
		
		$exif = is_callable('exif_read_data');
		$iptc = is_callable('iptcparse');
		
		$metadata_status = 'yes';
		if ($exif && $iptc) {
			$metadata = __('EXIF and IPTC metadata parsing supported.', true);
		} elseif ($exif) {
			$metadata_status = 'maybe';
			$metadata = __('EXIF metadata parsing supported. IPTC parsing not supported.', true);
		} elseif ($iptc) {
			$metadata_status = 'maybe';
			$metadata = __('IPTC metadata parsing supported. EXIF parsing not supported.', true);
		} else {
			$metadata_status = 'no';
			$metadata = __('Metadata parsing not supported.', true);
		}
		
		$xml_caching_note = $internal_caching_note = __('Operating normally', true);
		$xml_caching_status = $internal_caching_status = 'yes';
		
		if (!is_writable(XML_CACHE)) {
			$xml_caching_note = __('Director is unable to write to the xml_cache folder.', true);
			$xml_caching_status = 'maybe';
		}
		
		if (!is_writable(CACHE . DS . 'director')) {
			$internal_caching_note = __('Director is unable to write to the cache folders in app/tmp.', true);
			$internal_caching_status = 'maybe';
		}
		
		$reports = array(
				array(
					'name' => __('Licensing', true),
					'status' => TRIAL_STATE ? 'maybe' : 'yes',
					'note' => TRIAL_STATE ? __('Trial, limited to 50 uploads.', true) : sprintf(__('Activated. Key: %s', true), '<code>' . $account['Account']['activation_key'] . '</code>'),
					'button' => array('id' => '', 'label' => TRIAL_STATE ? __('Unlock', true) : __('Edit key', true), 'js' => "location.href='" . DIR_HOST . "/index.php?/accounts/activate/edit'")
					),
					
				array(
					'name' => __('Image processing library', true),
					'status' => $image_lib_status,
					'note' => $image_lib,
					'button' => ''
					),
					
				array(
					'name' => 'FFmpeg',
					'status' => $ffmpeg_status,
					'note' => $ffmpeg,
					'button' => array('id' => '', 'label' => __('More info', true), 'js' => "window.open('http://wiki.slideshowpro.net/SSPdir/CP-UsingFFmpeg')")
					),	
				
				array(
					'name' => __('Memory limit', true),
					'status' => $memory_limit_status,
					'note' => $memory_limit_note,
					'button' => array('id' => '', 'label' => __('More info', true), 'js' => "window.open('http://wiki.slideshowpro.net/SSPdir/CP-MemoryLimit')")
					),
					
				array(
					'name' => __('Metadata', true),
					'status' => $metadata_status,
					'note' => $metadata,
					'button' => ''
					),
				
				array(
					'name' => __('File uploading', true),
					'status' => ife($post_max_broken, 'maybe', 'yes'),
					'note' => sprintf(__('Uploading enabled. Maximum file size is %sB.', true), $max) . ife($post_max_broken, '<br /><span class="context">' . __("Your post_max_size is set lower than upload_max_filesize, which limits the size of file you may upload.", true) . '</span>'),
					'button' => array('id' => '', 'label' => __('More info', true), 'js' => "window.open('http://wiki.slideshowpro.net/SSPdir/CP-ImageUploadsDenied')")
					),
					
				array(
					'name' => 'PHP',
					'status' => 'yes',
					'note' => sprintf(__('Supported. Server running %s.', true), phpversion()),
					'button' => ''
					),
					
				array(
					'name' => 'MySQL',
					'status' => 'yes',
					'note' => __('Supported and connected', true),
					'button' => ''
					),
				
				array(
					'name' => __('Connection to slideshowpro.net', true),
					'status' => $connect_status,
					'note' => $connect_detail,
					'button' => ''
					),
					
				array(
					'name' => __('Album caching', true),
					'status' => 'yes',
					'note' => __('Operating normally', true),
					'button' => array('id' => 'album-clear-btn', 'label' => __('Clear cache', true), 'js' => "Messaging.dialogue('clear-album-caches')")
					),
					
				array(
					'name' => __('XML caching', true),
					'status' => $xml_caching_status,
					'note' => $xml_caching_note,
					'button' => array('id' => 'xml-clear-btn', 'label' => __('Clear cache', true), 'js' => "clear_other_caches('" . DIR_HOST . "', 'xml')")
					),
					
				array(
					'name' => __('Internal caching', true),
					'status' => $internal_caching_status,
					'note' => $internal_caching_note,
					'button' => array('id' => 'internal-clear-btn', 'label' => __('Clear cache', true), 'js' => "clear_other_caches('" . DIR_HOST . "', 'internal')")
					)
		);	
		
		$info = array(
					'php' => phpversion(),
					'memory' => ini_get('memory_limit'),
					'processing' => $image_lib,
					'max_upload' => $max,
					'post_max_broken' => $post_max_broken,
					'exif' => is_callable('exif_read_data'),
					'iptc' => is_callable('iptcparse'),
					'v_processing' => $ffmpeg
				);
				
		$this->set('info', $info);	
		$this->set('reports', $reports);
	}
	
	function settings() {
		@$account = $this->data = $this->account;
		$this->set('account', $account);
		uses('Folder');
		$themes_folder = new Folder(THEMES);
		$themes = $themes_folder->ls(true, false);
		$this->set('themes', $themes[0]);
		$custom_themes_folder = new Folder(USER_THEMES);
		$custom_themes_templates = $custom_themes_folder->ls(true, array('sample', '.', '..', '.svn'));
		$this->set('custom_themes', $custom_themes_templates[0]);
		$lang_folder = new Folder(APP . DS . 'locale');
		$langs = $lang_folder->ls(true, false);
		$this->set('langs', $langs[0]);
		$templates_folder = new Folder(PLUGS . DS . 'links');
		$link_templates = $templates_folder->ls(true, false);
		$this->set('link_templates', $link_templates[1]);
		$custom_templates_folder = new Folder(CUSTOM_PLUGS . DS . 'links');
		$custom_link_templates = $custom_templates_folder->ls(true, array('sample', '.', '..', '.svn'));
		$this->set('custom_link_templates', $custom_link_templates[0]);
		$iptcs = $this->Director->iptcTags;
		natsort($iptcs);
		$exifs = $this->Director->exifTags;
		natsort($exifs);
		$dirs = $this->Director->dirTags;
		natsort($dirs);
		$this->loadModel('Watermark');
		$this->set('watermarks', $this->Watermark->find('all'));
		$this->set('iptcs', $iptcs);
		$this->set('exifs', $exifs);
		$this->set('dirs', $dirs);
	}
	
	////
	// Set captions on all images in an album
	////
	function defaults($id) {
		$this->Account->id = $id;
		$this->Account->save($this->data);
		exit();
	}
	
	////
	// Update accoumt
	////
	function update($id) {
		$this->Account->id = $id;
		$this->Account->save($this->data);
		exit;
	}
	
	function theme($new_theme) {
		$account = $this->account;
		$this->Account->id = $account['Account']['id'];
		$theme = r('--', '/', strtolower($new_theme));
		$this->Account->recursive = -1;
		$this->Account->saveField('theme', '/' . $theme . '.css');
		$this->redirect('/accounts/settings');
		exit;
	}
	
	function language($new_lang) {
		$account = $this->account;
		$this->Account->id = $account['Account']['id'];
		$this->Account->recursive = -1;
		$this->Account->saveField('lang', $new_lang);
		$this->redirect('/accounts/settings');
	}
	
	function news($value = 0) {
		$account = $this->account;
		$this->Account->id = $account['Account']['id'];
		$this->Account->recursive = -1;
		$this->Account->saveField('externals', $value);
		$this->redirect('/accounts/settings');
	}
	
	function activate($action = 'error') {
		$this->layout = "simple";
		
		if (isset($this->data['transfer'])) {
			list($code, $result) = $this->Pigeon->activate($this->data['Account']['activation_key'], true);
			if ($code == 0) {
				$this->Account->id = $this->account['Account']['id'];
				$this->data['Account']['last_check'] = date('Y-m-d H:i:s', strtotime('+2 weeks'));
				$this->Account->save($this->data['Account']);
				$this->set('success', true);
			} else {
				$this->set('error', $result);
			}
		} else if ($this->data['Account']) {
			list($code, $result) = $this->Pigeon->activate($this->data['Account']['activation_key']);
			if ($code == 0) {
				$this->Account->id = $this->account['Account']['id'];
				$this->data['Account']['last_check'] = date('Y-m-d H:i:s', strtotime('+2 weeks'));
				$this->Account->save($this->data);
				$this->set('success', true);
			} else {
				$this->set('error', $result);
				
			}
		} else {
			$this->data = $this->account;
		}
		
		$this->set('action', $action);
	}
}

?>
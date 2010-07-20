<?php


class fvCommunityNewsInstall {
	
	protected $_db = null;
	
	protected $_settings = null;
	
	public function __construct(fvCommunityNewsSettings_Abstract $settings) {
		$this->_settings = $settings;
		$this->_db = fvCommunityNewsRegistry::get('wpdb');
		
		$this->_installDatabase()
			 ->_installSettings();
	}
	
	protected function _installDatabase() {
		$dbName = $this->_db->prefix . $this->_settings->DbName;
		$sql = str_replace('%DbName%', $dbName, file_get_contents(WP_PLUGIN_DIR . FVCN_PLUGINDIR . '/Config/database.sql'));
		
		if (!file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
			throw new Exception('Cannot find "upgrade.php"');
		} else {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta($sql);
		}
		
		return $this;
	}
	
	protected function _installSettings() {
		foreach ($this->_settings->getAll() as $setting) {
			switch ($setting->getName()) {
				case 'DbName' :
					$val =  $this->_db->prefix . $setting;
					break;
				default :
					$val = $setting;
			}
			
			
			switch ($setting['type']) {
				case 'bool' :
					$this->_settings->add($setting->getName(), ('true'==$val?true:false));
					break;
				case 'int' :
					$this->_settings->add($setting->getName(), (int)$val);
					break;
				case 'string' :
				default :
					$this->_settings->add($setting->getName(), (string)$val);
			}
		}
		
		return $this;
	}
	
}


<?php
	class singleton{
		private static $instances = array();
		protected function __construct() {}
		protected function __clone() {}
		public function __wakeup()
		{
			throw new Exception("Cannot unserialize singleton");
		}

		public static function classInstance(){
			$cls = get_called_class(); // late-static-bound class name
			if (!isset(self::$instances[$cls])) {
				self::$instances[$cls] = new static;
			}
			return self::$instances[$cls];
		}


		public static function clearInstance(){
			$cls = get_called_class(); // late-static-bound class name
			unset(self::$instances[$cls]);
			return static::classInstance();
		}

		private static function getConstants() {
			$oClass = new \ReflectionClass(get_called_class());
			return $oClass->getConstants();
	    }

	    public static function getStatuses(){
	    	$consts = static::getConstants();

	    	$statuses = array();
	    	foreach($consts as $name => $value){
	    		if( substr($name, 0, 7) == "STATUS_" )
	    			$statuses[$name] = $value;
	    	}

	    	return $statuses;
	    }

	    public static function getPermissions(){
	    	$consts = static::getConstants();
	    	foreach($consts as $i=>$v)
	    		if( substr($i, 0, 11) !== "PERMISSION_" )
	    			unset($consts[$i]);

	    	return $consts;
	    }
	}
?>
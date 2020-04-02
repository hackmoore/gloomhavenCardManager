<?php
	require_once("singleton.php");

	class db extends singleton{
		const TYPE_OR = 1;
		const TYPE_AND = 2;

		const ACTION_POST = 1;
		const ACTION_PUT = 2;
		const ACTION_DELETE = 3;
		const ACTION_GET 	= 4;

		const SUPPORT_SESSIONS = 1;

		private const DEFAULT_FILTERS = array(
			'value'		=> '',
			'type'		=> db::TYPE_AND,
			'wildcard'	=> false,
			'in'		=> false,
			'not'		=> false
		);

		private static $db;
		private static $loaded = false;

		public static function loaded(){
			return static::$loaded;
		}

		private static function checkDB(){
			if( is_null(static::$db) ){
				// Connect to the database, currently only MySQL is supported
				static::$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_TBLE);
				if( !static::$db ){
					ob_clean();
					
					trigger_error("Failed to connect to the database with message: MySQLi Error:".mysqli_connect_errno() ."): ". mysqli_connect_error(), E_USER_ERROR);
				}
				static::$loaded = true;
			}
		}

		private static function escape($mixed){
			static::checkDB();

			if( is_array($mixed) && empty($mixed) )
				return $mixed;

			if( !is_array($mixed) ){
				$mixed2 = static::$db->real_escape_string($mixed);
			}else{
				foreach($mixed as $k=>$v)
					$mixed2[static::$db->real_escape_string($k)] = static::$db->real_escape_string($v);
			}
			return $mixed2;
		}

		private static function runQuery($template, $args = null){
			$args = static::escape($args);
			$query = vsprintf($template, $args);
			
			$result = static::$db->query($query);
			if(static::$db->errno > 0){
				trigger_error(sprintf("There was an error (%s) with your query with statement %s", static::$db->errno, static::$db->error), E_USER_ERROR);
				return false;
			}

			// Return a relevant result
			if( strpos($template, 'ON DUPLICATE') !== false){
				// An odd nieche case where INSERT query is also an update query
				return $result;
			}else if( strpos($template, 'INSERT') === 0 ){
				return static::$db->insert_id;
			}else if( $result === true || $result === false ){
				return $result;
			}else{
				$fields = $result->fetch_fields();
				$rows = [];

				// Loop through each row
				while($row = $result->fetch_row()){
					$tmp = [];

					// Loop through each field.
					foreach( $row as $key => $value){
						$tmp[$fields[$key]->name] = self::castField($value, $fields[$key]->type);
					}

					if( count($row) === 1 )
						array_push($rows, $tmp[$fields[$key]->name]);
					else
						array_push($rows, $tmp);
				}

				return $rows;
			}
		}

		private static function castField($value, $type){
			if(is_null($value)) return null; 

			switch ( $type )
			{
				// Convert INT to an integer.
				case MYSQLI_TYPE_TINY:
				case MYSQLI_TYPE_SHORT:
				case MYSQLI_TYPE_LONG:
				case MYSQLI_TYPE_LONGLONG:
				case MYSQLI_TYPE_INT24:
					return intval($value);
					break;

				// Convert FLOAT to a float.
				case MYSQLI_TYPE_FLOAT:
				case MYSQLI_TYPE_DOUBLE:
					return floatval($value);
					break;

				// Convert TIMESTAMP to a DateTime object.
				case MYSQLI_TYPE_TIMESTAMP:
				case MYSQLI_TYPE_DATE:
				case MYSQLI_TYPE_DATETIME:
					return new \DateTime($value);
					break;
				default:
					return $value;
					break;
			}
		}

		private static function buildInsertQuery($table, $data){
			$str = sprintf("INSERT INTO %s(%s) VALUES(", $table, implode(',', array_keys($data)));
			

			foreach($data  as $field => $value){
				$str .= sprintf("'%%s',");
			}

			// Remove the last character
			$str = substr($str, 0, -1);
			$str .= sprintf(");");

			return $str;
		}

		private static function buildUpdateQuery(String $table, array $data, int $id){
			$str = sprintf("UPDATE %s SET", $table);

			foreach($data as $field => $value){
				$str .= sprintf(" %s = '%%s', ", $field);
			}

			$str = substr($str, 0, -2);
			$str .= sprintf(" WHERE id = '%s';", $id);

			return $str;
		}

		private static function buildDeleteQuery(String $table, int $id){
			return sprintf("DELETE FROM %s WHERE id = '%s';", $table, $id);
		}


		// Taken from: https://stackoverflow.com/questions/2350052/how-can-i-get-enum-possible-values-in-a-mysql-database
		private static function get_enum_values( $table, $field )
		{
			$table = strtolower($table);
			
		    // $type = static::$db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
			$row = static::runQuery("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'")[0]['Type'];
		    preg_match("/^enum\(\'(.*)\'\)$/", $row, $matches);
		    $enum = explode("','", $matches[1]);
		    return $enum;
		}

/////// ACTUAL functions
		public static function getSessionPlayers(string $partyCode){
			return static::runQuery("SELECT pl.id, pl.name, cl.ClassName, pa.id as sid
				FROM classes cl
					LEFT JOIN players pl ON pl.classid = cl.id
					LEFT JOIN parties pa ON pl.partyid = pa.id
				WHERE pa.code = '%s'
				AND pl.retired = 0;", $partyCode);
		}

		public static function getPlayer(int $playerid){
			return static::runQuery("SELECT pl.id, pl.classid, pl.name, pl.level, pl.retired
				FROM players pl
				WHERE pl.id = '%s';", $playerid);
		}

		public static function getPlayerCards(int $playerid){
			return static::runQuery("SELECT ca.id, ca.name, ca.initiative, ca.level
				FROM cards ca
					LEFT JOIN player_cards pc ON pc.cardid = ca.id
				WHERE pc.playerid = '%s';", $playerid);
		}

		public static function getClassCards(int $classid){
			return static::runQuery("SELECT ca.*
				FROM cards ca
				WHERE ca.class = '%s'
				ORDER BY ca.level ASC, ca.initiative ASC;", $classid);
		}















		// Taken from: https://stackoverflow.com/questions/1727077/generating-a-drop-down-list-of-timezones-with-php
		public static function generate_timezone_list(){
		    static $regions = array(
		        \DateTimeZone::AFRICA,
		        \DateTimeZone::AMERICA,
		        \DateTimeZone::ANTARCTICA,
		        \DateTimeZone::ASIA,
		        \DateTimeZone::ATLANTIC,
		        \DateTimeZone::AUSTRALIA,
		        \DateTimeZone::EUROPE,
		        \DateTimeZone::INDIAN,
		        \DateTimeZone::PACIFIC,
		    );

		    $timezones = array();
		    foreach( $regions as $region )
		    {
		        $timezones = array_merge( $timezones, \DateTimeZone::listIdentifiers( $region ) );
		    }

		    $timezone_offsets = array();
		    foreach( $timezones as $timezone )
		    {
		        $tz = new \DateTimeZone($timezone);
		        $timezone_offsets[$timezone] = $tz->getOffset(new \DateTime);
		    }

		    // sort timezone by offset
		    asort($timezone_offsets);

		    $timezone_list = array();
		    foreach( $timezone_offsets as $timezone => $offset )
		    {
		        $offset_prefix = $offset < 0 ? '-' : '+';
		        $offset_formatted = gmdate( 'H:i', abs($offset) );

		        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

		        $timezone_list[$timezone] = array(
		        	'offset' => $pretty_offset,
		        	'pretty' => $timezone
		        );
		    }

		    return $timezone_list;
		}

	}
?>
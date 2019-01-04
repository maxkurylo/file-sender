<?php

require_once "getconfigdata.php";

class Database {

	protected $host;
	protected $user;
	protected $password;
	protected $database;
	protected $table;
	protected $link;
	protected $error_log = array();

	//public functions

	function __construct() {
		$db_config = $this->read_db_config();
		if ($db_config) {
			$this->host = $db_config["host"];
			$this->database = $db_config["db_name"];
			$this->user = $db_config["user"];
			$this->password = $db_config["password"];
			$this->table = $db_config["users_table"];
		}
	}

	function __destruct() {
		mysqli_close($this->link);
	}

	public function get_error_log() {
		return $this->error_log;
	}

	public function connect_to_db() {
		$this->link = mysqli_connect($this->host, $this->user, $this->password, $this->database);
		if(mysqli_connect_errno($this->link)) {
			$this->error(mysqli_connect_error($this->link));
			return false;
		}
		return true;
	}

	public function post($data, $table = "") {
		if ($table == "")
			$table = $this->table;

		$query = "INSERT INTO $table (";
		foreach ($data as $key => $value) {
			$query .= "$key, ";
		}
		$query .= ") VALUES (";
		foreach ($data as $key => $value) {
			$query .= "'$value', ";
		}
		$query .= ")";
		$query = str_replace(", )", ")", $query);

		if (mysqli_query($this->link, $query)) {
			return true;
		}
		$this->error(mysqli_error($this->link));
		return false;
	}

	//проверить с невалидными данными бд и плохим запросом
	public function get($login, $table = "") {
		if ($table == "")
			$table = $this->table;

		$query = "SELECT * FROM $table WHERE user_login = '$login'";

		$result = array();

		$rows = mysqli_query($this->link, $query);

		if (mysqli_error_list($this->link)) {
			$this->error(mysqli_error($this->link));
		}
		elseif(mysqli_num_rows($rows) == 0) {
			$this->error("Incorrect login");
		}
		else {
			while($row = mysqli_fetch_array($rows, MYSQLI_ASSOC)) {
				array_push($result, $row);
			}
		}

		return $result;
	}


	public function update($data, $table = "") {
		if ($table == "")
			$table = $this->table;

		$query = "UPDATE `$table` SET ";

		foreach ($data as $key => $value) {
			if ($key == "id")
				continue;
			$query .= "`$key` = '$value', ";
		}
		$query = preg_replace('/,\s$/', " ", $query);

		$query .= "WHERE id = " . $data["id"];

		if (mysqli_query($this->link, $query)) {
			return true;
		}
		$this->error(mysqli_error($this->link));
		return false;
		
	}

	public function delete($id, $table = "") {
		if ($table == "")
			$table = $this->table;
		
		$query = "DELETE FROM $table WHERE id = $id LIMIT 1";

		if (mysqli_query($this->link, $query)) {
			return true;
		}
		$this->error(mysqli_error($this->link));
		return false;
	}

	public function drop_table($table_name) {
		$query = "DROP TABLE IF EXISTS $table_name";

		if (mysqli_query($this->link, $query)) {
			return true;
		}
		$this->error(mysqli_error($this->link));
		return false;
	}

	public function create_table() {
		// $form_config = get_data_from_form_config();
		// $table_name = get_data_from_config("database");
		// $table_name = $table_name["table_name"];

		// $query = "CREATE TABLE IF NOT EXISTS $table_name ( `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, ";

		// //iterate over blocks
		// foreach ($form_config as $_ => $block) {
		// 	//iterate over fields in block
		// 	foreach ($block as $field_name => $_) {
		// 		$query .= "`$field_name` TINYTEXT, ";
		// 	}
		// }

		// $query .= "PRIMARY KEY(`id`) )";

		// if (mysqli_query($this->link, $query)) {
		// 	return true;
		// }
		// $this->error(mysqli_error($this->link));
		return false;
	}

	//private functions

	protected function read_db_config() {
		return get_data_from_config("database");
	}

	protected function error($err) {
		array_push($this->error_log, $err);
	}
}

?>
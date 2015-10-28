<?php
// Lite MySQLi controller.
// Copyright (C) 2015  Cyriaque Skrapits
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// TODO
// ----
// - Improve the WHERE verification in the "find" method.
// - Is the database really needed to be given in CRUD functions ?
//

class Database {

	//
	// Private
	//

	/**
	 * Sanitize an array for queries.
	 *
	 * @param  mysqli $link	Database connection.
	 * @param  array $array	Array to sanitize.
	 * @return array		Sanitized array.
	 */
	private static function _escapeArray($link, $array) {
		$newArray = array();

		foreach ($array as $key => $value) {
			$newKey = $link->real_escape_string($key);
			$newValue = $link->real_escape_string($value);
			$newArray[$newKey] = $newValue;
		}

		return $newArray;
	}

	private static function _free($results) {
		mysqli_free_result($results);
	}

	private static function _query($link, $query) {

		if (!($results = mysqli_query($link, $query))) {
			//mysqli_free_result($results);
			return mysqli_error($link);
		}

		return $results;
	}

	private static function _resultsToArray($results) {
		$array = array();
		while($row = mysqli_fetch_assoc($results)) {
			array_push($array, $row);
		}
		return $array;
	}

	//
	// Public
	//

	/**
	 * Close the link. No shit Sherlock !
	 *
	 * @param  mysqli $link		Database connection
	 * @return boolean			Closed ?
	 */
	public static function close($link) {
		return mysqli_close($link);
	}

	/**
	 * Database connection method.
	 *
	 * @return mixed False if failed, mysqli object if OK.
	 */
	public static function connect($host, $user, $password, $database) {
		// Connection.
		$link = mysqli_connect($host, $user, $password, $database);

		// Check if the connection has been established.
		if (mysqli_connect_errno()) {
			Utils::response(false, array(false => mysqli_connect_error()));
		}

		$link->set_charset("utf8");

		return $link;
	}

	/**
	 * Find a record in the database by criterions.
	 *
	 * Usage :
	 * 	Database::find($link, 'table', array('id' => 0));
	 *
	 * @param  mysqli $link   	Database connection
	 * @param  string $table  	Table to operate
	 * @param  array $fields 	Criterions
	 * @return array         	Array of records
	 */
	public static function find($link, $table, $fields = array()) {
		// Sanitize the fields.
		$fields = self::_escapeArray($link, $fields);

		// Build criterions.
		// Array of `keys` LIKE 'value'.
		$criterions = array();
		foreach ($fields as $key => $value) {
			//foreach (explode(';', $values) as $value) {
				$criterion = '`' . $key . '` LIKE \'' . $value . '\'';
				array_push($criterions, $criterion);
			//}
		}

		// Build the query.
		// TODO : Improve the WHERE verification.
		$query = 'SELECT * FROM `' . $table . (count($fields) > 0 ? '` WHERE ' : '');
		$query .= implode(' AND ', $criterions); // Criterions

		// Query then converts the result to an array.
		$results = self::_query($link, $query);
		$array = self::_resultsToArray($results);
		self::_free($results);

		return $array;
	}

	/**
	 * Shortcut for the find() method that allows to get all the records a
	 * table.
	 *
	 * Usage :
	 * 	Database::findAll($link, 'table');
	 *
	 * @param  mysqli $link		Database connection
	 * @param  string $table	Table to operate
	 * @return array			Array of records
	 */
	public static function findAll($link, $table) {
		return self::find($link, $table);
	}

	/**
	 * Insertion query.
	 *
	 * Usage :
	 * 	Database::insert($link, 'database', 'table', array('name' => 'Joe'))
	 *
	 * Return values :
	 * 	0 : 		Insertion failed.
	 * 	Others : 	ID
	 *
	 * @param  mysqli $link		Database link.
	 * @param  string $table	Table to operate.
	 * @param  array $fields	Keys/values to insert.
	 * @return int				Id of the insertion. 0 if failed.
	 */
	public static function insert($link, $database, $table, $fields) {
		// Sanitize the fields.
		$fields = self::_escapeArray($link, $fields);

		// Build the query.
		$query = 'INSERT INTO `' . $database . '`.`' . $table . '` (`id`,`';
		$query .= implode('`,`', array_keys($fields)); // Keys
		$query .= '`) VALUES (NULL,\'';
		$query .= implode('\',\'', array_values($fields)); // Values
		$query .= '\');';

		self::_query($link, $query);

		// Return the ID.
		return $link->insert_id;
	}

	/**
	 * Removal query.
	 *
	 * Usage :
	 * 	Database::remove($link, 'database', 'table', array('id' => 1))
	 *
	 * @param  mysqli $link		Database link
	 * @param  string $database	Database
	 * @param  string $table	Table to operate
	 * @param  array $where		Criterions
	 * @return boolean			Removed ?
	 */
	public static function remove($link, $database, $table, $where) {
		// Build where criterions.
		// Array of `keys` = 'value'.
		$criterions = array();
		foreach ($where as $key => $value) {
			$criterion = '`' . str_replace('.', '`.`', $key) . '` = \'' . $value . '\'';
			array_push($criterions, $criterion);
		}

		// Build the query.
		$query = 'DELETE FROM `' . $database . '`.`' . $table . '` WHERE ';
		$query .= implode(' AND ', $criterions) . ';';

		self::_query($link, $query);

		return $link->affected_rows > 0;
	}

	public static function sanitze($results, $flags) {
		function sanitizer ($value) {
			return htmlentities($value, ENT_NOQUOTES);
		};
		foreach ($array as &$result) {
			$result = array_map('sanitizer', $result);
		}
	}

	/**
	 * Update a or many records.
	 *
	 * Usage :
	 * 	Database::update($link, 'table', array('name' => 'Georges'), array('id' => 2))
	 *
	 * @param  mysqli $link		Database connection
	 * @param  string $table	Table to operate
	 * @param  array $set		Fields to update
	 * @param  array $where		Criterions
	 * @return boolean			Updated ?
	 */
	public static function update($link, $table, $set, $where) {
		// Sanitize the fields.
		$set = self::_escapeArray($link, $set);
		$where = self::_escapeArray($link, $where);

		// Build definitions.
		// Array of `keys` = 'value'.
		$definitions = array();
		foreach ($set as $key => $value) {
			$definition = '`' . $key . '` = \'' . $value . '\'';
			array_push($definitions, $definition);
		}

		// Build where criterions.
		// Array of `keys` = 'value'.
		$criterions = array();
		foreach ($where as $key => $value) {
			$criterion = '`' . str_replace('.', '`.`', $key) . '` = \'' . $value . '\'';
			array_push($criterions, $criterion);
		}

		// Build the query.
		$query = 'UPDATE `' . DB_DATABASE . '`.`' . $table . '` SET ';
		$query .= implode(', ', array_values($definitions)); // Keys
		$query .= ' WHERE ';
		$query .= implode(' AND ', array_values($criterions)); // Values
		$query .= ';';

		self::_query($link, $query);

		return $link->affected_rows > 0;
	}
}
?>

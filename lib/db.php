<?php
/**
 * Class User
 *
 * This class represents a user in the system. It contains properties and methods
 * to manage user data and interactions.
 *
 * Properties:
 * - $id: The unique identifier for the user.
 * - $name: The name of the user.
 * - $email: The email address of the user.
 * - $password: The hashed password of the user.
 *
 * Methods:
 * - __construct($id, $name, $email, $password): Initializes a new instance of the User class.
 * - getId(): Returns the user's unique identifier.
 * - getName(): Returns the user's name.
 * - getEmail(): Returns the user's email address.
 * - getPassword(): Returns the user's hashed password.
 * - setName($name): Sets the user's name.
 * - setEmail($email): Sets the user's email address.
 * - setPassword($password): Sets the user's hashed password.
 */
class JsonDB {
    private $filePath;

    /**
     * Constructor for the class.
     *
     * Initializes the object with the given file path. If the file does not exist,
     * it creates a new file at the specified path and writes an empty JSON array to it.
     *
     * @param string $filePath The path to the file to be used.
     */
    public function __construct($filePath) {
        $this->filePath = $filePath;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([]));
        }
    }

    /**
     * Reads data from a JSON file specified by the filePath property.
     *
     * @return array The decoded JSON data as an associative array.
     */
    private function readData() {
        $json = file_get_contents($this->filePath);
        return json_decode($json, true);
    }

    /**
     * Writes the provided data to a JSON file.
     *
     * This method encodes the given data array into a JSON formatted string
     * with pretty print formatting and writes it to the file specified by
     * the $filePath property.
     *
     * @param array $data The data to be written to the JSON file.
     *
     * @return void
     */
    private function writeData($data) {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json);
    }

    /**
     * Inserts a new record into the data storage.
     *
     * @param array $record The record to be inserted.
     * @return void
     */
    public function insert($record) {
        $data = $this->readData();
        $data[] = $record;
        $this->writeData($data);
    }

    /**
     * Updates a record at the specified index.
     *
     * This method reads the current data, checks if a record exists at the given index,
     * and updates it with the provided record. If the index does not exist, an exception is thrown.
     *
     * @param int $index The index of the record to update.
     * @param mixed $record The new record to replace the existing one.
     * @throws Exception If the record at the specified index is not found.
     */
    public function update($index, $record) {
        $data = $this->readData();
        if (isset($data[$index])) {
            $data[$index] = $record;
            $this->writeData($data);
        } else {
            throw new Exception("Record not found.");
        }
    }

    /**
     * Deletes a record from the data array at the specified index.
     *
     * This method reads the current data, checks if the specified index exists,
     * and if it does, removes the record at that index and writes the updated data back.
     * If the index does not exist, it throws an exception.
     *
     * @param int $index The index of the record to delete.
     * @throws Exception If the record at the specified index is not found.
     */
    public function delete($index) {
        $data = $this->readData();
        if (isset($data[$index])) {
            array_splice($data, $index, 1);
            $this->writeData($data);
        } else {
            throw new Exception("Record not found.");
        }
    }

    /**
     * Retrieves a record from the data based on the provided index.
     *
     * @param int|string $index The index of the record to retrieve.
     * @return mixed The record data corresponding to the provided index.
     * @throws Exception If the record is not found.
     */
    public function get($index) {
        $data = $this->readData();
        if (isset($data[$index])) {
            return $data[$index];
        } else {
            throw new Exception("Record not found.");
        }
    }

    /**
     * Fetches records based on a specified condition.
     *
     * This method reads the current data and filters it based on the provided callback function.
     * The callback function should accept a record as its parameter and return true if the record
     * matches the condition, or false otherwise.
     *
     * @param callable $callback The callback function to use for filtering records.
     * @return array An array of records that match the specified condition.
     */
    public function fetch(callable $callback) {
        $data = $this->readData();
        return array_filter($data, $callback);
    }

    /**
     * Retrieves all records from the database.
     *
     * @return array An array of all records.
     */
    public function getAll() {
        return $this->readData();
    }
}
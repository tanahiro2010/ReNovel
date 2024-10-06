<?php
class JsonDB {
    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([]));
        }
    }

    private function readData() {
        $json = file_get_contents($this->filePath);
        return json_decode($json, true);
    }

    private function writeData($data) {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json);
    }

    public function insert($record) {
        $data = $this->readData();
        $data[] = $record;
        $this->writeData($data);
    }

    public function update($index, $record) {
        $data = $this->readData();
        if (isset($data[$index])) {
            $data[$index] = $record;
            $this->writeData($data);
        } else {
            throw new Exception("Record not found.");
        }
    }

    public function delete($index) {
        $data = $this->readData();
        if (isset($data[$index])) {
            array_splice($data, $index, 1);
            $this->writeData($data);
        } else {
            throw new Exception("Record not found.");
        }
    }

    public function get($index) {
        $data = $this->readData();
        if (isset($data[$index])) {
            return $data[$index];
        } else {
            throw new Exception("Record not found.");
        }
    }

    public function getAll() {
        return $this->readData();
    }
}
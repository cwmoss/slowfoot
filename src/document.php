<?php

namespace slowfoot;

use ArrayAccess;
use JsonSerializable;

class document implements ArrayAccess, JsonSerializable {

    public function __construct(
        public string $_id,
        public string $_type,
        // public string $_src,
        private array $data
    ) {
    }
    public function update(array $data) {
        $this->data = array_merge($this->data, $data);
    }

    public function jsonSerialize(): mixed {
        return ["_id" => $this->_id, "_type" => $this->_type, ...$this->data];
    }
    static public function new(array $data): self {
        $id = $data['_id'];
        $type = $data['_type'];
        unset($data['_id'], $data['_type']);
        return new self($id, $type, $data);
    }

    public function __get($prop) {
        return $this->data[$prop] ?? null;
    }

    public function offsetSet($offset, $value): void {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset): mixed {
        if ($offset == "_id") return $this->_id;
        if ($offset == "_type") return $this->_type;
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}

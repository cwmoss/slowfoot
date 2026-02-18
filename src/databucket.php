<?php

class databucket implements ArrayAccess {

    private $is_array = null;

    public function __construct(private array|object $data) {
        $this->is_array = is_array($data);
    }

    public function __get($prop) {
        if ($this->is_array) {
            $d = $this->data[$prop] ?? null;
        } else {
            $d = $this->data->$prop ?? null;
        }
        if (is_null($d)) return null;
        if (is_scalar($d)) return $d;
        return new self($d);
    }

    public function offsetSet($offset, $value): void {
    }

    public function offsetExists($offset): bool {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void {
    }

    public function offsetGet($offset): mixed {
        return $this->__get($offset);
    }

    public function unwrap() {
        return $this->data;
    }

    public function unwrap_as_arrays() {
        return self::object_to_array($this->data);
    }

    public function unwrap_as_objects() {
        return self::array_to_object($this->data);
    }

    public function with(string $key, $value): self {
        if ($this->is_array) $this->data[$key] = $value;
        else $this->data->$key = $value;
        return $this;
    }

    static public function object_to_array($object) {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        return array_map(self::object_to_array(...), (array) $object);
    }

    static public function array_to_object($array) {
        if (!is_array($array)) {
            return $array;
        }
        // return is_array($array) ? (object) array_map([__CLASS__, __METHOD__], $array) : $array;
        return (object) array_map(self::array_to_object(...), $array);
    }
}


// https://www.php.net/manual/en/class.arrayaccess.php
// https://www.php.net/manual/en/class.arrayobject.php

/*

$test = [
    'name' => 'David Hayes',
    'age' => 33,
    'hair' => 'bald',
    'to_buy' => [
        'bananas',
        'onion',
        'chocolate'
    ],
    "mother" => ["name" => "maria"]
];

$d = new databucket($test)->with("title", "a person");

echo $d->mother->name, $d->to_buy->{2}, $d->title, "\n";
echo $d['mother']['name'], $d['to_buy'][2], $d["title"], "\n";

var_dump($d['to_buy']);

$test = databucket::array_to_object($test);

$d = new databucket($test)->with("title", "a person");

echo $d->mother->name, $d->to_buy->{2}, $d->title, "\n";
echo $d['mother']['name'], $d['to_buy'][2], $d["title"], "\n";

var_dump($d['to_buy']);
*/
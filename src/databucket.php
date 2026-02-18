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

    public function get_path(array|string $path, $default = null) {
        if (!is_array($path)) $path = explode(".", $path);
        $current = $this->data;
        foreach ($path as $key) {
            if (is_array($current)) {
                if (!isset($current[$key])) {
                    $current = $default;
                    break;
                } else {
                    $current = $current[$key];
                }
            } elseif (is_object($current)) {
                if (!isset($current->$key)) {
                    $current = $default;
                    break;
                } else {
                    $current = $current->$key;
                }
            } else {
                $current = $default;
                break;
            }
        }
        if (is_null($current)) return null;
        if (is_scalar($current)) return $current;
        return new self($current);
        // return $current;
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
class image {

    public function __construct(public int $w, public int $h) {
    }
    public function size() {
        return sprintf("%sx%s", $this->w, $this->h);
    }
}

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

$d = new databucket($test)->with("title", "a person")->with("img", new image(300, 200));

echo $d->mother->name, $d->to_buy->{2}, $d->title, $d->img->w, "\n";
echo $d['mother']['name'], $d['to_buy'][2], $d["title"], $d["img"]["w"], "\n";

var_dump($d['to_buy']);

echo $d->img->unwrap()->size();
echo $d['img']->unwrap()->size();

$test = databucket::array_to_object($test);

$d = new databucket($test)->with("title", "a person")->with("img", new image(300, 200));

echo $d->mother->name, $d->to_buy->{2}, $d->title, $d->img->w, "\n";
echo $d['mother']['name'], $d['to_buy'][2], $d["title"], $d["img"]["w"], "\n";

var_dump($d['to_buy']);


echo $d->img->unwrap()->size(), "\n";
echo $d['img']->unwrap()->size(), "\n";

$d = new databucket(new image(300, 200));
echo $d->unwrap()->size(), "\n";
echo $d["w"], "\n";
echo $d->h, "\n";

$d = new databucket($test)->with("title", "a person")->with("img", new image(300, 200));

echo $d->get_path("mother.0.name"), "\n";
echo $d->get_path("mother.name"), "\n";
echo $d->get_path("to_buy.0"), "\n";
echo $d->get_path("to_buy.0.price"), "\n";
print_r($d->get_path("mother"));
*/
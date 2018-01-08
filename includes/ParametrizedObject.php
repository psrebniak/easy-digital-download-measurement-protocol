<?php

class ParametrizedObject implements \JsonSerializable
{
    protected $params = [];
    protected $data = [];

    public function __call($method, $args)
    {
        $action = substr($method, 0, 3);
        $param = substr($method, 3);

        if ($action === "set" && array_key_exists($param, $this->params)) {
            $key = $this->params[$param];
            $this->data[$key] = $args[0];

            return $this;
        }

        if ($action === "get") {
            if (isset($this->data[$param])) {
                return $this->data[$param];
            }
            return null;
        }

        throw new \InvalidArgumentException();
    }

    function jsonSerialize()
    {
        return $this->data;
    }
}

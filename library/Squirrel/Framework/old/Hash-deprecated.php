<?php namespace Squirrel;

class Hash extends Crypto {
    protected $algorithm;
    protected $data;

    public function __construct($algorithm) {
        $this->algorithm = $algorithm;
        $this->data      = '';
    }

    public function update($data) {
        $this->data .= $data;
        return $this;
    }

    public function digest($encoding) {
        return $this->encode($encoding, hash($this->algorithm, $this->data, true));
    }
}

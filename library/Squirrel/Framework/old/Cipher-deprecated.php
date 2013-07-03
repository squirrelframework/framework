<?php namespace Squirrel;

/**
 * Provides encryption and decryption
 * functionalities using mcrypt extension
 * and rijndael algorithm.
 *
 * @package  Squirrel
 * @category Encryption
 * @author   Valérian
 */
class Cipher {

    /**
     * @var string cypher key
     */
    protected $key;

    /**
     * @var string initialization vector
     */
    protected $iv;

    /**
     * @var string data to digest
     */
    protected $data;

    /**
     * Appends given data.
     *
     * @param  string
     * @return $this
     */
    public function update($data) {

        $this->data .= $data;

        return $this;
    }

    /**
     * Encrypts class data in given encoding.
     *
     * @param  string encoding
     * @return string encrypted data
     */
    public function encrypt() {

        $data = mcrypt_encrypt(
            MCRYPT_RIJNDAEL_256,
            $this->key,
            $this->data,
            MCRYPT_MODE_CBC,
            $this->iv
        );

        return String::cast($data);
    }

    /**
     * Decrypts class data in given encoding.
     *
     * @param  string encoding
     * @return string decrypted data
     */
    public function decrypt($encoding) {
        
        $data = $this->decode($encoding, $this->data);

        $data = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256,
            $this->key,
            $binary,
            MCRYPT_MODE_CBC,
            $this->iv
        );

        return String::cast($data);
    }

    /**
     * Sets key and initialization vector.
     *
     * @param  string key
     * @param  string initialization vector
     * @return void
     */
    public function __construct($key, $iv) {

        $this->key  = $key;
        $this->iv   = $iv;
        $this->data = '';
    }
}

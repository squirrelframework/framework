<?php namespace Squirrel;

class Encoding extends Object {

    /**
     * @var squirrel\Config
     */
    protected static $config;

    /**
     * @var string
     */
    protected $data;

    /**
     * Calls given encoding callback with given data.
     *
     * @param  string encoding
     * @param  string name
     * @param  string data
     * @return string processed data
     */
    protected static function call($encoding, $name, $data) {

        if (!self::$config->has($encoding)) {

            throw new Exceptions\Encoding(
                'Unknown encoding \':encoding\'',
                array(':encoding' => $encoding)
            );
        }

        return Callback::cast(self::$config->find($encoding, $name))
            ->call($data);
    }

    /**
     * Gets static configuration for encodings.
     *
     * @return void
     */
    public function __construct() {

        if (isset(self::$config)) {

            return;
        }
        
        // Get configuration
        self::$config = Config::instance('encodings');
    }

    /**
     * Encodes data with given encoding.
     *
     * @param  string encoding
     * @return string encoded data
     */
    public function encode($encoding) {

        return self::call($encoding, 'encode', $data);
    }

    /**
     * Decodes data with given encoding.
     *
     * @param  string encoding
     * @return string decoded data
     */
    public function decode($encoding) {

        return self::call($encoding, 'decode', $data);
    }
}

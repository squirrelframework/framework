<?php namespace Squirrel;

/**
 * Provides encoding and decoding
 * functionalities for encryption classes.
 *
 * @package  Squirrel
 * @category Encryption
 * @author   Valérian
 */
abstract class Crypto extends Object {

    // Encoding constants
    const ENCODING_BINARY = 'binary';
    const ENCODING_HEX    = 'hex';
    const ENCODING_BASE64 = 'base64';

    /**
     * Encodes given data with given encoding.
     *
     * @throws squirrel\exceptions\Encoding
     * @param  string encoding
     * @param  string data
     * @return string encoded data
     */
    protected function encode($encoding, $data) {

        switch ($encoding) {

            case static::ENCODING_BINARY:

                return $data;
                break;

            case static::ENCODING_HEX:

                return String::cast($data)->encode()->toString(); // bin2hex($data);
                break;

            case static::ENCODING_BASE64:

                return String::cast($data)->encodeBase64()->toString(); // base64_encode($data);
                break;

            default:

                throw new Exceptions\Encoding(
                    'Undefined encoding \':encoding\'',
                    array(':encoding' => $encoding)
                );
        }
    }

    /**
     * Decodes given data with given encoding.
     *
     * @param  string encoding
     * @param  string data
     * @return string decoded data
     */
    protected function decode($encoding, $data) {

        switch ($encoding) {

            case 'binary':

                return $data;
                break;

            case 'hex':

                return Shex2bin($data);
                break;

            case 'base64':

                return base64_decode($data);
                break;

            default:

                throw new Exceptions\Encoding(
                    'Undefined encoding \':encoding\'',
                    array(':encoding' => $encoding)
                );
        }
    }
}

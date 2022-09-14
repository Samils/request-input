<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\RequestInput
 * - Autoload, application dependencies
 *
 * MIT License
 *
 * Copyright (c) 2020 Ysare
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Sammy\Packs\RequestInput {
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists('Sammy\Packs\RequestInput\Base')){
  /**
   * @class Base
   * Base internal class for the
   * RequestInput module.
   * -
   * This is (in the ils environment)
   * an instance of the php module,
   * wich should contain the module
   * core functionalities that should
   * be extended.
   * -
   * For extending the module, just create
   * an 'exts' directory in the module directory
   * and boot it by using the ils directory boot.
   * -
   */
  class Base {
    /**
     * @method array getRequestInput
     *
     * Get the server request input based on the sent
     * content type from the request.
     * @return array
     */
    public final function getRequestInput () {

      $headers = self::getHeaders ();

      $contentTypesRe = '/^((text|application)\/(.+))/i';
      $contentType = 'text/json';

      if ( isset ($headers ['Content-Type']) ) {
        $contentType = $headers['Content-Type'];
      }

      if ( preg_match ( $contentTypesRe, $contentType, $match) ) {
        $phpRawInput =  static::getPhpRawInput ();

        $contentTextType = ucfirst (strtolower ($match[ 3 ]));
        $contentTextTypeParserName = 'parse' . $contentTextType;

        if (method_exists ($this, $contentTextTypeParserName)) {
          return call_user_func_array ([$this, $contentTextTypeParserName], [ $phpRawInput ]);
        }
      }

      return [];
    }

    protected final function parseJson ( $phpRawInput ) {
      $jsonData = self::jsonObject2Array (
        json_decode ( $phpRawInput )
      );

      return !is_array ($jsonData) ? [] : (
        $jsonData
      );
    }

    protected final function parseYaml ( $phpRawInput ) {
      $yamlLite = requires ('yaml-lite');

      if ( !$yamlLite ) return array ();

      $yamlData = $yamlLite->parse_yaml_string (
        $phpRawInput
      );

      return !is_array ($yamlData) ? [] : $yamlData;
    }

    protected final function parseXml ( $phpRawInput ) {
      $xm1rray = requires ('xm1rray');

      return self::jsonObject2Array (
        $xm1rray->parse_string ( $phpRawInput )
      );
    }

    protected final function getPhpRawInput () {
      return !function_exists('file_get_contents') ? null : (
        call_user_func_array('file_get_contents', array (
          'php://input'
        ))
      );
    }

    private static function jsonObject2Array ($object) {
      if (!(is_object ($object))) {
        return $object;
      }

      $object = ((array)($object));
      $ob = [];

      foreach ($object as $k => $v) {
        $ob[ $k ] = self::jsonObject2Array ( $v );
      }

      return is_array ($ob) ? $ob : (
        ((array)( $ob ))
      );
    }

    public static function RequestInput () {
      return call_user_func_array (
        [new static, 'getRequestInput'],
        func_get_args ()
      );
    }

    protected static function getHeaders () {
      if (function_exists ('getallheaders')) {
        return getallheaders ();
      }

      return [];
    }
  }}
}

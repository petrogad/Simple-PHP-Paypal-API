<?php
/*
The MIT License (MIT)

Copyright (c) 2013 

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
class Paypal
{

    public static $_debugMode = FALSE;

    /**
     * Last error message(s)
     * @var array
     */
    protected $_errors = array();

    /**
     * API Credentials - Staging
     */
    protected $_sandboxCredentials = array(
        'USER' => '', //@TODO: Set User Id
        'PWD' => '', //@TODO: Set Password
        'SIGNATURE' => '', //@TODO: Set Signature
    );
    protected $_staginEndPoint = 'https://api-3t.sandbox.paypal.com/nvp';
    private static $_stagingPostForPayment = ' https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=';

    /**
     * API Credentials - Live
     */
    protected $_liveCredentials = array(
        'USER' => '', //@TODO: Set User Id
        'PWD' => '', //@TODO: Set Password
        'SIGNATURE' => '', //@TODO: Set Signature
    );
    protected $_endPoint = 'https://api-3t.paypal.com/nvp';
    private static $_postForPayment = ' https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=';

    /**
     * API Version
     * @var string
     */
    protected $_version = '97.0'; //@TODO: Set Version

    public static function getPostRequestUrl(){
        if(Paypal::$_debugMode){
            return Paypal::$_stagingPostForPayment;
        }
        else {
            return Paypal::$_postForPayment;
        }
    }

    /**
     * Make API request
     *
     * @param string $method string API method to request
     * @param array $params Additional request parameters
     * @return array / boolean Response array / boolean false on failure
     */
    public function request($method, $params = array())
    {
        $this->_errors = array();
        if (empty($method)) { //Check if API method is not empty
            $this->_errors = array('API method is missing');
            return false;
        }

        //Our request parameters
        $requestParams = array(
                'METHOD' => $method,
                'VERSION' => $this->_version
            ) + $this->getCredentials();

        //Building our NVP string
        $request = http_build_query($requestParams + $params);

        //cURL settings
        $curlOptions = array(
            CURLOPT_URL => $this->getEndPoint(),
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        //Sending our request - $response will hold the API response
        $response = curl_exec($ch);

        //Checking for cURL errors
        if (curl_errno($ch)) {
            $this->_errors = curl_error($ch);
            curl_close($ch);
            return false;
            //Handle errors
        } else {
            curl_close($ch);
            $responseArray = array();
            parse_str($response, $responseArray); // Break the NVP string to an array
            return $responseArray;
        }
    }

    private function getCredentials()
    {
        if (Paypal::$_debugMode) {
            return $this->_sandboxCredentials;
        } else {
            return $this->_liveCredentials;
        }
    }

    private function getEndPoint()
    {
        if (Paypal::$_debugMode) {
            return $this->_staginEndPoint;
        } else {
            return $this->_endPoint;
        }
    }
}
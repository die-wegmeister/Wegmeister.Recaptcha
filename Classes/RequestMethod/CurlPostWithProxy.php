<?php

namespace Wegmeister\Recaptcha\RequestMethod;

use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestMethod\Curl;
use ReCaptcha\RequestParameters;

class CurlPostWithProxy implements RequestMethod
{
    /**
     * Curl connection to the reCAPTCHA service
     * @var Curl
     */
    private $curl;

    /**
     * URL for reCAPTCHA siteverify API
     * @var string
     */
    private $siteVerifyUrl;

    /**
     * Only needed if you want to override the defaults
     *
     * @param Curl $curl Curl resource
     * @param string $siteVerifyUrl URL for reCAPTCHA siteverify API
     */
    public function __construct(Curl $curl = null, $siteVerifyUrl = null)
    {
        $this->curl = (is_null($curl)) ? new Curl() : $curl;
        $this->siteVerifyUrl = (is_null($siteVerifyUrl)) ? ReCaptcha::SITE_VERIFY_URL : $siteVerifyUrl;
    }

    /**
     * Submit the cURL request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {
        $handle = $this->curl->init($this->siteVerifyUrl);

        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params->toQueryString(),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLINFO_HEADER_OUT => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
        );

        if (isset($_SERVER["http_proxy"])) {
            $proxy = trim(
                preg_replace('/^http\:\/\//i', '', $_SERVER["http_proxy"]),
                '/'
            );
            $proxy = explode(':', $proxy);
            $options[CURLOPT_RETURNTRANSFER] = 1;
            $options[CURLOPT_PROXY] = $proxy[0];
            $options[CURLOPT_PROXYPORT] = $proxy[1];
        }

        $this->curl->setoptArray($handle, $options);

        $response = $this->curl->exec($handle);
        $this->curl->close($handle);

        if ($response !== false) {
            return $response;
        }

        return '{"success": false, "error-codes": ["'.ReCaptcha::E_CONNECTION_FAILED.'"]}';
    }
}

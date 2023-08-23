<?php

namespace Wegmeister\Recaptcha\RequestMethod;

use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestMethod\Curl;
use ReCaptcha\RequestParameters;
use Neos\Flow\Annotations as Flow;

/**
 * Copy of \ReCaptcha\RequestMethod\CurlPost()
 * adding functionality for curl to work with a http proxy
 */
class CurlPostWithProxy implements RequestMethod
{
    /**
     * Curl connection to the reCAPTCHA service
     *
     * @var Curl
     */
    private $curl;

    /**
     * URL for reCAPTCHA siteverify API
     *
     * @var string
     */
    private $siteVerifyUrl;

    /**
     * Recaptcha settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Inject the settings
     *
     * @param array $settings The settings to inject.
     *
     * @return void
     */
    public function injectSettings(array $settings)
    {
        if (empty($settings['httpProxy'])) {
            throw new \Exception("Missing configuration, please add the following settings: 'Wegmeister.Recaptcha.httpProxy'");
        }
        $this->settings = $settings;
    }


    /**
     * Only needed if you want to override the defaults
     *
     * @param Curl   $curl          Curl resource
     * @param string $siteVerifyUrl URL for reCAPTCHA siteverify API
     */
    public function __construct(Curl $curl = null, $siteVerifyUrl = null)
    {
        $this->curl          = (is_null($curl)) ? new Curl() : $curl;
        $this->siteVerifyUrl = (is_null($siteVerifyUrl)) ? ReCaptcha::SITE_VERIFY_URL : $siteVerifyUrl;
    }

    /**
     * Submit the cURL request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     *
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {
        $handle = $this->curl->init($this->siteVerifyUrl);

        $options = [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $params->toQueryString(),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLINFO_HEADER_OUT    => false,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        $httpProxy = $this->settings['httpProxy'];
        $this->emitHttpProxyRetrieved($httpProxy);

        $proxy                           = explode(':', $httpProxy);
        $options[CURLOPT_RETURNTRANSFER] = 1;
        $options[CURLOPT_PROXY]          = $proxy[0];
        $options[CURLOPT_PROXYPORT]      = $proxy[1];

        $this->curl->setoptArray($handle, $options);

        $response = $this->curl->exec($handle);
        $this->curl->close($handle);

        if ($response !== false) {
            return $response;
        }

        return '{"success": false, "error-codes": ["' . ReCaptcha::E_CONNECTION_FAILED . '"]}';
    }

    /**
     * @param string $httpProxy
     *
     * @return void
     * @Flow\Signal
     */
    protected function emitHttpProxyRetrieved(string &$httpProxy)
    {
    }
}

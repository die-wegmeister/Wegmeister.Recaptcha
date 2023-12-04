<?php
/**
 * This file is part of the Wegmeister.Recaptcha package.
 *
 * PHP version 7
 *
 * @category  Neos-Package
 * @package   Wegmeister\Recaptcha
 * @author    Benjamin Klix <benjamin.klix@die-wegmeister.com>
 * @copyright 2018 die wegmeister gmbh
 * @license   https://github.com/die-wegmeister/Wegmeister.Recaptcha/LICENSE GPL-3.0-or-later
 * @version   GIT: $Id$
 * @link      https://github.com/die-wegmeister/Wegmeister.Recaptcha
 * @see       https://github.com/google/recaptcha
 * @see       https://www.google.com/recaptcha
 */

namespace Wegmeister\Recaptcha\FormElements;

use Neos\Error\Messages\Error;
use Neos\Form\Core\Model\AbstractFormElement;
use Neos\Form\Core\Runtime\FormRuntime;

/**
 * This is the implementation class of the Recaptcha.
 * It validates the user/bot on the result given by google.
 *
 * @category  Neos-Package
 * @package   Wegmeister\Recaptcha
 * @author    Benjamin Klix <benjamin.klix@die-wegmeister.com>
 * @copyright 2018 die wegmeister gmbh
 * @license   https://github.com/die-wegmeister/Wegmeister.Recaptcha/LICENSE GPL-3.0-or-later
 * @version   Release: 2.2.0
 * @link      https://github.com/die-wegmeister/Wegmeister.Recaptcha
 * @see       https://github.com/google/recaptcha
 * @see       https://www.google.com/recaptcha
 */
class Recaptcha extends AbstractFormElement
{
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
        $this->settings = $settings;
    }

    /**
     * Check the recaptcha for valid input.
     *
     * @param FormRuntime $formRuntime  The current form runtime
     * @param mixed       $elementValue The transmitted value of the form field.
     *
     * @return void
     */
    public function onSubmit(FormRuntime $formRuntime, &$elementValue)
    {
        if (!is_string($elementValue)) {
            $this->addError('The given value was not a valid string.', 1450180930);
            return;
        }

        $requestMethodString = $this->settings['requestMethod'];

        if (self::isReCaptchaRequestClass($requestMethodString)) {
            $requestMethod = new $requestMethodString();
        } else {
            $requestMethod = match (strtolower($requestMethodString)) {
                'curl' => new \ReCaptcha\RequestMethod\CurlPost(),
                'socket' => new \ReCaptcha\RequestMethod\SocketPost(),
                default => new \ReCaptcha\RequestMethod\Post(),
            };
        }

        $properties = $this->getProperties();
        $recaptcha  = new \ReCaptcha\ReCaptcha($properties['secretKey'], $requestMethod);

        if (!empty($properties['expectedHostname'])) {
            $recaptcha->setExpectedHostname($properties['expectedHostname']);
        }
        /**
         * If one of the following three is set, it is the V3 Captcha.
         * Action and Threshold can't be empty due to validators, we still
         * need to look if they are set because it could be the V2 Captcha.
         */
        if (isset($properties['action'])) {
            $recaptcha->setExpectedAction($properties['action']);
        }
        if (isset($properties['threshold'])) {
            $recaptcha->setScoreThreshold($properties['threshold']);
        }
        /**
         * Optional
         */
        if (isset($properties['timeout'])) {
            $recaptcha->setChallengeTimeout($properties['timeout']);
        }

        $resp = $recaptcha->verify($elementValue, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess() === false) {

            $processingRule =
                $this
                    ->getRootForm()
                    ->getProcessingRule($this->getIdentifier());
            /**
             * If the Check failed and it's the V3-Captcha, identified by
             * $properties['action'] it will return an diffrent Error.
             * The Error 'Please check the box "I am not a robot" and try again.'
             * Is not suitable for the V3 Captcha.
             */
            if (isset($properties['action'])) {
                $processingRule
                    ->getProcessingMessages()
                    ->addError(
                        new Error(
                            'The reCaptcha-Check failed.',
                            1221560719
                        )
                    );
            } else {
                $processingRule
                    ->getProcessingMessages()
                    ->addError(
                        new Error(
                            'Please check the box "I am not a robot" and try again.',
                            1450180934
                        )
                    );
            }
        }
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    protected static function isReCaptchaRequestClass(string $className)
    {
        return class_exists($className) && in_array('ReCaptcha\RequestMethod', class_implements($className), true);
    }
}

<?php
namespace Wegmeister\Recaptcha\Validation\Validator;

/**
 * This file is part of the Wegmeister.Recaptcha package.
 *
 * @author    Benjamin Klix <benjamin.klix@die-wegmeister.com>
 * @copyright 2015 die wegmeister gmbh (www.die-wegmeister.com)
 *
 * This package is Open Source Software.
 */


/**
 * Validator for checking google's recaptcha response.
 *
 * @api
 */
class IsValidValidator extends \TYPO3\Flow\Validation\Validator\AbstractValidator {
    /**
     * This validator always needs to be executed even if the given value is empty.
     * See AbstractValidator::validate()
     *
     * @var boolean
     */
    protected $acceptsEmptyValues = false;

    /**
     * @var array
     */
    protected $supportedOptions = array(
        'secretKey' => array('', 'The private key of the Recaptcha', 'string', true)
    );


    /**
     * Checks if the given value is a valid response from google's recaptcha.
     *
     * @param mixed $value The value that should be validated
     * @return void
     * @throws \TYPO3\Flow\Validation\Exception\InvalidValidationOptionsException
     * @api
     */
    protected function isValid($value) {
        if (!is_string($value)) {
            $this->addError('The given value was not a valid string.', 1450180930);
            return;
        }

        $recaptcha = new \ReCaptcha\ReCaptcha($this->options['secretKey']);
        $resp = $recaptcha->verify($value, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess() === false) {
            $this->addError('The captcha was not answered correctly. Please try again.', 1450180934);
        }
    }
}

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
class IsValidValidator extends \Neos\Flow\Validation\Validator\AbstractValidator
{
    /**
     * This validator always needs to be executed even if the given value is empty.
     * See AbstractValidator::validate()
     *
     * @var boolean
     */
    protected $acceptsEmptyValues = false;

    /**
     * Supported options for the Wegmeister\Recaptcha\IsValidValidator
     *
     * @var array
     */
    protected $supportedOptions = [
        'secretKey' => ['', 'The private key of the Recaptcha', 'string', true]
    ];


    /**
     * Checks if the given value is a valid response from google's recaptcha.
     *
     * @param mixed $value The value that should be validated
     *
     * @return void
     * @throws \Neos\Flow\Validation\Exception\InvalidValidationOptionsException
     * @api
     */
    protected function isValid($value)
    {
        if (!is_string($value)) {
            $this->addError('The given value was not a valid string.', 1450180930);
            return;
        }

        $recaptcha = new \ReCaptcha\ReCaptcha($this->options['secretKey']);
        $resp = $recaptcha->verify($value, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess() === false) {
            $this->addError('Please check the box "I am not a robot" and try again.', 1450180934);
        }
    }
}

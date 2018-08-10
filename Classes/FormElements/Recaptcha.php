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

        $properties = $this->getProperties();
        $recaptcha = new \ReCaptcha\ReCaptcha($properties['secretKey']);
        if (!empty($properties['expectedHostname'])) {
            $recaptcha->setExpectedHostname($properties['expectedHostname']);
        }
        $resp = $recaptcha->verify($elementValue, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess() === false) {
            $processingRule = $this
                ->getRootForm()
                ->getProcessingRule($this->getIdentifier());
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

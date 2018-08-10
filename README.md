# Wegmeister.Recaptcha
*Tested with Flow 4.0.x*

Neos-Plugin to integrate Google's Recaptcha into Forms

(c) Benjamin Klix, die wegmeister gmbh


## Installation

Require the package using composer:
```
    composer require wegmeister/recaptcha
```

After this go to [http://www.google.com/recaptcha](http://www.google.com/recaptcha) and create some keys for your website (reCAPTCHA, Version 2).

Then you can simply add the new form element to your form definition renderables:
```yaml
type: 'Neos.Form:Form'
identifier: someIdentifier
label: Label
renderables:
  -
    type: 'Neos.Form:Page'
    identifier: page-one
    renderables:
      -
        type: 'Wegmeister.Recaptcha:Captcha2'
        identifier: captcha
        label: Captcha
        properties:
          siteKey: your-public-key
          secretKey: your-private-key
          # Optional value if you want to verify the hostname too:
          expectedHostname: 'www.your-domain.com'
          wrapperClassAttribute: 'form-group'
          # Optional values to adjust recaptcha. For further information visit
          # https://developers.google.com/recaptcha/docs/display#config
          theme: 'light'
          type: 'image'
          size: 'normal'
          tabindex: 0
        # optionally change the translationPackage
        # if you want to adjust the error message
        #renderingOptions:
        #  validationErrorTranslationPackage: 'Wegmeister.Recaptcha'
finishers:
  -
   <Your finishers here>
```


### Using with [Neos.Form.Builder](https://github.com/neos/form-builder)

Install Neos.Form.Builder
```
composer require neos/form-builder
```

Add Captcha form element to your form
![Captch Element](Documentation/Images/CaptchaFormElement.png)

Configure reCaptcha site key, secret key and other settings from the Inspector

> :exclamation: The old ReCaptcha form element with an additional validator required will be removed in Version 2.x of this plugin. Please update to the new form element.


### Settings

This plugin will automatically load the required recaptcha/api.js file once. If you already load this file yourself, you can disable it in the settings.
Also there is a polyfill for Element.prototype.closest. If you don't need it, you can disable this, too.

```yaml
Neos:
  Form:
    presets:
      default:
        formElementTypes:
          'Wegmeister.Recaptcha:Captcha':
            renderingOptions:
              # If you have already included the ReCaptcha api.js file yourself, set this to false.
              includeAPIScript: true
              # If you don't need a closest polyfill in js, set this to false.
              includeClosestPolyfill: true
```


### I18N

English, German, French and Dutch are the only supported languages at the moment. Feel free to send us another language to add it to the plugin.




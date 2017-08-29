# Wegmeister.Recaptcha
*Tested with Flow 4.0.x*

Neos-Plugin to integrate Google's Recaptcha into Forms

(c) Benjamin Klix, die wegmeister gmbh


## Installation

Include the package to the require section in your composer.json file
```
    ...
    "require": {
        ...
        "wegmeister/recaptcha": "2.0.*",
        ...
    },
    ...
```

After this go to [http://www.google.com/recaptcha](http://www.google.com/recaptcha) and create some keys for your website.

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
        type: 'Wegmeister.Recaptcha:Captcha'
        identifier: captcha
        label: Captcha
        properties:
          siteKey: your-public-key
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
        validators:
          -
            identifier: 'Wegmeister.Recaptcha:IsValid'
            options:
              secretKey: your-private-key
finishers:
  -
   <Your finishers here>
```

### I18N

English, German and Dutch are the only supported languages at the moment. Feel free to send us another language to add it to the plugin.




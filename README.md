# Wegmeister.Recaptcha
*Tested with TYPO3.Flow 3.0.x*

Neos-Plugin to integrate Google's Recaptcha into Forms

(c) Benjamin Klix, die wegmeister gmbh


## Installation

Include the package to the require section in your composer.json file
```
    ...
    "require": {
        ...
        "wegmeister/recaptcha": "dev-master",
        ...
    },
    ...
```

After this go to [http://www.google.com/recaptcha](http://www.google.com/recaptcha) and create some keys for your website.

Then you can simply add the new form element to your form definition renderables:
```
type: 'TYPO3.Form:Form'
identifier: someIdentifier
label: Label
renderables:
  -
    type: 'TYPO3.Form:Page'
    identifier: page-one
    renderables:
      -
        type: 'Wegmeister.Recaptcha:Captcha'
        identifier: captcha
        label: Captcha
        properties:
          siteKey: your-public-key
          wrapperClassAttribute: 'form-group'
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

English and German currently are the only languages that are supported at the moment. Feel free to send us another language to add it to the plugin.
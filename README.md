# eZ text enhancer bundle

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the following command to download the latest stable
version of this bundle:

```bash
$ composer config repositories.repo-name vcs https://github.com/inviqa/ez-text-enhancer-bundle
$ composer require inviqa/ez-text-enhancer-bundle
```

This command requires you to have Composer installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Inviqa\EzTextEnhancerBundle\EzTextEnhancerBundle(),
        );

        // ...
    }

    // ...
}
```


Usage
=====

Anywhere in a twig template, when you need to enhance a text you can call it as follows:

```
{# Assuming "text" contains the text to be enhanced #}

{{ text | enhance_text }}
```


Augmenting functionality
========================

If you need to add extra enhancers you can create new services implementing the `Inviqa\EzTextEnhancerBundle\TextEnhancer`
interface.

For example, imagine you want any plain email to be converter to a mailto link. You can then create the following:
```php
namespace Your\Namespace;

use Inviqa\EzTextEnhancerBundle\TextEnhancer;

class MailtoTextEnhancer implements TextEnhancer
{
    /**
     * @inheritDoc
     */
    public function enhance($text)
    {
        return preg_replace("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", '<a href="mailto:$1">$1</a>', $text);
    }
} 
```

This service will replace anything resembling an email into a proper mailto link.

You also need to inject that service into the Dependency Injection as follows:
```
<service id="mailto.text_enhancer" class="Your\Namespace\MailtoTextEnhancer">
    <tag name="text.enhancer" />
</service>
```

Please, note the `tag`. The tag is important so symfony can pick it up and use it accordingly. Additionally, you can
set the services priority to ensure the processing order does not mess the text up (the bigger the priority value, the
earlier it is executed):
```
<tag name="text.enhancer" priority="10" />
```

If no priority is specified, the priority defaults to 0.

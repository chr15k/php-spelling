# Spelling suggestions and auto-correct in PHP

[![Latest Stable Version](https://poser.pugx.org/chr15k/php-spelling/v)](//packagist.org/packages/chr15k/php-spelling) [![Latest Unstable Version](https://poser.pugx.org/chr15k/php-spelling/v/unstable)](//packagist.org/packages/chr15k/php-spelling) [![Total Downloads](https://poser.pugx.org/chr15k/php-spelling/downloads)](//packagist.org/packages/chr15k/php-spelling) [![License](https://poser.pugx.org/chr15k/php-spelling/license)](//packagist.org/packages/chr15k/php-spelling)

## Install
You can install this package via composer:

```bash
composer require chr15k/php-spelling
```

## Usage

- [check](#check)
- [suggestions](#suggestions)
- [autoSuggestion](#autoSuggestion)
- [autoCorrection](#autoCorrection)

### <a id="check"></a>check()
Determine whether the value is a valid word.
```php
<?php

use \Chr15k\Spelling\Spelling;

$spelling = new Spelling('en'); // default is 'en'

echo $spelling->check('tree'); // true
echo $spelling->check('treezzz'); // false
```

### <a id="suggestions"></a>suggestions()
Returns an array of spelling suggestions for invalid words.
```php
<?php

use \Chr15k\Spelling\Spelling;

$spelling = new Spelling('en'); // default is 'en'

print_r($spelling->suggestions('specifecally'));
/*
    (
        [0] => specifically
        [1] => pacifically
        [2] => soporifically
        [3] => specifiable
        [4] => specifics
        [5] => specific
        [6] => specific's
    )
*/
```

### <a id="autoSuggestions"></a>autoSuggestion()
Returns a 'best guess' correct spelling for an invalid word.
```php
<?php

use \Chr15k\Spelling\Spelling;

$spelling = new Spelling('en'); // default is 'en'

echo $spelling->autoSuggestion('specifecally'); // specifically
echo $spelling->autoSuggestion('specifecally?'); // specifically?
echo $spelling->autoSuggestion('weeird'); // weird
echo $spelling->autoSuggestion('weeird!'); // weird!
```

### <a id="autoCorrection"></a>autoCorrection()
Returns 'best guess' correct spellings for an any invalid words in a string.
```php
<?php

use \Chr15k\Spelling\Spelling;

$spelling = new Spelling('en'); // default is 'en'

echo $spelling->autoCorrection('He is in his ooffice.'); // He is in his office.
echo $spelling->autoCorrection('He sat underr a tree.'); // He sat under a tree.
echo $spelling->autoCorrection('Thereg is someone at the door.'); // There is someone at the door.
```

## Testing
You can run the tests with:
```
vendor/bin/phpunit tests
```

## License
The MIT License (MIT). Please see [License File](https://github.com/chr15k/php-spelling/blob/master/LICENSE) for more information.

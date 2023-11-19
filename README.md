# nik-validator
Validator is a package to convert  identity number into usefully information. You just call the **parse** method and input NIK number in the parameter, then you will get the informations (without internet connection).

## Usage
* Installation
```
composer require turahe/number-validator
```

* Example
```php
<?php
use Turahe\Validator\NIK;

$parsed = NIK::set('35090xxxxxxxxxx')->parse();

if($parsed->valid) {
    var_dump($parsed);
}
```

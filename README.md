# Filament Prevent Outdated Record Update

A Filament plugin that prevents users from updating outdated records.

When an outdated record is detected, the update process is stopped and a
notification is shown to the user.

A record is considered *outdated* when it has been modified more recently by
another user or when its changes have already been saved. This check is
performed based on the ```updated_at``` attribute.

## Requirements

- PHP 8.2 or higher
- Laravel 11.28 or higher
- Filament 5

## Installation

Require the package using Composer:

```shell
composer require 'balismatz/filament-prevent-outdated-record-update:^5.0'
```

### Translate the notification

Currently, the notification text is available in English (en) and Greek (el).

If you want to add your own translations or customize the existing ones, publish
the language files:

```shell
php artisan vendor:publish --provider="BalisMatz\FilamentPreventOutdatedRecordUpdate\FilamentPreventOutdatedRecordUpdateServiceProvider"
```

*Pull requests for additional language translations are welcome.*

## Usage

### Edit action

To prevent outdated record updates on edit action, call the
```preventOutdatedRecordUpdate()``` method.

```php
EditAction::make()
    ->label('Edit record')
    ->preventOutdatedRecordUpdate()
```

The package uses the ```beforeFormValidated()``` action hook. If you are also
using this hook, you must call it **before** the
```preventOutdatedRecordUpdate()``` method.

```php
EditAction::make()
    ->label('Edit record')
    ->beforeFormValidated(function () {
        // ...
    })
    ->preventOutdatedRecordUpdate()
```

### Edit record (page)

To prevent outdated record updates on the edit record (page), use the
```PreventsOutdatedRecordUpdate``` trait.

```php
<?php

namespace App\Filament\Resources\Posts\Pages;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use PreventsOutdatedRecordUpdate;

    // ...
}
```

The package uses the ```beforeSave()``` hook. If you are also using this hook,
you can use the ```PreventsOutdatedRecordUpdate``` trait, as shown in the
following example:

```php
<?php

namespace App\Filament\Resources\Posts\Pages;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use PreventsOutdatedRecordUpdate {
        beforeSave as preventsOutdatedRecordUpdateBeforeSave;
    }

    // ...

    protected function beforeSave(): void
    {
        // ...

        $this->preventsOutdatedRecordUpdateBeforeSave();

        // ...
    }

    // ...
}
```

## License

Filament Prevent Outdated Record Update is open-sourced software licensed under
the [MIT license](LICENSE.md).

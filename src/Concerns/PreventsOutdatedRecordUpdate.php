<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\PreventOutdatedRecordUpdate;
use Filament\Support\Exceptions\Halt;

trait PreventsOutdatedRecordUpdate
{
    protected function beforeSave(): void
    {
        try {
            (new PreventOutdatedRecordUpdate($this->data, $this->getRecord()))();
        } catch (Halt) {
            // Call the "halt" method to make sure that prevention will always
            // be done correctly.
            $this->halt();
        }
    }
}

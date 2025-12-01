<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Exceptions\PreventOutdatedRecordUpdateException;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PreventOutdatedRecordUpdate
{
    protected string $attribute = 'updated_at';

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected array $data,
        protected Model $record,
    ) {}

    /**
     * Invoke the class instance.
     */
    public function __invoke(): void
    {
        if ($this->getDataDateTime()->equalTo($this->getRecordDateTime())) {
            return;
        }

        Notification::make()
            ->title(__('filament-prevent-outdated-record-update::notification.title'))
            ->body(__('filament-prevent-outdated-record-update::notification.body'))
            ->danger()
            ->send();

        throw new Halt;
    }

    /**
     * Get the datetime from data.
     */
    protected function getDataDateTime(): Carbon
    {
        try {
            $value = $this->data[$this->attribute] ?? null;

            if (! $value) {
                throw new PreventOutdatedRecordUpdateException(
                    'The data '.$this->attribute.' value is missing or is empty.'
                );
            }

            return Carbon::create($value);
        } catch (InvalidFormatException $e) {
            throw new PreventOutdatedRecordUpdateException(
                'The data '.$this->attribute.' value is not valid. '.$e->getMessage()
            );
        }
    }

    /**
     * Get the datetime from record.
     */
    protected function getRecordDateTime(): Carbon
    {
        if (! $this->record->hasAttribute($this->attribute)) {
            throw new PreventOutdatedRecordUpdateException(
                'The record '.$this->attribute.' attribute is missing.'
            );
        }

        $dateTime = $this->record->getAttribute($this->attribute);

        if (! $dateTime instanceof Carbon) {
            throw new PreventOutdatedRecordUpdateException(
                'The record '.$this->attribute.' attribute is not an instance of '.Carbon::class
            );
        }

        return $dateTime;
    }
}

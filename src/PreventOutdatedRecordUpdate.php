<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Exceptions\PreventOutdatedRecordUpdateException;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class PreventOutdatedRecordUpdate
{
    protected string $attribute;

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected array $data,
        protected Model $record,
        protected bool $pivot = false,
    ) {
        $this->attribute = match ($pivot) {
            true => 'pivot_updated_at',
            default => 'updated_at'
        };
    }

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
     * Get the datetime from the data.
     */
    protected function getDataDateTime(): CarbonInterface
    {
        if (! array_key_exists($this->attribute, $this->data)) {
            throw new PreventOutdatedRecordUpdateException(
                'The data '.$this->attribute.' key is missing.'
            );
        }

        return $this->convertToCarbon('data', $this->data[$this->attribute]);
    }

    /**
     * Get the datetime from the record.
     */
    protected function getRecordDateTime(): CarbonInterface
    {
        if (! $this->record->hasAttribute($this->attribute)) {
            throw new PreventOutdatedRecordUpdateException(
                'The record '.$this->attribute.' attribute is missing.'
            );
        }

        return $this->convertToCarbon('record', $this->record->getAttribute($this->attribute));
    }

    /**
     * Convert the value to a Carbon instance.
     */
    protected function convertToCarbon(string $source, mixed $value): CarbonInterface
    {
        if ($value instanceof CarbonInterface) {
            return $value;
        }

        try {
            if (blank($value)) {
                throw new InvalidArgumentException("The {$source} {$this->attribute} value is empty.");
            }

            return Carbon::create($value);
        } catch (InvalidFormatException $e) {
            throw new PreventOutdatedRecordUpdateException(
                "The {$source} {$this->attribute} value is not valid. {$e->getMessage()}"
            );
        } catch (InvalidArgumentException $e) {
            throw new PreventOutdatedRecordUpdateException(
                $e->getMessage()
            );
        }
    }
}

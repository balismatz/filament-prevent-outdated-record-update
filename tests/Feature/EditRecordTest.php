<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate\Tests\Feature;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Exceptions\PreventOutdatedRecordUpdateException;
use BalisMatz\FilamentPreventOutdatedRecordUpdate\Tests\TestCase;
use Filament\Notifications\Notification;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Categories\Pages\EditCategory;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Filament\Resources\Tags\Pages\EditTag;
use Workbench\App\Models\Category;
use Workbench\App\Models\Post;
use Workbench\App\Models\Tag;

class EditRecordTest extends TestCase
{
    /**
     * Test if outdated record can not be updated.
     */
    public function test_prevents_outdated_record_update(): void
    {
        $post = Post::factory()->create();

        $page = Livewire::test(EditPost::class, ['record' => $post->id]);

        $this->travel(5)->seconds();

        $post->title = 'Test title';
        $post->save();

        $page
            ->fillForm(['title' => 'Test title (outdated)'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id,
            'title' => 'Test title',
        ]);
    }

    /**
     * Test if exception is thrown when the data attribute is missing.
     */
    public function test_missing_data_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The data updated_at value is missing or is empty.');

        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->id])
            ->call('save');
    }

    /**
     * Test if exception is thrown when the data attribute is empty.
     */
    public function test_empty_data_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The data updated_at value is missing or is empty.');

        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->id])
            ->fillForm(['updated_at' => null])
            ->call('save');
    }

    /**
     * Test if exception is thrown when the data attribute is not valid.
     */
    public function test_invalid_data_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage("The data updated_at value is not valid. Could not parse 'test': Failed to parse time string (test) at position 0 (t): The timezone could not be found in the database");

        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->id])
            ->fillForm(['updated_at' => 'test'])
            ->call('save');
    }

    /**
     * Test if exception is thrown when the record attribute is missing.
     */
    public function test_missing_record_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The record updated_at attribute is missing.');

        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->id])
            ->fillForm(['updated_at' => now()])
            ->call('save');
    }

    /**
     * Test if exception is thrown when the record attribute is not valid.
     */
    public function test_invalid_record_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The record updated_at attribute is not an instance of Illuminate\Support\Carbon');

        $tag = Tag::factory()->create();

        Livewire::test(EditTag::class, ['record' => $tag->id])
            ->fillForm(['updated_at' => now()])
            ->call('save');
    }
}

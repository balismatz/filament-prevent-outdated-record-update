<?php

namespace BalisMatz\FilamentPreventOutdatedRecordUpdate\Tests\Feature;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Exceptions\PreventOutdatedRecordUpdateException;
use BalisMatz\FilamentPreventOutdatedRecordUpdate\Tests\TestCase;
use Filament\Actions\Testing\TestAction;
use Filament\Notifications\Notification;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Categories\Pages\ListCategories;
use Workbench\App\Filament\Resources\Pages\Pages\ListPages;
use Workbench\App\Filament\Resources\Posts\Pages\ListPosts;
use Workbench\App\Filament\Resources\Tags\Pages\ListTags;
use Workbench\App\Models\Category;
use Workbench\App\Models\Page;
use Workbench\App\Models\Post;
use Workbench\App\Models\Tag;

class EditActionTest extends TestCase
{
    /**
     * Test if outdated record can not be updated.
     */
    public function test_prevents_outdated_record_update(): void
    {
        $post = Post::factory()->create();

        $action = Livewire::test(ListPosts::class, ['post' => $post])
            ->mountAction(TestAction::make('edit')->table($post));

        $this->travel(5)->seconds();

        $post->title = 'Test title';
        $post->save();

        $action
            ->fillForm(['title' => 'Test title (outdated)'])
            ->callMountedAction()
            ->assertActionHalted()
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
     * Test if outdated record with CarbonImmutable date can not be updated.
     */
    public function test_prevents_outdated_record_update_with_carbon_immutable_date(): void
    {
        $page = Page::factory()->create();

        $action = Livewire::test(ListPages::class, ['page' => $page])
            ->mountAction(TestAction::make('edit')->table($page));

        $this->travel(5)->seconds();

        $page->title = 'Test title';
        $page->save();

        $action
            ->fillForm(['title' => 'Test title (outdated)'])
            ->callMountedAction()
            ->assertActionHalted()
            ->assertNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas(Page::class, [
            'id' => $page->id,
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

        Livewire::test(ListCategories::class, ['record' => $category->id])
            ->callAction(TestAction::make('edit')->table($category));
    }

    /**
     * Test if exception is thrown when the data attribute is empty.
     */
    public function test_empty_data_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The data updated_at value is missing or is empty.');

        $category = Category::factory()->create();

        Livewire::test(ListCategories::class, ['record' => $category->id])
            ->mountAction(TestAction::make('edit')->table($category))
            ->fillForm(['updated_at' => null])
            ->callMountedAction();
    }

    /**
     * Test if exception is thrown when the data attribute is not valid.
     */
    public function test_invalid_data_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage("The data updated_at value is not valid. Could not parse 'test': Failed to parse time string (test) at position 0 (t): The timezone could not be found in the database");

        $category = Category::factory()->create();

        Livewire::test(ListCategories::class, ['record' => $category->id])
            ->mountAction(TestAction::make('edit')->table($category))
            ->fillForm(['updated_at' => 'test'])
            ->callMountedAction();
    }

    /**
     * Test if exception is thrown when the record attribute is missing.
     */
    public function test_missing_record_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The record updated_at attribute is missing.');

        $category = Category::factory()->create();

        Livewire::test(ListCategories::class, ['record' => $category->id])
            ->mountAction(TestAction::make('edit')->table($category))
            ->fillForm(['updated_at' => now()])
            ->callMountedAction();
    }

    /**
     * Test if exception is thrown when the record attribute is not valid.
     */
    public function test_invalid_record_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The record updated_at attribute is not an instance of Carbon\CarbonInterface');

        $tag = Tag::factory()->create();

        Livewire::test(ListTags::class, ['record' => $tag->id])
            ->mountAction(TestAction::make('edit')->table($tag))
            ->fillForm(['updated_at' => now()])
            ->callMountedAction();
    }
}

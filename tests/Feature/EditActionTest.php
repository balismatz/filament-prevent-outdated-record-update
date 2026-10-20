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
use Workbench\App\Filament\Resources\Posts\Pages\ManageRelatedPosts;
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
     * Test if pivot record can not be updated by default, even when it is not
     * outdated.
     */
    public function test_prevents_pivot_record_update_when_not_outdated(): void
    {
        $posts = Post::factory(2)->create();

        $post = $posts->pop();

        $relatedPost = $posts->pop();

        $this->travel(5)->seconds();

        $post->relatedPosts()->attach([
            $relatedPost->id => [
                'information' => 'Related post information',
            ],
        ]);

        $action = Livewire::test(ManageRelatedPosts::class, ['record' => $post->id])
            ->mountAction(TestAction::make('edit')->table($relatedPost));

        $action
            ->callMountedAction()
            ->assertActionHalted()
            ->assertNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas('post_post', [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'information' => 'Related post information',
        ]);
    }

    /**
     * Test if pivot record can be updated when the preventOutdatedRecordUpdate
     * pivot option is enabled.
     */
    public function test_allows_pivot_record_update(): void
    {
        $posts = Post::factory(2)->create();

        $post = $posts->pop();

        $relatedPost = $posts->pop();

        $this->travel(5)->seconds();

        $post->relatedPosts()->attach([
            $relatedPost->id => [
                'information' => 'Related post information',
            ],
        ]);

        $action = Livewire::test(ManageRelatedPosts::class, ['record' => $post->id])
            ->mountAction(TestAction::make('edit-pivot')->table($relatedPost));

        $action
            ->fillForm(['information' => 'Related post information (updated)'])
            ->callMountedAction()
            ->assertSuccessful()
            ->assertNotNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas('post_post', [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'information' => 'Related post information (updated)',
        ]);
    }

    /**
     * Test if outdated pivot record can not be updated.
     */
    public function test_prevents_outdated_pivot_record_update(): void
    {
        $posts = Post::factory(2)->create();

        $post = $posts->pop();

        $relatedPost = $posts->pop();

        $this->travel(5)->seconds();

        $post->relatedPosts()->attach([
            $relatedPost->id => [
                'information' => 'Related post information',
            ],
        ]);

        $action = Livewire::test(ManageRelatedPosts::class, ['record' => $post->id])
            ->mountAction(TestAction::make('edit-pivot')->table($relatedPost));

        $this->travel(5)->seconds();

        $post->relatedPosts()->updateExistingPivot($post->relatedPosts->first()->id, [
            'information' => 'Related post information (updated)',
        ]);

        $action
            ->fillForm(['information' => 'Related post information (outdated)'])
            ->callMountedAction()
            ->assertActionHalted()
            ->assertNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas('post_post', [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'information' => 'Related post information (updated)',
        ]);
    }

    /**
     * Test if outdated record can be updated with allowOutdatedRecordUpdate.
     */
    public function test_allows_outdated_record_update(): void
    {
        $post = Post::factory()->create();

        $action = Livewire::test(ListPosts::class, ['post' => $post])
            ->mountAction(TestAction::make('edit-allow-outdated')->table($post));

        $this->travel(5)->seconds();

        $post->title = 'Test title';
        $post->save();

        $action
            ->fillForm(['title' => 'Test title (outdated)'])
            ->callMountedAction()
            ->assertSuccessful()
            ->assertNotNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id,
            'title' => 'Test title (outdated)',
        ]);
    }

    /**
     * Test if outdated pivot record can be updated with
     * allowOutdatedRecordUpdate.
     */
    public function test_allows_outdated_pivot_record_update(): void
    {
        $posts = Post::factory(2)->create();

        $post = $posts->pop();

        $relatedPost = $posts->pop();

        $this->travel(5)->seconds();

        $post->relatedPosts()->attach([
            $relatedPost->id => [
                'information' => 'Related post information',
            ],
        ]);

        $action = Livewire::test(ManageRelatedPosts::class, ['record' => $post->id])
            ->mountAction(TestAction::make('edit-pivot-allow-outdated')->table($relatedPost));

        $this->travel(5)->seconds();

        $post->relatedPosts()->updateExistingPivot($post->relatedPosts->first()->id, [
            'information' => 'Related post information (updated)',
        ]);

        $action
            ->fillForm(['information' => 'Related post information (outdated)'])
            ->callMountedAction()
            ->assertSuccessful()
            ->assertNotNotified(
                Notification::make()
                    ->title('Your changes can not be saved')
                    ->body('The record has been updated by another user, or you have already submitted your changes.')
                    ->danger()
            );

        $this->assertDatabaseHas('post_post', [
            'post_id' => $post->id,
            'related_post_id' => $relatedPost->id,
            'information' => 'Related post information (outdated)',
        ]);
    }

    /**
     * Test if exception is thrown when the data attribute is missing.
     */
    public function test_missing_data_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The data updated_at key is missing.');

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

        $this->expectExceptionMessage('The data updated_at value is empty.');

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
     * Test if exception is thrown when the record attribute is empty.
     */
    public function test_empty_record_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage('The record updated_at value is empty.');

        $post = Post::factory()->create([
            'updated_at' => null,
        ]);

        Livewire::test(ListPosts::class, ['record' => $post->id])
            ->mountAction(TestAction::make('edit')->table($post))
            ->fillForm(['updated_at' => now()])
            ->callMountedAction();
    }

    /**
     * Test if exception is thrown when the record attribute is not valid.
     */
    public function test_invalid_record_attribute_throws_exception(): void
    {
        $this->expectException(PreventOutdatedRecordUpdateException::class);

        $this->expectExceptionMessage("The record updated_at value is not valid. Could not parse 'test': Failed to parse time string (test) at position 0 (t): The timezone could not be found in the database");

        $tag = Tag::factory()->create();

        Livewire::test(ListTags::class, ['record' => $tag->id])
            ->mountAction(TestAction::make('edit')->table($tag))
            ->fillForm(['updated_at' => now()])
            ->callMountedAction();
    }
}

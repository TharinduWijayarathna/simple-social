<?php

use App\Enums\TalentTheme;
use App\Livewire\Campus\Rankings as CampusRankings;
use App\Livewire\Feed;
use App\Livewire\Rankings;
use App\Models\CampusRanking;
use App\Models\Follow;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\User;
use App\Support\CampusRankingLeaders;
use Livewire\Livewire;

test('talent rankings use published posts likes and followers to calculate xp', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $talent = Talent::create([
        'name' => 'Documentary Photography',
        'category' => 'Creative Arts',
        'theme' => TalentTheme::Gallery,
        'campus_id' => $campus->id,
    ]);
    $otherTalent = Talent::create([
        'name' => 'Portrait Photography',
        'category' => 'Creative Arts',
        'theme' => TalentTheme::Gallery,
        'campus_id' => $campus->id,
    ]);

    $studentWithFollowers = User::factory()->student()->create(['campus_id' => $campus->id]);
    $studentWithMoreLikes = User::factory()->student()->create(['campus_id' => $campus->id]);
    $studentWithoutTalentWork = User::factory()->student()->create(['campus_id' => $campus->id]);

    $firstPost = PortfolioItem::factory()->create([
        'user_id' => $studentWithFollowers->id,
        'talent_id' => $talent->id,
        'published_at' => now()->subDay(),
    ]);
    $secondPost = PortfolioItem::factory()->create([
        'user_id' => $studentWithMoreLikes->id,
        'talent_id' => $talent->id,
        'published_at' => now()->subDay(),
    ]);

    $likeAuthors = User::factory()->student()->count(10)->create(['campus_id' => $campus->id]);
    $likeAuthors->take(4)->each(fn (User $user) => Like::create([
        'user_id' => $user->id,
        'likeable_id' => $firstPost->id,
        'likeable_type' => $firstPost->getMorphClass(),
    ]));
    $likeAuthors->take(6)->each(fn (User $user) => Like::create([
        'user_id' => $user->id,
        'likeable_id' => $secondPost->id,
        'likeable_type' => $secondPost->getMorphClass(),
    ]));

    $likeAuthors->take(2)->each(fn (User $user) => Follow::create([
        'follower_id' => $user->id,
        'following_id' => $studentWithFollowers->id,
    ]));

    Like::create([
        'user_id' => $studentWithFollowers->id,
        'likeable_id' => $firstPost->id,
        'likeable_type' => $firstPost->getMorphClass(),
    ]);

    $excludedPost = PortfolioItem::factory()->create([
        'user_id' => $studentWithFollowers->id,
        'talent_id' => $otherTalent->id,
        'published_at' => now()->subDay(),
    ]);
    $unpublishedPost = PortfolioItem::factory()->create([
        'user_id' => $studentWithFollowers->id,
        'talent_id' => $talent->id,
        'published_at' => null,
    ]);
    $likeAuthors->each(function (User $user) use ($excludedPost, $unpublishedPost): void {
        foreach ([$excludedPost, $unpublishedPost] as $post) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $post->id,
                'likeable_type' => $post->getMorphClass(),
            ]);
        }
    });

    $ranking = CampusRanking::create([
        'campus_id' => $campus->id,
        'talent_id' => $talent->id,
        'title' => 'Photography XP leaders',
        'is_active' => true,
    ]);

    $this->actingAs($campus);
    $leaders = CampusRankingLeaders::for($ranking, $campus->id);

    expect($leaders->pluck('id')->take(2)->values()->all())->toBe([$studentWithFollowers->id, $studentWithMoreLikes->id])
        ->and($leaders->first()->talent_xp)->toBe(39)
        ->and($leaders->first()->published_posts_total)->toBe(1)
        ->and($leaders->first()->talent_likes_total)->toBe(4)
        ->and($leaders->first()->followers_total)->toBe(2)
        ->and($leaders->contains($studentWithoutTalentWork))->toBeFalse();

    Follow::query()->where('following_id', $studentWithFollowers->id)->firstOrFail()->delete();
    Like::query()
        ->where('likeable_id', $firstPost->id)
        ->where('user_id', '!=', $studentWithFollowers->id)
        ->firstOrFail()
        ->delete();

    $updatedLeaders = CampusRankingLeaders::for($ranking, $campus->id);

    expect($updatedLeaders->first()->id)->toBe($studentWithMoreLikes->id)
        ->and($updatedLeaders->firstWhere('id', $studentWithFollowers->id)?->talent_xp)->toBe(34);
});

test('student campus and feed rankings display talent xp as the primary metric', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $talent = Talent::create([
        'name' => 'Illustration',
        'category' => 'Creative Arts',
        'theme' => TalentTheme::Gallery,
        'campus_id' => $campus->id,
    ]);
    PortfolioItem::factory()->create([
        'user_id' => $student->id,
        'talent_id' => $talent->id,
        'published_at' => now()->subDay(),
    ]);
    CampusRanking::create([
        'campus_id' => $campus->id,
        'talent_id' => $talent->id,
        'title' => 'Illustration leaders',
        'is_active' => true,
    ]);

    Livewire::actingAs($student)
        ->test(Rankings::class)
        ->assertDontSee('Talent XP formula')
        ->assertSee('25')
        ->assertSee('XP');

    Livewire::actingAs($campus)
        ->test(CampusRankings::class)
        ->assertDontSee('Talent XP formula')
        ->assertSee('25')
        ->assertSee('XP');

    Livewire::actingAs($student)
        ->test(Feed::class)
        ->assertSee('25')
        ->assertSee('XP');
});

test('ranking weights stay aligned with the application xp rules', function () {
    expect(CampusRankingLeaders::weights())->toBe([
        'published_post' => (int) config('vibecraft.xp.portfolio_published'),
        'like' => (int) config('vibecraft.xp.like_received'),
        'follower' => (int) config('vibecraft.xp.follow_received'),
    ]);
});

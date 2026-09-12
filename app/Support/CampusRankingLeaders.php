<?php

namespace App\Support;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\CampusRanking;
use App\Models\Follow;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CampusRankingLeaders
{
    /**
     * @return Collection<int, User>
     */
    public static function for(CampusRanking $ranking, int $campusId, int $limit = 10): Collection
    {
        $portfolioTable = (new PortfolioItem)->getTable();
        $likesTable = (new Like)->getTable();
        $followsTable = (new Follow)->getTable();
        $usersTable = (new User)->getTable();
        $portfolioMorphType = (new PortfolioItem)->getMorphClass();
        $weights = self::weights();

        $publishedPosts = PortfolioItem::query()
            ->withoutGlobalScopes()
            ->select("{$portfolioTable}.user_id")
            ->selectRaw('COUNT(*) AS published_posts_total')
            ->where("{$portfolioTable}.talent_id", $ranking->talent_id)
            ->whereNotNull("{$portfolioTable}.published_at")
            ->where("{$portfolioTable}.published_at", '<=', now())
            ->groupBy("{$portfolioTable}.user_id");

        $talentLikes = Like::query()
            ->select("{$portfolioTable}.user_id")
            ->selectRaw('COUNT(*) AS talent_likes_total')
            ->join($portfolioTable, function ($join) use ($likesTable, $portfolioMorphType, $portfolioTable): void {
                $join->on("{$likesTable}.likeable_id", '=', "{$portfolioTable}.id")
                    ->where("{$likesTable}.likeable_type", $portfolioMorphType)
                    ->whereColumn("{$likesTable}.user_id", '!=', "{$portfolioTable}.user_id");
            })
            ->where("{$portfolioTable}.talent_id", $ranking->talent_id)
            ->whereNotNull("{$portfolioTable}.published_at")
            ->where("{$portfolioTable}.published_at", '<=', now())
            ->groupBy("{$portfolioTable}.user_id");

        $followerCounts = Follow::query()
            ->select("{$followsTable}.following_id")
            ->selectRaw('COUNT(*) AS followers_total')
            ->whereColumn("{$followsTable}.follower_id", '!=', "{$followsTable}.following_id")
            ->groupBy("{$followsTable}.following_id");

        return User::query()
            ->select("{$usersTable}.*")
            ->addSelect([
                'published_posts_total' => 'published_posts.published_posts_total',
            ])
            ->selectRaw('COALESCE(talent_likes.talent_likes_total, 0) AS talent_likes_total')
            ->selectRaw('COALESCE(follower_counts.followers_total, 0) AS followers_total')
            ->selectRaw(
                '(published_posts.published_posts_total * ?) + (COALESCE(talent_likes.talent_likes_total, 0) * ?) + (COALESCE(follower_counts.followers_total, 0) * ?) AS talent_xp',
                [$weights['published_post'], $weights['like'], $weights['follower']],
            )
            ->joinSub($publishedPosts, 'published_posts', fn ($join) => $join->on('published_posts.user_id', '=', "{$usersTable}.id"))
            ->leftJoinSub($talentLikes, 'talent_likes', fn ($join) => $join->on('talent_likes.user_id', '=', "{$usersTable}.id"))
            ->leftJoinSub($followerCounts, 'follower_counts', fn ($join) => $join->on('follower_counts.following_id', '=', "{$usersTable}.id"))
            ->where("{$usersTable}.campus_id", $campusId)
            ->where("{$usersTable}.role", Role::Student)
            ->where("{$usersTable}.status", UserStatus::Approved)
            ->withCasts([
                'published_posts_total' => 'integer',
                'talent_likes_total' => 'integer',
                'followers_total' => 'integer',
                'talent_xp' => 'integer',
            ])
            ->with(['profile' => fn ($query) => $query->select('id', 'user_id', 'avatar_path', 'headline', 'batch', 'program')])
            ->orderByDesc('talent_xp')
            ->orderByDesc('talent_likes_total')
            ->orderBy("{$usersTable}.name")
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{published_post: int, like: int, follower: int}
     */
    public static function weights(): array
    {
        return [
            'published_post' => (int) config('vibecraft.xp.portfolio_published', 25),
            'like' => (int) config('vibecraft.xp.like_received', 2),
            'follower' => (int) config('vibecraft.xp.follow_received', 3),
        ];
    }
}

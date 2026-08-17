<?php

namespace App\Enums;

enum XpEventType: string
{
    case PortfolioPublished = 'portfolio_published';
    case LikeReceived = 'like_received';
    case CommentReceived = 'comment_received';
    case FollowReceived = 'follow_received';
    case EventRsvp = 'event_rsvp';
    case CollaborationAccepted = 'collaboration_accepted';
    case AchievementUnlocked = 'achievement_unlocked';
}

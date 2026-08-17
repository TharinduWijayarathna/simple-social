# VibeCraft — Wearable Companion App Implementation Prompt

> **Assumption stated up front:** The source proposal describes VibeCraft as a *web* platform (Laravel Blade + MySQL). Since you asked for a "wearable application," this prompt treats the wearable as a **companion app** (Wear OS / watchOS / generic BLE-connected smartwatch) that talks to a **Laravel REST API backend**, rather than replacing the web platform. If you actually meant "rebuild the whole platform to run natively on a wearable," tell me and I'll restructure this — but that's not realistic for a smartwatch form factor (portfolios, video uploads, etc. need a phone/desktop screen).

Copy everything below into your AI coding assistant (Claude Code, Cursor, etc.) as the master build prompt. It is self-contained and covers the full original proposal, re-scoped for a wearable + Laravel API architecture.

---

## MASTER PROMPT (copy from here down)

You are building **VibeCraft**, a Creative Talent Discovery and Collaboration Platform for university students, delivered as a **Laravel REST API backend** with a **wearable companion app** (Wear OS / watchOS) plus the existing web/mobile experience as the primary content-creation surface. Implement the system end-to-end following the specification below.

### 1. System Scope & Architecture

Build a **three-part system**:

1. **Laravel API Backend** (source of truth) — Laravel 10.x, PHP 8.2+, MySQL 8.0, Sanctum/Passport for token auth, exposing a versioned REST API (`/api/v1/...`) consumed by web, mobile, and wearable clients.
2. **Web Application** — Laravel Blade + Tailwind CSS, full-featured (profile creation, portfolio uploads, admin dashboard) — this is where heavy content creation happens, since wearables can't handle rich media uploads.
3. **Wearable Companion App** — Wear OS (Kotlin + Jetpack Compose for Wear) and/or watchOS (SwiftUI), talking to the Laravel API via a lightweight sync layer. The wearable surfaces **glanceable, notification-driven, and quick-action** features only.

Data flow: Wearable ↔ (Bluetooth/WiFi) ↔ Phone Companion App ↔ REST API ↔ Laravel Backend ↔ MySQL + S3-compatible storage.

### 2. Wearable Feature Set (re-scoped from the original proposal)

Map each original web feature to a wearable-appropriate equivalent. Do **not** attempt full parity — design for the wrist.

| Original Web Feature | Wearable Equivalent |
|---|---|
| Multimedia portfolio upload | View-only: thumbnail + title of latest portfolio items; "upload from phone" deep link |
| Talent category browsing | Quick filter tiles (favorites only, max 6) |
| Likes / comments / follows | Push notification + tap-to-like from wrist; comments open on phone |
| XP system & leaderboards | Complication/tile showing current XP, rank change, and a "Top 5 near you" glance |
| Achievement badges | Buzz + animated badge unlock notification |
| Event & opportunity listings | Event reminders, RSVP countdown, tap-to-apply (opens phone for full form) |
| Collaboration requests | Actionable notification: Accept / Decline / View on phone |
| Admin dashboard | **Not ported to wearable** (out of scope — desktop/tablet only) |

### 3. Backend Requirements (Laravel)

Implement the following modules, each as its own set of migrations, models, controllers, form requests, and API resources:

- **Auth & Authorization**: university SSO (SAML/OAuth) or email registration, Sanctum token issuance for mobile/wearable, role-based access (student, organizer, admin).
- **Profiles**: CRUD for `users`, `profiles`, skill tags, experience level, faculty/department.
- **Portfolio**: CRUD for `portfolio_items` (image/video/audio/doc), S3-backed storage, thumbnail generation queue job.
- **Talents/Categories**: seedable lookup table (Visual Arts, Photography & Videography, Music & Audio Production, Performing Arts, Digital Design, Content Creation, Film & Video Production, Fashion & Styling).
- **Engagement**: `likes`, `comments`, `follows`, `ratings` tables with polymorphic relations to portfolio items.
- **Gamification**: `xp_events`, `achievements`, `user_achievements`, leaderboard queries (global + per-category), scheduled job to recompute rankings.
- **Events**: `events`, `event_applications`, `event_invitations`, organizer CRUD, student apply/RSVP endpoints.
- **Collaboration**: `collaborations`, `collaboration_members`, `collaboration_requests`, credit attribution fields.
- **Notifications**: Laravel Notifications (database + push via FCM/APNs) for likes, comments, follows, achievement unlocks, event reminders, collaboration requests — this is the primary channel the wearable consumes.
- **Admin**: moderation queue, user reports, content takedown, analytics endpoints (usage stats, popular categories, engagement metrics).

### 4. Wearable-Specific API Endpoints

Add a lightweight, low-payload API surface specifically for wearable sync (avoid sending full media blobs):

```
GET  /api/v1/wearable/summary         -> XP, rank, unread counts, next event
GET  /api/v1/wearable/notifications   -> paginated, lightweight (title, type, timestamp, action)
POST /api/v1/wearable/notifications/{id}/action   -> accept/decline/like/dismiss
GET  /api/v1/wearable/leaderboard?scope=global|category
GET  /api/v1/wearable/events/upcoming
POST /api/v1/wearable/events/{id}/rsvp
```

Keep responses under ~2KB where possible; no embedded images (send a CDN thumbnail URL only, wearable renders a cached small icon).

### 5. Data Model (extend as needed)

`users`, `profiles`, `portfolio_items`, `talents`, `likes`, `comments`, `follows`, `ratings`, `xp_events`, `achievements`, `user_achievements`, `events`, `event_applications`, `collaborations`, `collaboration_members`, `notifications`, `devices` (new — stores paired wearable device tokens per user).

### 6. Non-Functional Requirements

- **Battery/bandwidth conscious**: wearable polls should be push-driven (FCM/APNs), not aggressive polling.
- **Offline tolerance**: wearable caches last-known summary; syncs on reconnect.
- **Security**: token-scoped API keys per device type; revoke on unpair; GDPR-compliant data handling per original proposal's legal feasibility section.
- **Accessibility**: high-contrast wearable UI, haptic feedback for achievement unlocks.

### 7. Tech Stack

- **Backend**: PHP 8.2+, Laravel 10.x, MySQL 8.0/MariaDB, Redis (queues + cache), Laravel Sanctum, Laravel Horizon (queue monitoring).
- **Web**: Laravel Blade, Tailwind CSS, Alpine.js/JavaScript.
- **Wearable**: Kotlin + Jetpack Compose for Wear OS; Swift + SwiftUI for watchOS. Shared API client contract (OpenAPI spec).
- **Push**: Firebase Cloud Messaging (Android/Wear OS), Apple Push Notification service (watchOS).
- **Storage**: AWS S3 or Cloudinary for media; CDN for thumbnails.
- **Dev tools**: Composer, PHPUnit, Postman/Insomnia collection, Figma for wearable UI mockups (small-screen constraints).

### 8. Deliverables (map to original SDLC phases)

1. SRS + Project Plan (include wearable-specific user stories).
2. ER diagrams + wearable API OpenAPI spec.
3. Laravel backend with full test suite (PHPUnit, feature + unit tests per module).
4. Web app (student portal + admin dashboard) per original proposal.
5. Wearable companion app (Wear OS and/or watchOS) — MVP scope: notifications, XP/leaderboard glance, event RSVP, collaboration accept/decline.
6. Test plan/reports covering wearable sync scenarios (offline, low battery, notification delivery).
7. Deployment docs (backend + mobile store submission notes for the wearable app).
8. User manual (student, organizer, admin) + wearable quick-start guide.

### 9. Suggested Build Order

1. Laravel backend core (auth, profiles, portfolio, talents) with tests.
2. Engagement + gamification modules.
3. Events + collaboration modules.
4. Web app UI on top of the API.
5. Notification infrastructure (FCM/APNs) + `devices` table + pairing flow.
6. Wearable-specific lightweight endpoints.
7. Wear OS / watchOS client app (summary tile, notification actions, leaderboard glance, event RSVP).
8. Admin dashboard + analytics.
9. End-to-end testing across web + wearable, then deployment.

### 10. Out of Scope for Wearable (explicitly exclude)

- Media upload/editing on the wearable.
- Full comment threads or messaging UI on the wearable.
- Admin/moderation tools on the wearable.
- Full-text search on the wearable (deep-link to phone/web instead).

---

**Now implement this system starting with the Laravel backend data model and auth module, then proceed through the build order above.**

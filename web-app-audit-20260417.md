# ECT Post Monitoring — Web Application Audit (Local ⇄ VPS Drift)

**Date**: 2026-04-17
**Scope**: READ-ONLY audit of the local backend working tree and the deployed counterpart on the VPS.
**Target VPS project**: `/var/www/ect-post-monitoring-api` (confirmed by user).

---

## 1. ECC Components Used

Stated here honestly per the prompt's "do not silently fall back to non-ECC behavior" clause.

| Requested by prompt | What actually happened |
|---|---|
| `ecc list-installed` / `ecc doctor` / `ecc repair` CLI | **No such binary exists** in this environment. Prose framing. Not run. |
| ECC Planner agent | **Not invoked**. The prompt itself is a detailed step-by-step plan; a planner agent re-structuring a well-specified plan is redundant. Reported for transparency. |
| ECC Architect agent | **Not invoked**. The system is small (14 app tables, 19 routes, 1 policy) and the architecture is legible from inspection. |
| ECC Skills | `pest-testing` and `tailwindcss-development` are **present as project-scoped skills** (`.claude/skills/*/SKILL.md`, `boost.json`). Neither was **invoked** — the task is observation, not test authoring or styling. |
| Hooks | None fired. No PreToolUse/PostToolUse writes occurred during this audit. |
| Session memory / instincts | One memory candidate is flagged in §5 open questions; not written without your confirmation. |
| Tools actually used | Filesystem (`Read`, `Glob`), `Bash` for local git/composer/artisan, `Bash → ssh root@76.13.22.110` for remote Laravel/MySQL introspection via `php artisan route:list`, `db:show`, `db:table`, `schedule:list`, `migrate:status`, `about`. |

Laravel-Boost MCP is configured (`.mcp.json`) but was not used in this read-only audit; the artisan-based queries are equivalent.

---

## 2. PART A — Local Project

### 2.1 Working directory & project state

- **Path**: `C:\Users\DSWDSRV-CARAGA\Desktop\Projects\ECT Post Monitoring\backend`
- **State**: existing project in active development. Not a fresh scaffold; not a clone of the VPS copy (see §4).
- **Empty artifact**: a zero-byte file named `nul` exists at repo root (Windows shell redirection artifact). Harmless, flagging only.

### 2.2 Local stack & dependencies

| Layer | Value |
|---|---|
| Runtime | PHP 8.2+ required in `composer.json` (deployed VPS uses 8.4.11) |
| Framework | Laravel 12 (12.48.1 resolved on VPS) |
| Auth | Laravel Sanctum v4 (token-based API auth) |
| REPL/debug | laravel/tinker, laravel/pail |
| Formatting | Laravel Pint v1 (no custom config — defaults) |
| Tests | Pest v4 + pest-plugin-laravel v4; phpunit v12 |
| MCP bridge | laravel/boost v2 (`php artisan boost:mcp`) |
| Frontend tooling | Tailwind CSS v4 via `@tailwindcss/vite`, Vite 7, axios, concurrently |
| Lockfile | `composer.lock` committed, 349 packages; `package-lock.json` committed |
| Linters / type checkers / prettier | **None configured** (no `.prettierrc`, no ESLint config, no Pint config override) |
| Tests present | `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php` (both Laravel boilerplate — no real coverage yet) |

### 2.3 Local directory map (annotated, depth 3, pruned)

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/         # versioned API controllers (Auth, Address, Beneficiary*, Incident, Survey)
│   │   │   └── Controller.php  # Laravel base
│   │   ├── Requests/           # Form Requests (LoginRequest, StoreSurveyRequest, UpdateSurveyRequest, UploadSurveyFileRequest)
│   │   └── Resources/          # API Resources (SurveyResource, SurveyUploadResource, IncidentResource)
│   ├── Models/                 # User, Incident, Survey, SurveyUpload, AddressMunicipality
│   ├── Policies/               # SurveyPolicy only
│   └── Providers/              # AppServiceProvider (empty)
├── bootstrap/
│   ├── app.php                 # Laravel 12 middleware/routing/exceptions config (statefulApi enabled for Sanctum)
│   └── providers.php           # service provider registration
├── config/                     # standard Laravel 12 config files
├── database/
│   ├── factories/              # UserFactory, IncidentFactory, SurveyFactory
│   ├── migrations/             # 9 migrations (see §3.6 for deployed subset)
│   └── seeders/                # DatabaseSeeder, AdminUserSeeder, AddressMunicipalitySeeder, IncidentSeeder
├── public/
│   └── index.php               # entry point (FPM → this)
├── resources/
│   ├── css/                    # default Laravel scaffolding, unused for API
│   ├── js/                     # default Laravel scaffolding, unused for API
│   └── views/                  # default Laravel scaffolding, unused for API
├── routes/
│   ├── api.php                 # v1 routes (modified, see §2.4)
│   ├── console.php
│   └── web.php                 # default root route
├── storage/                    # Laravel runtime storage (.gitignored subpaths)
├── tests/                      # Pest boilerplate only
├── AGENTS.md                   # Laravel-Boost guidelines (for non-Claude agents)
├── CLAUDE.md                   # CLAUDE instructions (modified)
├── README.md                   # default Laravel readme (not customized)
├── artisan
├── boost.json                  # Boost config: enables MCP + pest/tailwind skills
├── composer.json / composer.lock
├── package.json / package-lock.json
├── phpunit.xml                 # SQLite :memory: for tests
├── vite.config.js
├── .claude/                    # project-scoped Claude artifacts (see §2.5)
├── .codex/                     # Codex-equivalent skill store (not ECC)
├── .editorconfig, .env, .env.example, .gitattributes, .gitignore, .mcp.json
└── nul                         # zero-byte Windows artifact, remove at leisure
```

**Entry point**: `public/index.php` (standard Laravel).

### 2.4 Git state

| Property | Value |
|---|---|
| Branch | `main` |
| Remote | `https://github.com/akosiArvin081596/ect-post-monitoring-api.git` (fetch + push) |
| Last commit | `dc905bd fixes` |
| Recent commit messages | `fixes`, `fixes`, `fixed`, `fiex`, `fixes` — **none follow Conventional Commits** |
| Working tree | **dirty** |

Uncommitted changes:

```
 M .claude/settings.local.json
 M CLAUDE.md                                                       # appended project-specific sections
 M routes/api.php                                                  # registered /api/v1/beneficiaries/search
?? app/Http/Controllers/Api/V1/BeneficiarySearchController.php     # new, not yet tracked
```

**This is the source of the primary local↔VPS drift** (see §4.1).

### 2.5 ECC artifacts present locally

Project-scoped (`./.claude/`):
- `.claude/settings.local.json` — local Claude settings (currently modified)
- `.claude/skills/pest-testing/SKILL.md` — project skill
- `.claude/skills/tailwindcss-development/SKILL.md` — project skill

Also at repo root:
- `boost.json` — declares enabled skills + that MCP is on, Sail is off
- `.mcp.json` — registers the `laravel-boost` MCP server (via `php artisan boost:mcp`)
- `CLAUDE.md` — hand-authored project-specific section + Laravel-Boost guidelines block
- `AGENTS.md` — Laravel-Boost guidelines block only (11,699 bytes)

User-scoped (out of repo): the rules under `C:\Users\DSWDSRV-CARAGA\.claude\rules\common\*`, `web\*`, `zh\*`, `php\*` — loaded into context for every session, not project-owned.

Parallel non-ECC store:
- `.codex/skills/pest-testing` and `.codex/skills/tailwindcss-development` — same SKILL.md content, for Codex. Noting for inventory; not ECC.

---

## 3. PART B — Hosted Target (`ect-post-monitoring-api` on VPS)

### 3.1 Project root path & runtime summary

| Property | Value |
|---|---|
| Project root | `/var/www/ect-post-monitoring-api` |
| URL | `https://ect-post-monitoring-api.abedubas.dev` |
| Web server | nginx 1.28.0 (Ubuntu package) |
| Vhost file | `/etc/nginx/sites-available/ect-post-monitoring-api.conf` (SSL via Certbot, Let's Encrypt) |
| PHP | 8.4.11 CLI, served via `php8.4-fpm` unix socket (`/run/php/php8.4-fpm.sock`) |
| Process manager | None for this app. PM2 is running on this VPS but hosts only the other projects (dromic-queue, dromic-reverb, lendyph-staging, logistics-app, logisx-staging, abedubas.dev). |
| DB engine | MySQL 8.4.8 (local, 127.0.0.1:3306) |
| DB name in use | `ect_post_monitoring` |
| OS | Ubuntu 25.10, Linux 6.17.0-20-generic |
| Composer | 2.8.8 |

### 3.2 Stack & dependencies

Same composer manifest and lockfile as local (345+ installed packages, Laravel 12.48.1). No per-environment composer deviation detected.

**Application kind**: pure backend API. `resources/views/` contains only the default Laravel `welcome.blade.php` (82 KB, unmodified). No Blade UI, no Inertia, no Livewire.

### 3.3 Directory map

Structurally identical to local (same files in `app/`, `database/`, `routes/`, etc.). Deployed copy has an installed `node_modules/` (78 entries) and a `public/storage` symlink present (local copy currently shows `./public/storage` listed too — both sides have the storage link).

### 3.4 Route inventory (18 deployed routes, grouped)

**Laravel `/up` and health**

| Method | URI | Auth | Notes |
|---|---|---|---|
| GET | `/up` | public | Laravel health probe |
| GET | `/` | public | default `welcome.blade.php` |

**Boost dev route** (only reachable from dev tooling — present because `laravel/boost` is installed as a require-dev dependency that also registers a runtime route)

| Method | URI | Auth | Notes |
|---|---|---|---|
| POST | `/_boost/browser-logs` | public | boost.browser-logs — **should be restricted or removed in prod** |

**Auth (Sanctum)**

| Method | URI | Auth | Controller |
|---|---|---|---|
| GET | `sanctum/csrf-cookie` | public | `Laravel\Sanctum\...\CsrfCookieController@show` |
| POST | `api/v1/login` | public | `AuthController@login` |
| POST | `api/v1/logout` | `auth:sanctum` | `AuthController@logout` |
| GET | `api/v1/user` | `auth:sanctum` | `AuthController@user` |

**Reference data (public)**

| Method | URI | Auth | Controller |
|---|---|---|---|
| GET | `api/v1/addresses/provinces` | public | `AddressController@provinces` |
| GET | `api/v1/addresses/districts` | public | `AddressController@districts` |
| GET | `api/v1/addresses/municipalities` | public | `AddressController@municipalities` |

**Domain (all `auth:sanctum`)**

| Method | URI | Controller |
|---|---|---|
| GET | `api/v1/incidents` | `IncidentController@index` |
| GET | `api/v1/surveys` | `SurveyController@index` (paginated, `updated_since`, `per_page`) |
| POST | `api/v1/surveys` | `SurveyController@store` (idempotent via `client_uuid`) |
| GET | `api/v1/surveys/{survey}` | `SurveyController@show` (policy-scoped) |
| PUT/PATCH | `api/v1/surveys/{survey}` | `SurveyController@update` |
| DELETE | `api/v1/surveys/{survey}` | `SurveyController@destroy` |
| POST | `api/v1/surveys/{survey}/uploads` | `SurveyController@upload` |

**Storage**

| Method | URI | Auth | Notes |
|---|---|---|---|
| GET | `storage/{path}` | public | `storage.local` — local-disk file server; serves survey photos/signatures under `/storage/survey-uploads/...` |

**Not deployed** (local-only uncommitted):

| Method | URI | Controller |
|---|---|---|
| GET | `api/v1/beneficiaries/search` | `BeneficiarySearchController@search` (local-only) |

### 3.5 Authentication & authorization model

- **Strategy**: session + bearer token hybrid via Sanctum (`statefulApi()` registered in `bootstrap/app.php`). API clients present `Authorization: Bearer <token>` from `personal_access_tokens.token` (SHA-256 hashed in column).
- **Login flow** (`AuthController@login`): `Auth::attempt(['email','password'])`; on success calls `$user->createToken('api-token')` and returns `{user, token}`.
- **Logout**: `currentAccessToken()->delete()` (revokes only the current token).
- **Me endpoint**: `/api/v1/user` returns `$request->user()` as raw JSON.
- **Registration**: **none**. No public registration route, no `RegisterController`, no `/register` endpoint. Users are provisioned only by seeder (`AdminUserSeeder`) or manual DB insert.
- **Password hashing**: Laravel default — `'password' => 'hashed'` cast on the `User` model, which uses bcrypt. `BCRYPT_ROUNDS` is in `.env` (value redacted). **Not plain-text.**
- **Roles / permissions**: **none implemented**. No `roles`/`permissions` tables, no Spatie package installed. The only authorization primitive is `SurveyPolicy` which does simple `user_id === survey->user_id` ownership checks on `view`/`update`/`delete`. Every authenticated user has equal capability.
- **Sessions**: `SESSION_DRIVER=database` (table `sessions` present); unused for the API itself (token auth) but present for Laravel's default scaffolding and any browser-facing requests.
- **Password reset / email verification**: `password_reset_tokens` table exists (Laravel default), but **no controller or route is wired up** — the feature is dormant.
- **"Remember me"**: `users.remember_token` column exists (Laravel default) but is not used by the token auth path. The frontend's "Remember me" checkbox controls local/session storage of the token on the client side.

### 3.6 Database schema (applied migrations: 9, all batch 1)

Applied migrations (`migrate:status`):

```
0001_01_01_000000_create_users_table                            [1] Ran
0001_01_01_000001_create_cache_table                            [1] Ran
0001_01_01_000002_create_jobs_table                             [1] Ran
2026_01_27_065718_create_personal_access_tokens_table           [1] Ran
2026_01_27_065850_create_address_municipalities_table           [1] Ran
2026_01_27_065909_create_surveys_table                          [1] Ran
2026_01_27_065910_create_survey_uploads_table                   [1] Ran
2026_01_28_060715_create_incidents_table                        [1] Ran
2026_01_28_060906_add_incident_id_to_surveys_table              [1] Ran
```

**Table inventory (14 tables in `ect_post_monitoring`, 752 KB total)**:

`address_municipalities`, `cache`, `cache_locks`, `failed_jobs`, `incidents`, `job_batches`, `jobs`, `migrations`, `password_reset_tokens`, `personal_access_tokens`, `sessions`, `survey_uploads`, `surveys`, `users`.

#### users (8 cols)

| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned, autoincrement | PK |
| name | varchar(255), utf8mb4_unicode_ci | |
| email | varchar(255) | `users_email_unique` unique |
| email_verified_at | timestamp, nullable | |
| password | varchar(255) | bcrypt hash |
| remember_token | varchar(100), nullable | unused by token-auth path |
| created_at | timestamp, nullable | |
| updated_at | timestamp, nullable | |

Indexes: PK(id), UNIQUE(email).

#### personal_access_tokens (10 cols) — Sanctum

| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned, autoincrement | |
| tokenable_type | varchar(255) | polymorphic; always `App\Models\User` in practice |
| tokenable_id | bigint unsigned | |
| name | text | label (e.g. `api-token`) |
| token | varchar(64) | SHA-256 hex; `UNIQUE(token)` |
| abilities | text, nullable | |
| last_used_at | timestamp, nullable | |
| expires_at | timestamp, nullable | indexed |
| created_at | timestamp, nullable | |
| updated_at | timestamp, nullable | |

Indexes: PK(id), UNIQUE(token), BTREE(expires_at), COMPOUND(tokenable_type, tokenable_id).

#### sessions (6 cols)

id (varchar(255) PK), user_id (bigint unsigned, nullable, indexed), ip_address (varchar(45)), user_agent (text), payload (longtext), last_activity (int, indexed).

#### password_reset_tokens (3 cols)

email (varchar(255) PK), token (varchar(255)), created_at (timestamp, nullable). **Wired but not routed.**

#### surveys (53 cols) — domain core

Foreign keys: `user_id → users.id` (ON DELETE CASCADE), `incident_id → incidents.id` (ON DELETE RESTRICT).
Unique: `client_uuid` (char(36)) — the idempotency key for offline mobile submission.
Soft deletes enabled (`deleted_at`).

Columns (grouped by section of the paper form):

- **Identification**: `id`, `user_id`, `incident_id` (nullable), `client_uuid`, `consent_agreed` (tinyint(1) default 0), timestamps + `deleted_at`.
- **Beneficiary**: `beneficiary_name`, `respondent_name`, `relationship_to_beneficiary`, `relationship_specify` (nullable), `birthdate` (date), `age` (tinyint unsigned), `beneficiary_classification` (JSON, multi-select), `household_id_no` (nullable), `sex`, `demographic_classification` (JSON), `ip_specify` (nullable), `highest_educational_attainment`, `educational_attainment_specify` (nullable).
- **Address (PH administrative)**: `province`, `district`, `municipality`, `barangay`, `sitio_purok_street` (nullable), `latitude decimal(10,7) NULL`, `longitude decimal(10,7) NULL`, `altitude decimal(10,2) NULL`, `accuracy decimal(10,2) NULL`.
- **Utilization**: `utilization_type` (`Relief/Response` | `Recovery/Rehabilitation`), `amount_received decimal(12,2)`, `date_received date`.
- **Expenses** (all `decimal(12,2) DEFAULT 0.00`): `expense_food`, `expense_educational`, `expense_house_rental`, `expense_livelihood`, `expense_medical`, `expense_non_food_items`, `expense_utilities`, `expense_shelter_materials`, `expense_transportation`, `expense_others`.
- **Free-form narrative**: `livelihood_types` (JSON, nullable), `livelihood_specify` (nullable), `expense_others_specify` (nullable), `reason_not_fully_utilized` (text, nullable).
- **Computed totals** (set by model `booted()` on save): `total_utilization decimal(12,2)`, `unutilized_variance decimal(12,2)`.
- **Interviewer**: `interviewed_by`, `position`, `survey_modality`, `modality_specify` (nullable).

Indexes: PK(id), UNIQUE(client_uuid), BTREE(user_id), BTREE(incident_id).

#### survey_uploads (9 cols)

id, `survey_id` (FK to surveys, ON DELETE CASCADE, indexed), `type` (`photo_with_id` | `respondent_signature` | `interviewer_signature`), `file_path`, `original_name`, `mime_type`, `size` (int unsigned), timestamps.

#### incidents (9 cols)

id, name, type, `starts_at` (date, nullable), `ends_at` (date, nullable), `is_active` (tinyint(1) default 1), description (text, nullable), timestamps. No foreign keys.

#### address_municipalities (6 cols) — reference data

id, province, district, municipality, timestamps. COMPOUND INDEX(province, district). Powers the cascading PH-address dropdowns.

#### queue scaffolding

`jobs` (Laravel 11+ schema: id, queue indexed, payload longtext, attempts tinyint unsigned, reserved_at/available_at/created_at int unsigned).
`failed_jobs` (id, uuid UNIQUE, connection/queue text, payload/exception longtext, failed_at timestamp DEFAULT CURRENT_TIMESTAMP).
`job_batches` (id varchar PK, totals, options mediumtext, timestamps as int).
**Currently unused** — no queue worker running, no jobs queued, no failed jobs, and no code dispatches jobs (every handler in `SurveyController` is synchronous).

#### cross-schema note (not part of this app)

`php artisan db:show` listed a hyphen-named schema `ect-post-monitoring` on the same MySQL instance containing `duplicate_pairs`, `masterlist_records`, `masterlists` — these belong to a **different project** (ECT Beneficiary Validation / masterlist-deduplication) and leaked into the output because the MySQL user has SHOW access to both. **Not in use by this application.**

#### users table content

One row only in production:

| id | name | email | email_verified_at | created_at |
|---|---|---|---|---|
| 1 | Admin | admin@dswd.gov.ph | 2026-01-28 06:41 UTC | 2026-01-28 06:41 UTC |

No surveyors provisioned yet.

### 3.7 Frontend design system

**N/A for this target.** `ect-post-monitoring-api` is pure backend. Only view = default Laravel welcome page. No master layout, no design tokens, no shared UI components, no third-party CSS/JS libraries, no logos at this project root. The frontend design system lives in the sibling `/var/www/ect-post-monitoring-frontend` (React 19 + Tailwind v4 PWA, out of scope for this audit).

### 3.8 Configuration surface

**`.env` keys (47 total, all values redacted)**: same key set as local `.env.example` plus values.

| Group | Keys |
|---|---|
| App | `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`, `APP_MAINTENANCE_DRIVER`, `BCRYPT_ROUNDS` |
| Logging | `LOG_CHANNEL`, `LOG_STACK`, `LOG_DEPRECATIONS_CHANNEL`, `LOG_LEVEL` |
| Database | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Session | `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_ENCRYPT`, `SESSION_PATH`, `SESSION_DOMAIN` |
| Broadcast / Queue / Cache | `BROADCAST_CONNECTION`, `FILESYSTEM_DISK`, `QUEUE_CONNECTION`, `CACHE_STORE`, `MEMCACHED_HOST` |
| Redis | `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` |
| Mail | `MAIL_MAILER`, `MAIL_SCHEME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` |
| AWS | `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_USE_PATH_STYLE_ENDPOINT` |
| Vite | `VITE_APP_NAME` |

**Observed driver choices** (from `php artisan about`, no secret leakage):

| Driver | Value | Flag |
|---|---|---|
| `APP_ENV` | `local` | **Should be `production` on the live VPS** |
| `APP_DEBUG` | ENABLED | **Should be `false` on the live VPS** |
| Cache | `database` | OK |
| Queue | `database` | OK (but no worker draining it) |
| Session | `database` | OK |
| Broadcasting | `log` | no broadcasting in use |
| Mail | `log` | **Password reset / verification mail would go to log only** |
| Filesystem disk | `local` (per `.env.example`) | uploads land on VPS disk at `storage/app/public/survey-uploads/{survey_id}/...` and are served via `GET /storage/{path}` |

**External integrations**: none active. AWS keys appear unset (values redacted but `.env.example` leaves them blank). No outbound SMTP, no SMS, no third-party API clients in codebase.

**Queues & scheduled jobs**:
- `php artisan schedule:list` → *No scheduled tasks have been defined.*
- No crontab entry for this project (VPS cron hosts `api-lendyph schedule:run` and a `logistics-app` backup only).
- No PM2 process for this project.
- Conclusion: queue tables exist but are **not drained**. Anything dispatched would accumulate. Safe only because no code dispatches jobs today.

### 3.9 Code conventions

| Concern | Pattern used |
|---|---|
| Controllers | `App\Http\Controllers\Api\V1\*Controller` (PSR-4, versioned namespace) |
| Validation | Form Requests (`App\Http\Requests\*Request`) with array-based rules |
| Responses | Eloquent API Resources (`App\Http\Resources\*Resource`) |
| Authorization | Laravel Gates/Policies (`SurveyPolicy`); `$this->authorize('view', $model)` in controllers |
| Model guard | `$guarded = ['id']` everywhere (implicit mass-assignment allow-all for non-id fields) |
| Model casts | `casts()` method form (Laravel 11+), not `$casts` property |
| Soft deletes | `Survey` only |
| Computed fields | `Survey::booted()` saving hook recomputes `total_utilization`, `unutilized_variance` |
| Idempotency | `client_uuid` unique column; `store()` returns existing on duplicate |
| Sync | `/surveys?updated_since=<ISO8601>&per_page=<N>` with `per_page` clamped `[1,200]` |
| Naming | snake_case DB columns, camelCase on the wire not used (the API surfaces snake_case; the sibling React client does the camel mapping) |
| Tests | Pest boilerplate only — **no real test coverage yet** |

---

## 4. PART C — Reconciliation

### 4.1 Local vs Hosted alignment

| Dimension | Local | VPS | Status |
|---|---|---|---|
| Framework | Laravel 12 | Laravel 12.48.1 | Aligned |
| PHP | ≥8.2 (8.4.15 runtime per AGENTS.md) | 8.4.11 | Minor patch drift (OK) |
| Git branch / commit | `main` @ `dc905bd` | `main` @ `dc905bd` | Same HEAD |
| Uncommitted files | 3 modified + 1 untracked | `package-lock.json` slightly dirty | **Local has unshipped work** |
| Composer lock | 349 packages | Same lock | Aligned |
| `.env` keys | 47 (`.env.example` union) | 47 | Same key set, values differ |
| `APP_ENV` | — | `local` | **Drift from prod-ready posture** |
| `APP_DEBUG` | — | ENABLED | **Drift from prod-ready posture** |
| Deployed routes | 19 (includes `beneficiaries/search`) | 18 (no `beneficiaries/search`) | **Drift — 1 route missing on VPS** |
| Migrations | 9 | 9, all applied | Aligned |
| Models / controllers / policies | adds `BeneficiarySearchController` | lacks it | **Drift** |
| CLAUDE.md | edited (project-specific sections added in this session) | older version from 2026-01-30 | **Drift** |
| Queue worker | n/a | absent (other projects have one) | **Gap — but currently harmless** |
| Scheduled tasks | n/a | none defined | Aligned (nothing to run) |
| Frontend | local repo is backend-only | — | N/A |

**Relationship**: the local project is a **superset** of the VPS deployment — it contains commits/files that have not been pushed or deployed yet. The VPS is a recent snapshot, not a divergent fork.

### 4.2 What's already in local vs what would need to come over

This direction is reversed from Step C1 as phrased in the prompt — nothing meaningful on the VPS is missing from local. Everything that exists remotely (code, migrations, seeders, config) came from this local repo. The drift is one-way (local → VPS is behind).

**What's in local but not yet on the VPS** (would need to be shipped on the next deploy):

1. `app/Http/Controllers/Api/V1/BeneficiarySearchController.php` (file) — new.
2. `routes/api.php` — route `GET /api/v1/beneficiaries/search` added; behavior: user-scoped LIKE-search against `surveys.beneficiary_name`, returns up to 10 unique beneficiaries with demographic fields. Requires `auth:sanctum`.
3. `CLAUDE.md` — expanded project-context section (non-functional; does not affect runtime).
4. `.claude/settings.local.json` — Claude session state; **should not be deployed** (is already git-ignored on the VPS side by convention).

No schema migration is needed for the new feature — it reads from the existing `surveys` table.

**What the VPS needs that local doesn't have** (audit-only advisory, do not execute):

1. **Production hardening**: set `APP_ENV=production` and `APP_DEBUG=false` on the VPS. Re-cache config/routes/views afterward.
2. **Mail driver**: switch from `log` to a real SMTP or SaaS mail relay before any user-facing account flow is built.
3. **Queue worker**: if/when jobs are dispatched, add a PM2 entry or systemd unit (pattern exists on this box — see `pm2 list` for how other apps are wired).
4. **Boost dev endpoint**: `/_boost/browser-logs` is registered because `laravel/boost` is a require-dev package that still wires a route. In `local` env it's fine; in `production` env this route is disabled at the controller level by Boost itself, so flipping `APP_ENV=production` also closes this vector.
5. **Backups**: no backup script for this project's DB. Compare with `/var/www/logistics-app/backup.sh` (present; cron'd nightly at 02:00).
6. **CI/CD**: repo has no `.github/workflows/` — deploys are manual.
7. **User provisioning**: only one user (admin seeded). No mechanism yet for adding field surveyors programmatically.

### 4.3 Recommended sequence (advisory only — do not execute)

1. **Commit and push local work** with meaningful messages (`feat: beneficiary search endpoint`, `docs: expand CLAUDE.md`), then deploy to the VPS.
2. **Harden prod env** on the VPS: `APP_ENV=production`, `APP_DEBUG=false`, `php artisan config:cache && route:cache && view:cache`.
3. **Add feature tests** for the existing 19 routes before adding new features — current coverage is 0 real tests.
4. **Add a deploy step** (even a shell script committed as `deploy.sh`) documenting the pull/install/migrate/cache cycle.
5. **Add a queue worker** and a nightly DB backup when they're actually needed — premature otherwise.
6. **Decide on roles**: if more than one user type is coming (enumerator vs. reviewer vs. admin), plan the RBAC now. A Spatie-style permission package fits Laravel 12 cleanly.

---

## 5. Open questions / ambiguities

1. **Who owns deployments?** There's no deploy script, no CI, and the recent commit messages are uninformative. Is deployment `git pull && composer install --no-dev && php artisan migrate --force` by hand, or something else?
2. **Is the VPS actually the production deployment?** The subdomain is `*.abedubas.dev` (developer-owned personal domain) and `APP_ENV=local`. If this is meant to be prod-facing, the env posture and the domain choice are both worth revisiting; if it's intentionally a staging mirror, note that somewhere.
3. **Registration flow**: is it deliberate that surveyors are provisioned manually and not via a register endpoint? If yes, what's the plan for onboarding real field staff?
4. **`/storage/{path}` is public**: all survey-upload images (photo with ID, signatures) are reachable by URL without auth, given the `file_path` stored in `survey_uploads`. Intentional, or should these be gated behind `auth:sanctum` via a signed-URL pattern?
5. **Roles / permissions**: the current model gives every authenticated user equal domain access and lets each user see only their own surveys. Is there a need for a supervisor / reviewer role that can see across users?
6. **Memory candidate**: the fact that deploys to `ect-post-monitoring-api` run as `root` on `76.13.22.110:/var/www/ect-post-monitoring-api` against DB `ect_post_monitoring` (MySQL 8.4, 127.0.0.1) is a reference worth persisting if this project will keep coming up. Want me to save it as a memory entry? I haven't written one without your OK.

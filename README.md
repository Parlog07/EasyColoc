# EasyColoc

EasyColoc is a Laravel 12 monolith for managing shared flat expenses.

## Stack
- Laravel 12
- PHP 8.2+
- PostgreSQL
- Laravel Breeze (auth)
- Tailwind CSS + Blade

## Core Features
- Colocations: create, show, cancel
- Memberships with roles (`owner`, `member`), `joined_at`, `left_at`
- Invitation by email + token (expires in 1 hour)
- Accept / refuse invitation via token links
- Expenses with categories, payer, amount, date
- Balances and simplified settlements (who owes who)
- Payments (`Mark Paid`) with balance updates
- Reputation system (`+1` / `-1`) based on debt behavior
- Admin dashboard: global stats + ban/unban users
- Banned users are auto-logged out and blocked

## Business Rules Implemented
- One active colocation per user
- Owner cannot cancel a colocation while other active members exist
- Owner can transfer ownership to an active member
- Owner can remove members (owner cannot remove self)
- On leave/cancel/remove:
  - debtor => `reputation -1`
  - no debt => `reputation +1`
- If owner removes a debtor member, debt is transferred to owner via payment record

## Local Setup
```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Configure PostgreSQL in `.env`, then run:

```bash
php artisan migrate
```

Run app:

```bash
php artisan serve
npm run dev
```

## Mail Setup (Gmail SMTP)
In `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=smtp
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD="your_16_char_app_password"
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="EasyColoc"
```

Then clear config cache:

```bash
php artisan config:clear
```

### Gmail App Password
1. Enable 2-Step Verification on your Google account.
2. Go to Google Account -> Security -> App passwords.
3. Generate an app password.
4. Use that value as `MAIL_PASSWORD`.

## Main Routes
- `/dashboard`
- `/colocations`
- `/colocations/create`
- `/colocations/{colocation}`
- `/admin` (admin only)

Invitation routes:
- `POST /colocations/{colocation}/invite`
- `GET /invitations/{token}/accept`
- `GET /invitations/{token}/refuse`

## Notes
- UI is Blade-based (not React/Next runtime).
- If UI changes do not appear, run:

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
npm run build
```

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
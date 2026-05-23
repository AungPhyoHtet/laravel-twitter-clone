# Laravel Twitter Clone

A Twitter-like REST API built with Laravel, designed for mobile clients.

## Stack

- **PHP** 8.3 / **Laravel** 13
- **Authentication** — Laravel Sanctum (token-based)
- **Testing** — Pest 4

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## Running locally

The app is served automatically by [Laravel Herd](https://herd.laravel.com) at:

```
http://laravel-twitter-clone.test
```

## Testing

```bash
php artisan test --compact
```

## API

All endpoints are versioned under `/api/v1`. Authentication uses Sanctum bearer tokens.

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/v1/register` | Register |
| `POST` | `/api/v1/login` | Login |
| `POST` | `/api/v1/logout` | Logout |
| `GET` | `/api/v1/users/{id}` | Get user profile |
| `GET` | `/api/v1/users/{id}/tweets` | User's tweets |
| `POST` | `/api/v1/users/{id}/follow` | Follow a user |
| `DELETE` | `/api/v1/users/{id}/follow` | Unfollow a user |
| `GET` | `/api/v1/users/{id}/followers` | List followers |
| `GET` | `/api/v1/users/{id}/following` | List following |
| `GET` | `/api/v1/feed` | Feed (tweets from followed users) |
| `GET` | `/api/v1/tweets` | All tweets |
| `POST` | `/api/v1/tweets` | Create a tweet |
| `GET` | `/api/v1/tweets/{id}` | Get a tweet |
| `DELETE` | `/api/v1/tweets/{id}` | Delete a tweet |
| `GET` | `/api/v1/tweets/search?q=` | Search tweets |

### Detailed documentation

- [Authentication](docs/api/auth.md)
- [Users](docs/api/users.md)
- [Tweets](docs/api/tweets.md)
- [Follows & Feed](docs/api/follows.md)

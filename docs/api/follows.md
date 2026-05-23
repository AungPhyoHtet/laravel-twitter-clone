# Follows & Feed API

## Authentication

All endpoints require a Sanctum bearer token.

```
Authorization: Bearer <token>
```

---

## Endpoints

### Follow a user

```
POST /api/v1/users/{id}/follow
```

**Responses**

| Status | Description |
|--------|-------------|
| 201 | Successfully followed |
| 401 | Unauthenticated |
| 404 | User not found |
| 422 | Cannot follow yourself / already following |

---

### Unfollow a user

```
DELETE /api/v1/users/{id}/follow
```

**Responses**

| Status | Description |
|--------|-------------|
| 204 | Successfully unfollowed |
| 401 | Unauthenticated |
| 404 | User not found |
| 422 | Not currently following this user |

---

### List followers

Returns a paginated list of users who follow `{id}`.

```
GET /api/v1/users/{id}/followers
```

**Response `200`**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Jane Doe",
      "username": "janedoe",
      "avatar": null
    }
  ],
  "links": {
    "first": "http://laravel-twitter-clone.test/api/v1/users/1/followers?page=1",
    "last": "http://laravel-twitter-clone.test/api/v1/users/1/followers?page=3",
    "prev": null,
    "next": "http://laravel-twitter-clone.test/api/v1/users/1/followers?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 10,
    "to": 10,
    "total": 25
  }
}
```

---

### List following

Returns a paginated list of users that `{id}` follows.

```
GET /api/v1/users/{id}/following
```

**Response `200`** — same structure as followers.

---

### Feed

Returns paginated tweets from users the authenticated user follows, ordered newest first.

```
GET /api/v1/feed
```

**Response `200`**

```json
{
  "data": [
    {
      "id": 12,
      "user_id": 5,
      "body": "Hello world",
      "user": {
        "id": 5,
        "name": "Jane Doe",
        "username": "janedoe",
        "avatar": null
      },
      "created_at": "2026-05-23T13:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://laravel-twitter-clone.test/api/v1/feed?page=1",
    "last": "http://laravel-twitter-clone.test/api/v1/feed?page=5",
    "prev": null,
    "next": "http://laravel-twitter-clone.test/api/v1/feed?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 10,
    "to": 10,
    "total": 44
  }
}
```

Returns an empty `data` array when the user follows nobody.

---

## Error responses

| Scenario | Status | Message |
|----------|--------|---------|
| Unauthenticated | 401 | `Unauthenticated.` |
| User not found | 404 | `User not found.` |
| Self-follow | 422 | `You cannot follow yourself.` |
| Already following | 422 | `You are already following this user.` |
| Not following | 422 | `You are not following this user.` |

```json
{
  "message": "User not found."
}
```

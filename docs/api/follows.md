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
  "links": { ... },
  "meta": { ... }
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
  "links": { ... },
  "meta": { ... }
}
```

Returns an empty `data` array when the user follows nobody.

---

## Error response format

```json
{
  "message": "User not found."
}
```

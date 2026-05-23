# Tweets API

## Authentication

All endpoints require a Sanctum bearer token.

```
Authorization: Bearer <token>
```

---

## Endpoints

### List tweets

Returns all tweets paginated, ordered newest first.

```
GET /api/v1/tweets
```

**Response `200`**

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 3,
      "body": "Hello world",
      "user": {
        "id": 3,
        "name": "Jane Doe",
        "username": "janedoe",
        "avatar": null
      },
      "created_at": "2026-05-23T13:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://laravel-twitter-clone.test/api/v1/tweets?page=1",
    "last": "http://laravel-twitter-clone.test/api/v1/tweets?page=10",
    "prev": null,
    "next": "http://laravel-twitter-clone.test/api/v1/tweets?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 10,
    "to": 10,
    "total": 100
  }
}
```

---

### Get a tweet

```
GET /api/v1/tweets/{id}
```

**Response `200`**

```json
{
  "data": {
    "id": 1,
    "user_id": 3,
    "body": "Hello world",
    "user": {
      "id": 3,
      "name": "Jane Doe",
      "username": "janedoe",
      "avatar": null
    },
    "created_at": "2026-05-23T13:00:00.000000Z"
  }
}
```

**Response `404`**

```json
{
  "message": "Tweet not found."
}
```

---

### Create a tweet

```
POST /api/v1/tweets
```

**Request body**

```json
{
  "body": "Hello world"
}
```

**Response `201`**

```json
{
  "data": {
    "id": 42,
    "user_id": 3,
    "body": "Hello world",
    "user": {
      "id": 3,
      "name": "Jane Doe",
      "username": "janedoe",
      "avatar": null
    },
    "created_at": "2026-05-23T13:00:00.000000Z"
  }
}
```

**Validation errors `422`**

| Field | Scenario | Message |
|-------|----------|---------|
| `body` | Missing | `The body field is required.` |
| `body` | Over 280 characters | `The body field must not be greater than 280 characters.` |

---

### Delete a tweet

Only the tweet's owner can delete it.

```
DELETE /api/v1/tweets/{id}
```

**Response `204`** — No content.

**Response `403`** — Deleting another user's tweet.

```json
{
  "message": "This action is unauthorized."
}
```

**Response `404`** — Tweet not found (route model binding).

```json
{
  "message": "No query results for model [App\\Models\\Tweet]."
}
```

---

### Search tweets

Searches tweet body text. Case-insensitive, paginated, ordered newest first.

```
GET /api/v1/tweets/search?search={keyword}
```

**Query parameters**

| Parameter | Required | Description |
|-----------|----------|-------------|
| `search` | Yes | Keyword to search in tweet body |

**Response `200`** — same structure as list tweets.

**Response `422`**

```json
{
  "message": "The search field is required.",
  "errors": {
    "search": ["The search field is required."]
  }
}
```

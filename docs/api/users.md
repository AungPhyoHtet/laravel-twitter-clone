# Users API

## Authentication

All endpoints require a Sanctum bearer token.

```
Authorization: Bearer <token>
```

---

## Endpoints

### Get user profile

```
GET /api/v1/users/{id}
```

**Response `200`**

```json
{
  "id": 1,
  "name": "Jane Doe",
  "username": "janedoe",
  "avatar": null,
  "profile": "This is my bio.",
  "location": "San Francisco, United States",
  "link": "https://example.com",
  "link_text": "example.com",
  "created_at": "2026-05-01T10:00:00.000000Z",
  "tweets": [
    {
      "id": 5,
      "body": "Hello world",
      "created_at": "2026-05-23T13:00:00.000000Z"
    }
  ]
}
```

> Tweets are ordered newest first and not paginated on this endpoint. Use `GET /api/v1/users/{id}/tweets` for paginated results.

**Response `404`**

```json
{
  "message": "User not found."
}
```

---

### List user tweets

Returns the user's tweets paginated, ordered newest first.

```
GET /api/v1/users/{id}/tweets
```

**Response `200`**

```json
{
  "data": [
    {
      "id": 5,
      "user_id": 1,
      "body": "Hello world",
      "user": {
        "id": 1,
        "name": "Jane Doe",
        "username": "janedoe",
        "avatar": null
      },
      "created_at": "2026-05-23T13:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://laravel-twitter-clone.test/api/v1/users/1/tweets?page=1",
    "last": "http://laravel-twitter-clone.test/api/v1/users/1/tweets?page=3",
    "prev": null,
    "next": "http://laravel-twitter-clone.test/api/v1/users/1/tweets?page=2"
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

**Response `404`**

```json
{
  "message": "User not found."
}
```

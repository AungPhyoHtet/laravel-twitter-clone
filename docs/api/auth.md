# Auth API

## Endpoints

### Register

```
POST /api/v1/register
```

**Request body**

```json
{
  "name": "Jane Doe",
  "username": "janedoe",
  "email": "jane@example.com",
  "password": "password",
  "password_confirmation": "password",
  "device_name": "iPhone 15"
}
```

**Response `201`**

```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Jane Doe",
    "username": "janedoe",
    "email": "jane@example.com",
    "avatar": null
  }
}
```

---

### Login

```
POST /api/v1/login
```

**Request body**

```json
{
  "email": "jane@example.com",
  "password": "password",
  "device_name": "iPhone 15"
}
```

**Response `200`**

```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Jane Doe",
    "username": "janedoe",
    "email": "jane@example.com",
    "avatar": null
  }
}
```

---

### Logout

Deletes the current device token. Requires authentication.

```
POST /api/v1/logout
```

**Response `200`**

```json
{
  "message": "Logged out successfully."
}
```

---

## Error responses

### Validation errors `422`

```json
{
  "message": "Password must be at least 8 characters.",
  "errors": {
    "password": [
      "Password must be at least 8 characters."
    ]
  }
}
```

| Field | Scenario | Message |
|-------|----------|---------|
| `name` | Missing | `Name is required.` |
| `username` | Missing | `Username is required.` |
| `username` | Taken | `This username is already taken.` |
| `email` | Missing | `Email is required.` |
| `email` | Invalid format | `Please provide a valid email address.` |
| `email` | Already registered | `This email is already registered.` |
| `password` | Missing | `Password is required.` |
| `password` | Under 8 chars | `Password must be at least 8 characters.` |
| `password` | Mismatch | `Password confirmation does not match.` |
| `password_confirmation` | Missing | `Password confirmation is required.` |
| `device_name` | Missing | `Device name is required.` |

### Invalid credentials `422`

```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```

### Unauthenticated `401`

```json
{
  "message": "Unauthenticated."
}
```

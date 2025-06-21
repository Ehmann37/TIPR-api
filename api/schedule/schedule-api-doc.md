# 🚌 Schedule API Documentation

> This API handles all operations related to trip schedules including adding, fetching, updating, and deleting.

---

## 🔐 Authentication

All endpoints require an `Authorization` header:

```
Authorization: trip123api
Content-Type: application/json
```

---

## 📥 Fields (Used in JSON)

| Field           | Type    | Description                                |
|-----------------|---------|--------------------------------------------|
| `schedule_id`   | Integer | Unique ID of the schedule (auto-generated) |
| `first_trip`    | String  | First trip time (format: `HH:MM AM/PM`)     |
| `last_trip`     | String  | Last trip time (format: `HH:MM AM/PM`)      |
| `time_interval` | Integer | Time gap (in minutes) between each trip     |

---

## 📌 Endpoints

### ➕ Add Schedule

`POST https://trip-api.dcism.org/api/schedule/add.php`

#### Request Body:
```json
{
  "first_trip": "03:00 AM",
  "last_trip": "07:00 PM",
  "time_interval": 30
}
```

#### Response:
```json
{
  "status": "success",
  "message": "Schedule added successfully"
}
```

---

### 📋 Get All Schedules

`GET https://trip-api.dcism.org/api/schedule/get.php`

#### Response:
```json
{
  "status": "success",
  "data": [
    {
      "schedule_id": 1,
      "first_trip": "03:00:00",
      "last_trip": "19:00:00",
      "time_interval": 30
    },
    ...
  ]
}
```

---

### 🔍 Get Schedule by ID

`GET https://trip-api.dcism.org/api/schedule/getById.php?id=1`

#### Response (if found):
```json
{
  "status": "success",
  "data": {
    "schedule_id": 1,
    "first_trip": "03:00:00",
    "last_trip": "19:00:00",
    "time_interval": 30
  }
}
```

#### Response (if not found):
```json
{
  "status": "error",
  "message": "Schedule not found"
}
```

---

### ✏️ Update Schedule

`PUT https://trip-api.dcism.org/api/schedule/update.php`

#### Request Body:
```json
{
  "schedule_id": 1,
  "first_trip": "04:00 AM",
  "last_trip": "06:00 PM",
  "time_interval": 20
}
```

#### Response:
```json
{
  "status": "success",
  "message": "Schedule updated successfully"
}
```

---

### 🗑️ Delete Schedule

`DELETE https://trip-api.dcism.org/api/schedule/delete.php?id=1`

#### Response (if successful):
```json
{
  "status": "success",
  "message": "Schedule deleted successfully"
}
```

#### Response (if not found):
```json
{
  "status": "error",
  "message": "Schedule not found"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using **Postman**.
- Make sure to set **Authorization header** and use **raw JSON** for `POST`.

---

## 📌 Notes

- `first_trip` and `last_trip` can be in `HH:MM AM/PM` (will be converted to `HH:MM:SS`)
- `time_interval` must be an integer (in minutes)
- All responses are in **JSON** format
- Use these endpoints when assigning schedules to routes or buses
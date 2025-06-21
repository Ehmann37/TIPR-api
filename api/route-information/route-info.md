# 🚍 Route Information API Documentation

RESTful API for managing route metadata in the TRIP system.

---

## 🔐 Authorization

All requests require the following headers:
```
Authorization: Bearer trip123api
Content-Type: application/json
```


---

## 📦 Endpoints

### ➕ POST `/api/route-information/add.php`

Add a new route.

#### Body:
```json
{
  "route_name": "City Circle Line",
  "schedule_id": 1
}

```

#### Response
```json
{
  "status": "success"
}
```

---

### 📤 GET `/api/route-information/get.php`

Retrieve all routes.

#### Response
```json
{
  "status": "success",
  "data": [
    {
      "route_id": 1,
      "route_name": "City Circle Line",
      "schedule_id": 1
    },
    ...
  ]
}
```

---

### 🔎 GET `/api/route-information/getById.php?id=1`

Retrieve a route by its ID.

#### Response
```json
{
  "status": "success",
  "data": {
    "route_id": 1,
    "route_name": "City Circle Line",
    "schedule_id": 1
  }
}
```

---

### ✏️ PUT `/api/route-information/update.php`

Update route details.

#### Request Body
```json
{
  "route_id": 1,
  "route_name": "Express East Line",
  "schedule_id": 2
}
```

#### Response
```json
{
  "status": "success"
}
```

---

### 🗑 DELETE `/api/route-information/delete.php`

Delete a route by ID.

#### Request Body
```json
{
  "route_id": 1
}
```

#### Response
```json
{
  "status": "success"
}
```

---

## 🧠 Notes

- route_id is auto-incremented in the database.
- schedule_id must already exist in the schedule table.
- Use get.php to populate dropdowns or list views in the frontend.
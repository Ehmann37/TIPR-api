# 🛑 Stop API — REST Documentation

Manage terminal stops used in bus routes and trips.

## 🔐 Authorization

All endpoints require:

```
Authorization: Bearer trip123api
Content-Type: application/json
```

---

## 🔄 Endpoints

### 📥 POST `/api/stop/add.php`

Add a new stop.

#### Request Body
```json
{
  "stop_name": "CSBT",
  "longitude": 123.89316865374035,
  "latitude": 10.298358793808859
}
```

#### Response
```json
{
  "status": "success"
}
```

---

### 📤 GET `/api/stop/get.php`

Retrieve all stops.

#### Response
```json
{
  "status": "success",
  "data": [
    {
      "stop_id": 1,
      "stop_name": "CSBT",
      "longitude": 123.89316865374035,
      "latitude": 10.298358793808859
    },
    ...
  ]
}
```

---

### 🔎 GET `/api/stop/getById.php?id=1`

Retrieve a stop by its ID.

#### Response
```json
{
  "status": "success",
  "data": {
    "stop_id": 1,
    "stop_name": "CSBT",
    "longitude": 123.89316865374035,
    "latitude": 10.298358793808859
  }
}
```

---

### ✏️ PUT `/api/stop/update.php`

Update a stop's information.

#### Request Body
```json
{
  "stop_id": 1,
  "stop_name": "Updated Terminal",
  "longitude": 123.900000,
  "latitude": 10.300000
}
```

#### Response
```json
{
  "status": "success"
}
```

---

### 🗑 DELETE `/api/stop/delete.php`

Delete a stop by ID.

#### Request Body
```json
{
  "stop_id": 1
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

- Longitude and latitude are stored as `FLOAT` or `DECIMAL` in the DB.
- `stop_id` is auto-incremented.
- Use the `get.php` and `getById.php` endpoints to fetch stop info for dropdowns or route building.
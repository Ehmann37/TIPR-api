# 🚌 Route API Documentation

This API manages the route-stop mappings in the TRIP system.

Each route is made up of a list of stops, each with an assigned order.  
This API allows frontend apps to add, retrieve, update, and delete those mappings.

---

## 🔐 Authorization

All endpoints require a bearer token in the header:
```
Authorization: Bearer trip123api
Content-Type: application/json
```


---

## 📄 Endpoints

### 1. ➕ Add Route Stop

**URL:** `/api/route/add.php`  
**Method:** `POST`  
**Body:** JSON

```json
{
  "route_id": 1,
  "stop_id": 2,
  "stop_order": 1
}

```

#### Response
```json
{
  "status": "success"
}
```

---

### 2. 📥 Get All Route Stops
**URL:** `/api/route/get.php`  
**Method:** `GET` 


#### Response
```json
{
  "status": "success",
  "data": [
    {
      "route_id": 1,
      "stop_id": 2,
      "stop_order": 1
    },
    ...
  ]
}
```

---

### 3. 🔍 Get Route Stops by Route ID
**URL:** `/api/route/getByRouteId.php?route_id=1`  
**Method:** `GET` 

#### Response
```json
{
  "status": "success",
  "data": [
    {
      "route_id": 1,
      "stop_id": 2,
      "stop_order": 1
    }
  ]
}
```

---
### 4. 🔍 Get One Stop in a Route
**URL:** `/api/route/getOne.php?route_id=1&stop_id=2`  
**Method:** `GET` 

#### Response
```json
{
  "status": "success",
  "data": {
    "route_id": 1,
    "stop_id": 2,
    "stop_order": 1
  }
}
```

---


### 5. ✏️ Update Stop Order

**URL:** `/api/route/update.php`  
**Method:** `PUT`
**Body:** JSON


#### Request Body
```json
{
  "route_id": 1,
  "stop_id": 2,
  "stop_order": 3
}
```

#### Response
```json
{
  "status": "success"
}
```

---

### 🗑️ Delete Stop from Route

**URL:** `/api/route/delete.php`  
**Method:** `DELETE`
**Body:** JSON


#### Request Body
```json
{
  "route_id": 1,
  "stop_id": 2
}
```

#### Response
```json
{ "status": "success" }
```

---

## 🧠 Notes

- route_id + stop_id are used together as a composite key.
- Always check ordering using stop_order
- You must create stops and route information before using this API.

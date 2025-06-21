# Driver API Documentation

This API manages bus driver data in the TRIP system. All endpoints require API token authentication.

## Authorization

Add the following header to all requests:
```
Authorization: Bearer trip123api
```

## Endpoints

### 1. Get All Drivers

Retrieves all bus drivers in the system.

- **URL**: `/api/driver/get.php`
- **Method**: `GET`
- **Authentication**: Required
- **Response**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "driver_id": 1,
        "company_id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "license_number": "LIC12345678",
        "contact_info": "09123456789"
      },
      // ... other drivers
    ]
  }
  ```

### 2. Get Driver by ID

Retrieves a specific driver by ID.

- **URL**: `/api/driver/getById.php?id={driver_id}`
- **Method**: `GET`
- **Parameters**: `id` (integer) - The driver ID
- **Authentication**: Required
- **Response**:
  ```json
  {
    "status": "success",
    "data": {
      "driver_id": 1,
      "company_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "license_number": "LIC12345678",
      "contact_info": "09123456789"
    }
  }
  ```

### 3. Add New Driver

Creates a new driver record.

- **URL**: `/api/driver/add.php`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "company_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "license_number": "LIC12345678",
    "contact_info": "09123456789"
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "driver_id": 1
  }
  ```

### 4. Update Driver

Updates an existing driver record.

- **URL**: `/api/driver/update.php`
- **Method**: `PUT`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "driver_id": 1,
    "company_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "license_number": "LIC12345678",
    "contact_info": "09123456789"
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Driver updated successfully"
  }
  ```

### 5. Delete Driver

Deletes an existing driver record.

- **URL**: `/api/driver/delete.php`
- **Method**: `DELETE`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "driver_id": 1
  }
  ```
- **Response**:
  ```json
  {
    "status": "success",
    "message": "Driver deleted successfully"
  }
  ```

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
  "status": "error",
  "message": "Missing required fields" 
}
```

### 401 Unauthorized
```json
{
  "status": "error",
  "message": "Unauthorized access. Invalid token."
}
```

### 404 Not Found
```json
{
  "status": "error",
  "message": "Driver not found"
}
```

### 500 Internal Server Error
```json
{
  "status": "error",
  "message": "Failed to [operation]",
  "error": "Error details"
}
``` 
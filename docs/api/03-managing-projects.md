Managing Projects API Resources
===============================

[Go back to API home](01-api.md)

Resources for managing projects.

### POST http://your.trk.life.domain/api/categories/create ###

Create a new project category.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| name              | required  | *                    | The name of the new category. |
| order             | optional  | int >= 0             | The order to display the category. A positive int, with the lowest being shown first, and highest last. Will default to null, meaning it will be displayed last in the list. |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    name: Customer Projects
    order: 1

**Example Response**

    {
        "status": "success",
        "message": "Successfully created category."
    }

---

### POST http://your.trk.life.domain/api/categories/{id}/update ###

Update a project category.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| name              | required  | *                    | The name of the category. |
| order             | optional  | int >= 0             | The order to display the category. A positive int, with the lowest being shown first, and highest last. Will default to null, meaning it will be displayed last in the list. |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    name: Core Platform
    order: 3

**Example Response**

    {
        "status": "success",
        "message": "Successfully updated category."
    }

---

### POST http://your.trk.life.domain/api/categories/{id}/archive ###

Archives a project category, making it hidden from view for weeks which don't have tracking data for any projects within
it, and means that new tracking data cannot be saved against any projects within it.

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Response**

    {
        "status": "success",
        "message": "Successfully archived category."
    }

---

### POST http://your.trk.life.domain/api/projects/create ###

Create a new project.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| name              | required  | *                    | The name of the new project. |
| category_id       | required  | *                    | The ID of the category to put the project in. |
| order             | optional  | int >= 0             | The order to display the project within it's category. A positive int, with the lowest being shown first, and highest last. Will default to null, meaning it will be displayed last in the list. |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    name: Acme Co. Website
    order: 1

**Example Response**

    {
        "status": "success",
        "message": "Successfully created project."
    }

---

### POST http://your.trk.life.domain/api/projects/{id}/update ###

Update a project.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| name              | required  | *                    | The name of the project. |
| category_id       | required  | *                    | The ID of the category to put the project in. |
| order             | optional  | int >= 0             | The order to display the project within it's category. A positive int, with the lowest being shown first, and highest last. Will default to null, meaning it will be displayed last in the list. |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    name: Acme Co. Website
    order: 3

**Example Response**

    {
        "status": "success",
        "message": "Successfully updated project."
    }

---

### POST http://your.trk.life.domain/api/projects/{id}/archive ###

Archives a project, making it hidden from view for weeks which don't have tracking data for the project, and means 
that new tracking data cannot be saved against the project.

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Response**

    {
        "status": "success",
        "message": "Successfully archived project."
    }

---

### POST http://your.trk.life.domain/api/items/create ###

Create a new item within a project.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| name              | required  | *                    | The name of the new item. |
| project_id        | required  | *                    | The ID of the project the item is within. |
| order             | optional  | int >= 0             | The order to display the item within the project. A positive int, with the lowest being shown first, and highest last. Will default to null, meaning it will be displayed last in the list. |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    name: Development
    order: 1

**Example Response**

    {
        "status": "success",
        "message": "Successfully added item to project."
    }

---

### POST http://your.trk.life.domain/api/items/{id}/update ###

Update a project item.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| name              | required  | *                    | The name of the item. |
| project_id        | required  | *                    | The ID of the project the item is within. |
| order             | optional  | int >= 0             | The order to display the item within the project. A positive int, with the lowest being shown first, and highest last. Will default to null, meaning it will be displayed last in the list. |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    name: Development
    order: 3

**Example Response**

    {
        "status": "success",
        "message": "Successfully updated project item."
    }

---

### POST http://your.trk.life.domain/api/items/{id}/archive ###

Archives a project item, making it hidden from view for weeks which don't have tracking data for the item, and means 
that new tracking data cannot be saved against the item.

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Response**

    {
        "status": "success",
        "message": "Successfully archived project item."
    }

---

Next: [Tracking](04-tracking.md)

Previous: [Users](02-users.md)
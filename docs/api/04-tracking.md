Tracking API Resources
======================

[Go back to API home](01-api.md)

Resources for saving and retrieving tracking data.

### GET http://your.trk.life.domain/api/tracking/data ###

Receive all tracking data between two dates, inclusive. The data returns all levels for that time period for the 
user, i.e. it will return all categories, all projects within each category, and all items within each project, as well 
as all hours tracked between that time, and each day's journal entry.

**Arguments (Query params)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| start_date        | required  | YYYYMMDD             | The start date of the tracking data to return. |
| end_date          | required  | YYYYMMDD             | The end date of the tracking data to return. |

**Returns**

Returns a JSON object containing all of the tracking data, see the example response for more detail.

**Example Request**

    start_date: 20160613
    end_date: 20160619

**Example Response**

    {
        categories: [
            {
                id: 1,
                name: "Platform Work",
                order: 1,
                status: "active|archived",
                projects: [
                    {
                        id: 13,
                        name: "Feature One",
                        order: 1,
                        status: "active|archived",
                        items: [
                            {
                                id: 7,
                                name: "Development",
                                order: 1,
                                status: "active|archived",
                                hours: [
                                    20160613: 7.5,
                                    20160614: 2,
                                    20160615: 5.5,
                                    20160616: 7.5,
                                    20160617: 0,
                                    20160618: 0,
                                    20160619: 0
                                ]
                            },
                            ...
                        ]
                    },
                    ...
                ],
            },
            ...
        ],
        journals: {
            20160613: "",
            20160614: "Today I worked on client projects a lot, meaning not a lot platform work.",
            20160615: "",
            20160616: "",
            20160617: "Today there was a support disaster, so not much was done.",
            20160618: "",
            20160619: ""
        }
    }

---

### POST http://your.trk.life.domain/api/tracking/save ###

Save hours to an item on a given day.

**Arguments (Query params)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| item_id           | required  | *                    | The ID of the item to track time against. |
| date              | required  | YYYYMMDD             | The date to track hours on. |
| hours             | required  | decimal hours        | The number of hours to track. Must be less that 24 hours, and must be in decimal form. |

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    item_id: 7
    date: 20160615
    hours: 5.5

**Example Response**

    {
        "status": "success",
        "message": "Successfully tracked time."
    }

### POST http://your.trk.life.domain/api/journal/save ###

Save a journal entry against a date.

**Arguments (Query params)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| date              | required  | YYYYMMDD             | The date to save journal on. |
| journal           | required  | *                    | The text of the journal entry. |

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    date: 20160615
    journal: Today there was a support disaster, so not much was done.

**Example Response**

    {
        "status": "success",
        "message": "Successfully saved journal."
    }

---

Previous: [Managing Projects](03-managing-projects.md)
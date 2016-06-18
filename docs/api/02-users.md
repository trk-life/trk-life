Users API Resources
===================

[Go back to API home](01-api.md)

User related API resources.

# Auth related #

### POST http://your.trk.life.domain/api/users/login ###

Logs in a user, returning a token which can be used for authenticating future requests. Doesn't require the 
"Authorisation" header.

**Arguments (Form data)**

| Argument          | Required? | Allowed values | Description |
| ----------------- | --------- | -------------- | ----------- |
| email             | required  | *              | The user's email address |
| password          | required  | *              | The user's password |
| stay_logged_in    | optional  | 1, 0           | Whether to give the token a long expiry |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail", along with a message (see below), or "success" along with the
user object and the new token.

* message: Returned with the "fail" status, containing a user-friendly message with the failure reason.

* user: Returned with the "success" status, containing a JSON object with the user's details (see the example response 
below)

* token: Returned with the "success" status, containing the authentication token.

**Example Request**

    email: email@example.com
    password: password1
    stay_logged_in: 1

**Example Response**

    {
        "status": "success",
        "user": {
            "id": 12,
            "email": "email@example.com",
            "first_name": "John",
            "last_name": "Doe",
            "role": "user",
            "status": "active",
            "created": 1464897863,
            "modified": 1465939139
        },
        "token": "6653aed2427173ee9e4cb952b2ffefe7f11d0cf3d014c23f0aa1a8f17fa968a4"
    }

---

### GET http://your.trk.life.domain/api/users/logout ###

Logs out a user, invalidating the token passed in the "Authorisation" header. If the token passed is invalid,
a 401 Unauthorised response is returned.

**Returns**

JSON object containing the following properties:

* status: Always "success".

* message: Always "Successfully logged out.".

**Example Response**

    {
        "status": "success",
        "message": "Successfully logged out."
    }

---

### GET http://your.trk.life.domain/api/users/validate-token ###

Validate that a token is still valid, also returning the user's details. If the token passed is invalid,
a 401 Unauthorised response is returned.

**Returns**

* status: Always "success".

* message: Always "Token is valid".

* user: The logged in user's details (See example response below).

**Example Response**

    {
        "status": "success",
        "message": "Token is valid",
        "user": {
            "id": 12,
            "email": "email@example.com",
            "first_name": "John",
            "last_name": "Doe",
            "role": "user",
            "status": "active",
            "created": 1464897863,
            "modified": 1465939139
        }
    }

---

### POST http://your.trk.life.domain/api/users/forgotten-password ###

Request a password reset for a forgotten password, using an email address. If successful a reset link is sent via email. 
Doesn't require the "Authorisation" header.

**Arguments (Form data)**

| Argument          | Required? | Allowed values | Description |
| ----------------- | --------- | -------------- | ----------- |
| email             | required  | *              | The user's email address |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    email: email@example.com

**Example Response**

    {
        "status": "success",
        "message": "An email has been sent to this address containing a link to reset your password."
    }

---

### POST http://your.trk.life.domain/api/users/reset-password ###

Resets a user's password using the link sent via email by a forgotten password request. Doesn't require the
"Authorisation" header.

**Arguments (Form data)**

| Argument          | Required? | Allowed values | Description |
| ----------------- | --------- | -------------- | ----------- |
| token             | required  | *              | The token send via email in a forgotten password request |
| new_password      | required  | *              | The user's new password |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    token: 8b1b560704be4a04cd1587d05351e0f66f77e14bb3cdbc1a57e7aa8cbf6eac6b
    new_password: password2

**Example Response**

    {
        "status": "success",
        "message": "Successfully reset password."
    }

---

# User settings #

Resources for managing own user.

### GET http://your.trk.life.domain/api/settings/user/get ###

Get the currently logged in user's details.

**Returns**

A JSON object of the user.

* user: The logged in user's details (See example response below).

**Example Response**

    {
        "id": 12,
        "email": "email@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "role": "user",
        "status": "active",
        "created": 1464897863,
        "modified": 1465939139
    }

---

### POST http://your.trk.life.domain/api/settings/user/update ###

Update the currently logged in user. This requires the user's password to be sent in the request for extra security.

**Arguments (Form data)**

| Argument          | Required? | Allowed values | Description |
| ----------------- | --------- | -------------- | ----------- |
| current_password  | required  | *              | The user's current password, used for security reasons |
| email             | required  | *              | The user's email address |
| first_name        | required  | *              | The user's first name |
| last_name         | required  | *              | The user's last name |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    current_password: password2
    email: new_email@example.com
    first_name: Jimmy
    last_name: Doe

**Example Response**

    {
        "status": "success",
        "message": "Successfully updated user."
    }

---

### POST http://your.trk.life.domain/api/settings/user/change-password ###

Change the currently logged in user's password. This requires the user's password to be sent in the request for extra security.

**Arguments (Form data)**

| Argument          | Required? | Allowed values | Description |
| ----------------- | --------- | -------------- | ----------- |
| current_password  | required  | *              | The user's current password, used for security reasons |
| new_password      | required  | *              | The user's new password |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    current_password: password2
    new_password: password3

**Example Response**

    {
        "status": "success",
        "message": "Successfully changed password."
    }

---

### POST http://your.trk.life.domain/api/settings/user/delete ###

Delete the currently logged in user's account. This requires password re-entry, for security reasons. A user cannot 
delete themselves if they are the last admin user in the current installation's team. Once deleted the user is 
immediately logged out, and will be unable to log back in. When a user is deleted, all of their data
is removed from the database.

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Response**

    {
        "status": "success",
        "message": "Successfully deleted user."
    }

---

# Team management #

Resources for managing a team and it's users. These resources require user to have **admin** role.

### POST http://your.trk.life.domain/api/team/users/list ###

Get a list of all of the user's in the current trk.life installation's team.

**Returns**

A JSON array of user objects.

**Example Response**

    [
        {
            "id": 12,
            "email": "email@example.com",
            "first_name": "John",
            "last_name": "Doe",
            "role": "user",
            "status": "active",
            "created": 1464897863,
            "modified": 1465939139
        }
        ...
    ]

---

### POST http://your.trk.life.domain/api/team/users/{id}/get ###

Get a single user's details.

**Returns**

A JSON object of the user.

* user: The chosen user's details (See example response below).

**Example Response**

    {
        "id": 12,
        "email": "email@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "role": "user",
        "status": "active",
        "created": 1464897863,
        "modified": 1465939139
    }

---

### POST http://your.trk.life.domain/api/team/users/create ###

Create a new user in the team.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| email             | required  | *                    | The new user's email address |
| password          | required  | *                    | The new user's password |
| first_name        | required  | *                    | The new user's first name |
| last_name         | required  | *                    | The new user's last name |
| role              | required  | "admin", "user"      | The new user's role |
| status            | required  | "active", "disabled" | The new user's status |

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    email: new_user@example.com
    password: password1
    first_name: Mary
    last_name: Smith
    role: user
    status: active

**Example Response**

    {
        "status": "success",
        "message": "Successfully created new user."
    }

---

### POST http://your.trk.life.domain/api/team/users/{id}/update ###

Updates a user in the team. 

Note: A user cannot downgrade their own role from admin to user, or change their own status from active to disabled.

**Arguments (Form data)**

| Argument          | Required? | Allowed values       | Description |
| ----------------- | --------- | -------------------- | ----------- |
| email             | required  | *                    | The user's email address |
| password          | optional  | *                    | The user's password |
| first_name        | required  | *                    | The user's first name |
| last_name         | required  | *                    | The user's last name |
| role              | required  | "admin", "user"      | The user's role |
| status            | required  | "active", "disabled" | The user's status |

Note: The password field is optional. If a password change isn't wanted, the field should either be left blank or 
omitted completely.

**Returns**

Returns a JSON object containing these properties:

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Request**

    email: updated_user@example.com
    first_name: Mary
    last_name: Smith
    role: user
    status: active

**Example Response**

    {
        "status": "success",
        "message": "Successfully updated user."
    }

---

### POST http://your.trk.life.domain/api/team/users/{id}/delete ###

Delete a user from the team. The logged in user cannot delete themselves through team settings. If logged in, the 
deleted user will be logged out immediately and will be unable to log back in. When a user is deleted, all of their data
is removed from the database.

**Returns**

* status: Always returned, and contains either "fail" or "success".

* message: Always returned, containing a user-friendly message with the failure reason or a success message.

**Example Response**

    {
        "status": "success",
        "message": "Successfully deleted user."
    }

---

Next: [Managing Projects](03-managing-projects.md)

Previous: [API home](01-api.md)
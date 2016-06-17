Users API Resources
===================

[Go back to API home](01-api.md)

User related API resources.

### Auth related ###

##### POST http://your.trk.life.domain/api/users/login #####

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

##### GET http://your.trk.life.domain/api/users/logout #####

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

##### GET http://your.trk.life.domain/api/users/validate-token #####

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

##### POST http://your.trk.life.domain/api/users/forgotten-password #####

TODO

##### POST http://your.trk.life.domain/api/users/reset-password #####

TODO

### User settings ###

Resources for managing own user.

##### GET http://your.trk.life.domain/api/settings/user/get #####

TODO

##### POST http://your.trk.life.domain/api/settings/user/update #####

Note: requires password entry. Cannot downgrade own user's role unless there is another admin.

TODO

##### POST http://your.trk.life.domain/api/settings/user/change-password #####

Note: requires existing password entry

TODO

##### POST http://your.trk.life.domain/api/settings/user/delete #####

Note: requires password entry. Cannot delete own user if it is the last user (admin user).

TODO

### Team management ###

Resources for managing a team and it's users. These resources require user to have **admin** role.

##### POST http://your.trk.life.domain/api/team/users/list #####

TODO

##### POST http://your.trk.life.domain/api/team/users/{id}/get #####

TODO

##### POST http://your.trk.life.domain/api/team/users/create #####

TODO

##### POST http://your.trk.life.domain/api/team/users/{id}/update #####

TODO

##### POST http://your.trk.life.domain/api/team/users/{id}/delete #####

Note: cannot delete own user through team settings.

TODO

Next: [Managing Projects](03-managing-projects.md)

Previous: [API home](01-api.md)
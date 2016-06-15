HTTP API
========

This document contains available API resources for trk.life. These resources are used by both the web and desktop 
applications to persist tracking data and users. If a third party wants to integrate with a trk.life installation, they 
can also use these API resources to do so.

### Errors ###

trk.life uses HTTP response codes to indicate problems with API requests.

* **200: OK.** Everything worked, this should be expected from every call to the API.

* **401: Unauthorised.** A valid authentication token was not supplied in the request.

* **403: Forbidden.** The user doesn't have permission (Check user's role) to perform this action.

* **404: Not Found.** The resource doesn't exist.

* **405: Method Not Allowed.** The resource doesn't accept the request method (GET or POST).

### Authentication ###

Requests are authenticated with a token, which is passed in the **Authorization** header, for example:

    Authorization: Bearer 6653aed2427173ee9e4cb952b2ffefe7f11d0cf3d014c23f0aa1a8f17fa968a4

Tokens are obtained by a user logging in with the user login resource, giving an email address and password. The token 
is returned in the JSON response, more details of this are available below. Tokens are valid for 1 day by default, or 
90 days if the user requests to stay logged in, tokens will remain valid for an hour after it's last use if it goes past
this time.

### Resources ###

* [Users](02-users.md)

* [Managing Projects](03-managing-projects.md)

* [Tracking](04-tracking.md)

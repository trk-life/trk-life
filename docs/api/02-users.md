Users API Resources
===================

[Go back to API home](01-api.md)

User related API resources.

### Auth related ###

##### POST http://your.trk.life.domain/api/users/login #####

TODO

##### GET http://your.trk.life.domain/api/users/logout #####

TODO

##### GET http://your.trk.life.domain/api/users/validate-token #####

TODO

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
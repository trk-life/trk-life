window.trk = window.trk || {};
window.trk.angular = {
    routers: angular.module("trk.routers", ["ngRoute"]),
    controllers: angular.module("trk.controllers", []),
    services: angular.module("trk.services", []),
    filters: angular.module("trk.filters", []),
    directives: angular.module("trk.directives", [])
};

angular
    .module("trkApp", [
        "trk.routers",
        "trk.controllers",
        "trk.services",
        "trk.filters",
        "trk.directives",
        "ngRoute",
        "ngAnimate"
    ])
    .config(function() {
        // TODO -- Put routes here
        // TODO -- Redirect to login if not authed
    })
    .run(function() {
        // TODO
    });

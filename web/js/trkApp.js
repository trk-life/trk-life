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
    .config(function($routeProvider) {

        // TODO -- redirect users if they're not logged in

        $routeProvider
            .when("/error", {
                templateUrl: "partials/error.html"
            })
            .when("/login", {
                templateUrl: "partials/account/login.html",
                controller: "loginController",
                controllerAs: "vm"
            })
            .when("/logout", {
                templateUrl: "partials/account/logout.html",
                controller: "logoutController"
            });
    })
    .run(function() {
        // TODO
    });

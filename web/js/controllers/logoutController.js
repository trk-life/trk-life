trk.angular.controllers
    .controller("logoutController", function($scope, $location, User) {
        if (!User.isAuthenticated()) {
            $location.path("/login");
            return;
        }

        User.doLogout();
        $location.path("/login");
    });

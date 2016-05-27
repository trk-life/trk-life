trk.angular.controllers
    .controller("loginController", function($scope, $location, User) {
        if (User.isAuthenticated()) {
            $location.path("/");
            return;
        }

        var vm = this;
        $scope.page.title = "Sign In - trk.life";

        vm.login = doLogin;

        vm.loginData = {
            email: "",
            password: ""
        };

        function doLogin() {
            User.doLogin(vm.loginData.email, vm.loginData.password);
            // TODO -- on success, redirect
            // TODO -- on fail, show error
            console.info("submitted");
        }
    });

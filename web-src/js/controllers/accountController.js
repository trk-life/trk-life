trk.angular.controllers
    .controller("accountController", function($scope) {
        var vm = this;

        $scope.page.title = "Sign In - trk.life";

        vm.loginData = {
            email: "",
            password: ""
        };
    });

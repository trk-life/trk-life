trk.angular.controllers
    .controller("welcomeController", function($scope, User) {
        var vm = this;

        $scope.page.title = "Overview - trk.life";

        vm.user = User;
        vm.weekHours = 5;
    });

trk.angular.services
    .factory("User", function() {
        var factory = {
            isAuthenticated: isAuthenticated,
            doLogin: doLogin
        };

        return factory;

        ///////////////////////////////

        function isAuthenticated() {
            // TODO -- Call API
            return false;
        }

        function doLogin() {
            // TODO -- Call API
        }
    });

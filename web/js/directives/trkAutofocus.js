/**
 * Use this directive instead of HTML5 autofocus
 * to ensure auto-focusing of dynamic content
 */

trk.angular.directives
    .directive("trkAutofocus", ["$timeout", function($timeout) {
        return {
            restrict: "A",
            link: function(scope, element) {
                $timeout(function () {
                    element[0].focus();
                });
            }
        }
    }]);

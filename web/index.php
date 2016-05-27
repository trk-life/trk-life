<!DOCTYPE HTML>
<html lang="en" ng-app="trk-app" ng-controller="trkAppController as app">
    <head>
        <title ng-bind="Page.title()">Loading...</title>
        <!-- load CSS here -->
    </head>
    <body>
        <header>
            <ng-include src="'partials/header.html'" class="nav" />
        </header>

        <ng-view></ng-view>

        <footer>
            <ng-include src="'partials/footer.html'" />
        </footer>

<?php include "include_js.php" ?>
    </body>
</html>

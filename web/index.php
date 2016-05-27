<?php
session_start();
?><!DOCTYPE HTML>
<html lang="en" ng-app="trkApp" ng-controller="trkAppController as app">
    <head>
        <title ng-bind="page.title">Loading...</title>

        <link rel="stylesheet" href="css/lib/bootstrap.css" />
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

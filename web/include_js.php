<?php

class JsIncludes
{
    private static $jsLibrary = array(
        // 3rd Party
        "lib/angular1.5.5/angular.js",
        "lib/angular1.5.5/angular-route.js",
        "lib/angular1.5.5/angular-animate.js",

        "js/trkApp.js",

        // Controllers
        "js/controllers/trkAppController.js",

        // Directives

        // Services
        "js/services/Globals.js",
        "js/services/Page.js"
    );

    public static function render()
    {
        foreach (self::$jsLibrary as $file) {
            echo "        <script type='text/javascript' src='{$file}'></script>\n";
        }
    }
}

JsIncludes::render();

var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    minifyCSS = require('gulp-clean-css'),
    addsrc = require('gulp-add-src');

/**
 * Minify and combine JS files, including jQuery and Bootstrap
 */
gulp.task('scripts', function() {
    gulp.src([
            'node_modules/angular/angular.js',
            'node_modules/angular-route/angular-route.js',
            'node_modules/angular-animate/angular-animate.js',
            'node_modules/jquery/dist/jquery.js',
            'node_modules/bootstrap/dist/js/bootstrap.js',
            'web-src/js/**/*.js'
        ])
        // NOTE disable uglify as it breaks angular
        //.pipe(uglify())
        .pipe(concat('script.js'))
        .pipe(gulp.dest('web/dist/js'));
});

/**
 * Build SASS, combine with Bootstrap CSS and minify
 */
gulp.task('styles', function() {
    gulp.src([
            'web-src/css/main.css'
        ])
        .pipe(addsrc.prepend('node_modules/bootstrap/dist/css/bootstrap.css'))
        .pipe(addsrc.prepend('node_modules/font-awesome/css/font-awesome.css'))
        .pipe(minifyCSS())
        .pipe(concat('style.css'))
        .pipe(gulp.dest('web/dist/css'));
});

/**
 * Move bootstrap and project font files into dist
 */
gulp.task('fonts', function() {
    gulp.src([
            'node_modules/bootstrap/dist/fonts/*',
            'node_modules/font-awesome/fonts/*',
            'web-src/fonts/*'
        ])
        .pipe(gulp.dest('web/dist/fonts'));
});

/**
 * The default gulp task
 */
gulp.task('default', function() {
    gulp.run('scripts', 'styles', 'fonts');
});

/**
 * Watch asset files for changes. First runs default to prevent annoying issues.
 */
gulp.task('watch', function() {
    gulp.run('default');

    gulp.watch('web-src/css/**/*.css', function(event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        gulp.run('styles');
    });

    gulp.watch('web-src/js/**/*.js', function(event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        gulp.run('scripts');
    });
});

'use strict';

var gulp         = require('gulp'),
    sass         = require('gulp-ruby-sass'),
    rename       = require("gulp-rename"),
    uglify       = require('gulp-uglify'),
    concat       = require('gulp-concat'),
//browserSync  = require('browser-sync').create(),
    sourcemaps   = require('gulp-sourcemaps'),
    clean        = require('gulp-clean'),
    autoprefixer = require('gulp-autoprefixer'),
    include      = require('gulp-include');

/**
 * Static Server + watching scss/html files
 */
gulp.task('serve', ['sass', 'scripts'], function() {

    gulp.watch("./themes/muster/sass/*.scss", ['sass']);
    gulp.watch("./themes/muster/sass/partials/*.scss", ['sass']);
    gulp.watch("./themes/muster/js/lib/*.js", ['scripts']);
    gulp.watch("./themes/muster/js/vendor/*.js", ['scripts']);
});

/**
 * Compile with gulp-ruby-sass + source maps
 */
gulp.task('sass', function () {
    return sass('./themes/muster/sass/main.scss', {sourcemap: true})
        .on('error', function (err) {
            console.error('Error!', err.message);
        })
        .pipe(sourcemaps.write('./', {
            includeContent: false,
            sourceRoot: './themes/muster/sass'
        }))
        .pipe(gulp.dest('./themes/muster/css'));
});

gulp.task('scripts', function() {
    gulp.src([
        './themes/muster/js/vendor/jquery.js',
        './themes/muster/js/vendor/*.js',
        './themes/muster/js/lib/*.js'
    ])
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./themes/muster/js'))
});

gulp.task('deploy', ['sass', 'scripts']);

gulp.task('default', ['serve']);
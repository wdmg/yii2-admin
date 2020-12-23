const gulp = require('gulp');
const cleaner = require('gulp-clean');
const gulpSass = require('gulp-sass');
const jsConcat = require('gulp-concat');
const jsUglify = require('gulp-terser');
const cleanCSS = require('gulp-clean-css');
const beautify = require('gulp-beautify');
const rename = require('gulp-rename');
const jsInclude = require('gulp-include');
const cssExtend = require('gulp-autoprefixer');
const sourceMaps = require('gulp-sourcemaps');

function jquery() {
    return gulp.src([
            'node_modules/jquery/dist/jquery.js'
        ]).pipe(gulp.dest('assets/js/'));
}

function bootstrap() {
    return gulp.src([
            'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'
        ]).pipe(gulp.dest('assets/js/')) &&
        gulp.src([
            'node_modules/bootstrap-sass/assets/fonts/bootstrap/*'
        ]).pipe(gulp.dest('assets/fonts/glyphicons/'));
}

function helpers() {
    return gulp.src([
            'node_modules/jquery-helper/dist/jquery.helper.js',
            'node_modules/jquery-helper/dist/jquery.touch.js'
        ]).pipe(jsConcat('helper.js')).pipe(gulp.dest('assets/js/'));
}

function sticky_sidebar() {
    return gulp.src([
            'node_modules/sticky-sidebar/dist/jquery.sticky-sidebar.js'
        ]).pipe(rename('sticky-sidebar.js')).pipe(gulp.dest('assets/js/'));
}

function roboto_fontface() {
    return gulp.src([
            'node_modules/roboto-fontface/fonts/**/*',
        ]).pipe(gulp.dest('assets/fonts'));
}

function copy() {
    return jquery() &&
        bootstrap() &&
        helpers() &&
        sticky_sidebar() &&
        roboto_fontface();
}

function cleanup() {
    return gulp.src([
            'assets/*'
        ], {
            read: false
        }).pipe(cleaner());
}


function js_minify() {
    return gulp.src(['assets/js/*.js', '!assets/js/*.min.js'])
        .pipe(sourceMaps.init())
        .pipe(jsUglify())
        .pipe(sourceMaps.write())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/js'));
}

function css_minify() {
    return gulp.src(['assets/css/*.css', '!assets/css/*.min.css'])
        .pipe(sourceMaps.init())
        .pipe(cleanCSS({debug: true}, (details) => {
            console.log(`${details.name}: ${details.stats.originalSize}`);
            console.log(`${details.name}: ${details.stats.minifiedSize}`);
        }))
        .pipe(sourceMaps.write())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css'));
}

function minify() {
    return js_minify() && css_minify();
}

function js() {
    return gulp.src('src/js/*.js')
        .pipe(sourceMaps.init())
        .pipe(jsInclude({
            extensions: 'js',
            hardFail: true,
            separateInputs: true
        }))
        .on('error', console.log)
        .pipe(beautify.js({
            indent_size: 2
        }))
        .pipe(jsConcat('admin.js'))
        .pipe(sourceMaps.write())
        .pipe(gulp.dest('assets/js'));
}

function sass() {
    return gulp.src('src/scss/*.scss')
        .pipe(sourceMaps.init())
        .pipe(gulpSass({
            includePaths: ['node_modules']
        }).on('error', gulpSass.logError))
        .pipe(cssExtend({
            cascade: false
        }))
        .pipe(beautify.css({
            indent_size: 2
        }))
        .pipe(sourceMaps.write())
        .pipe(gulp.dest('assets/css'));
}

function images() {
    return gulp.src('src/images/*').pipe(gulp.dest('assets/images')) &&
        gulp.src(['src/favicon.png', 'src/favicon.ico']).pipe(gulp.dest('assets'));
}

function watchFiles() {
    gulp.watch('src/js/**/*.js', gulp.series(js, js_minify));
    gulp.watch('src/scss/**/*.scss', gulp.series(sass, css_minify));
    gulp.watch('src/images/**/*.*', gulp.series(images));
    return;
}

exports.js = js;
exports.sass = sass;
exports.cleanup = cleanup;
exports.js_minify = js_minify;
exports.css_minify = css_minify;
exports.minify = gulp.parallel(js_minify, css_minify);
exports.watch = gulp.parallel(sass, js, watchFiles);
exports.copy = gulp.parallel(jquery, bootstrap, helpers, sticky_sidebar, roboto_fontface);
exports.default = gulp.series(cleanup, copy, images, js, sass, minify, watchFiles);
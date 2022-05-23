const gulp    = require('gulp');
const postcss = require('gulp-postcss');
const ts      = require('gulp-typescript');
const sass    = require('gulp-sass');
const cssnano = require('cssnano');
const wpPot   = require('gulp-wp-pot');
const minify  = require('gulp-minify');

function watch() {
	gulp.watch('./assets/src/scss/*.scss', style);
	gulp.watch('./assets/src/js/*.ts', tsc);
	gulp.watch('./*.php', wp_pot);
	gulp.watch('./includes/*.php', wp_pot);
}

function style() {
	let plugins = [
        cssnano()
    ];

	// location of style
	return gulp.src('./assets/src/scss/*.scss')
	// Compile file
	.pipe(sass())
	// Use postcss
	.pipe(postcss(plugins))
	// Push build
	.pipe(gulp.dest('./assets/build/css'))
}

var tsProject = ts.createProject({
    declaration: false // if you need the *.d.ts files
});

function tsc() {
    return gulp.src('./assets/src/js/*.ts')
        .pipe(tsProject())
        .pipe(minify({
            noSource: true,
            ext:{
                min:'.js'
            }
        }))
        .pipe(gulp.dest('./assets/build/js'));
}
 
function wp_pot() {
    return gulp.src('./**/**/*.php')
        .pipe(wpPot( {
            domain: 'cleanup-action-scheduler',
            package: 'Cleanup_Action_Scheduler'
        } ))
        .pipe(gulp.dest('./languages/en_GB.pot'));
}

exports.style  = style;
exports.watch  = watch;
exports.tsc    = tsc;
exports.wp_pot = wp_pot;
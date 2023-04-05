const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');
const gulpIf = require('gulp-if');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const notify = require('gulp-notify');
const plumber = require('gulp-plumber');
const combiner = require('stream-combiner2').obj;
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');


const isDevelopment = false;
// const isDevelopment = !process.env.NODE_ENV || process.env.NODE_ENV === 'development';

// convert sass to css and compress to prod
gulp.task('styles', () => gulp.src('assets/scss/style.scss')
  .pipe(plumber({
    errorHandler: notify.onError(err => ({
      title: 'STYLES',
      message: err.message,
    })),
  }))
  .pipe(gulpIf(isDevelopment, sourcemaps.init()))
  .pipe(autoprefixer())
  .pipe(sass())
  .pipe(gulpIf(isDevelopment, sourcemaps.write('.')))
  .pipe(gulpIf(!isDevelopment, combiner(
    cleanCSS(),
    concat('style.min.css'),
  )))
  .pipe(gulp.dest('./assets/css')));

// compress js
gulp.task('js', () => gulp.src(['js/*.js', '!js/*.min.js'])
  .pipe(uglify())
  .pipe(rename({ suffix: '.min' }))
  .pipe(gulp.dest('js')));

// sass compile
gulp.task('watch', () => {
  gulp.watch('assets/scss/**/*.*', gulp.series('styles'));
});

// development
gulp.task('dev', gulp.series(
  'styles',
  gulp.parallel('watch'),
));

// production
gulp.task('prod', gulp.series(
  'styles',
  'js',
));

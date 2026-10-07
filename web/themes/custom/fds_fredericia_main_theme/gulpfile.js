// Configurations
let compileConfig = {
  settings: require('./src/compile-settings.json')
};

let gulpConfig = {
  settings: require('./src/gulp-settings.json')
};


const { src, dest, series, watch } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const csso = require('gulp-csso');
const include = require('gulp-file-include');
const htmlmin = require('gulp-htmlmin');
const del = require('del');
const concat = require('gulp-concat');
const autoprefixer = require('gulp-autoprefixer');
const sync = require('browser-sync').create();



function html() {
  return src('src/**.html')
    .pipe(
      include({
        prefix: '@@',
      })
    )
    .pipe(
      htmlmin({
        collapseWhitespace: true,
      })
    )
    .pipe(dest('dist'));
}

function scss() {
  return src(compileConfig.settings.styles)
    .pipe(sass())
    .pipe(
      autoprefixer({
        overrideBrowserslist: ['last 2 versions'],
      })
    )
    // .pipe(csso())
    .pipe(concat('stylesheet.css'))
    .pipe(dest('dist/stylesheets'));
}




function fonts() {
  return src(compileConfig.settings.fonts).pipe(dest('dist/fonts'));
}
function img() {
  return src('src/images/**/*').pipe(dest('dist/images'));
}

function js() {
  return src(compileConfig.settings.javascripts)
    .pipe(include())
    .pipe(concat('app.js'))
    .pipe(dest('dist/javascripts'))
    .pipe(sync.stream());
}

function clear() {
  return del('dist');
}



function serve() {
  sync.init({
    proxy: gulpConfig.settings.options.proxy
  });

  watch('**/*.+(twig|twig.html|tpl|tpl.php|html)', series(html)).on('change', sync.reload);
  watch('src/styles/**/**.scss', series(scss)).on('change', sync.reload);
  watch('src/styles/css/**/**.css', series(scss)).on('change', sync.reload);
  watch('src/images/**.**', series(img)).on('change', sync.reload);
  watch('src/javascripts/**/*.js', series(js)).on('change', sync.reload);
}

exports.build = series(clear, scss, html, js, img, fonts);
exports.serve = series(clear, scss, html, js, img, fonts, serve);
exports.clear = clear;

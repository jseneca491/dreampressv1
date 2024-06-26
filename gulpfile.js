(() => {

  'use strict';

  const
    
    /** Dependencies **/
    doesNotMatch = require('assert'),
    endianness = require('os'),
    exit = require('process'),
    gulp = require( 'gulp' ),
    fs = require('fs'),
    rimraf = require('rimraf'),
    async = require( 'async' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    browserSync  = require( 'browser-sync' ).create(),
    reload  = browserSync.reload,
    sass  = require('gulp-sass')(require('sass')),
    cleanCSS  = require( 'gulp-clean-css' ),
    sourcemaps  = require( 'gulp-sourcemaps' ),
    concat  = require( 'gulp-concat' ),
    changed = require( 'gulp-changed' ),
    uglify  = require( 'gulp-uglify' ),
    lineec  = require( 'gulp-line-ending-corrector' ),
    { exec } = require('child_process'),
    path = require('path'),

    /** Directories **/
    currentDirectory = process.cwd().replace(/\\/g, '/') + '/',
    themename = 'divi-child',
    root  = currentDirectory + 'wp-content/themes/' + themename + '/',
    scss  = root + 'sass/',
    js  = root + 'js/',
    vendor = currentDirectory + 'node_modules/',
    libs = root + 'libraries/',
    dist = root + 'dist/',
    themeCSS  = root + 'dist/css/',
    jsdist  = root + 'dist/js/',
    libCSS  = root + 'libraries/css/',
    libJS  = root + 'libraries/js/',
    imgSRC = root + 'images/*',
    imgDEST = root + 'dist/images/',

    /** Javascript Library Addons **/
    jsSRC = [
      libJS + 'glightbox/glightbox.min.js',
      libJS + 'swiper/swiper-bundle.js',
      // libJS + 'jquery-match-height/jquery.matchHeight-min.js',
      // libJS + 'rellax/rellax.min.js',
      // libJS + 'parallax-js/parallax.min.js',
      js + 'custom.js',
    ],

    /** Styling Library Addons **/
    cssSRC =  [
      //libCSS + 'font-awesome/font-awesome.min.css',
      //libCSS + 'flexboxgrid/flexboxgrid.min.css',
      libCSS + 'glightbox/glightbox.min.css',
      libCSS + 'swiper/swiper-bundle.css',
      libCSS + 'style.css',
    ],

    /** Watch Files **/
    PHPWatchFiles  = root + '**/*.php',
    libsWatchFiles  = root + 'libraries/sass/**/*.scss',
    styleWatchFiles  = root + '**/*.scss',
    

    /** Browser Sync Config **/
    browserSyncConfig = {
      open: 'local',
      proxy: 'domain.local/',
      port: 8080,
    };

    console.log(vendor);

    /** Debugging Node **/
    // fs.access(libsWatchFiles, (err) => {
    //   if (err) {
    //       console.log(err.message);
    //       console.log(err.code);
    //   }
    // });

    function migrate(cb) {
      async.series([
          // function (callback){
          //   gulp.src(vendor + 'font-awesome/fonts/**/*.{ttf,woff,woff2,eof,svg}')
          // .pipe(gulp.dest(dist + 'fonts/').on('end', callback));
          // },
          // function (callback){
          //   gulp.src(vendor + 'font-awesome/css/font-awesome.min.css')
          // .pipe(gulp.dest(libCSS + 'font-awesome/').on('end', callback));
          // },
          function (callback){
            gulp.src(root + 'style.css')
          .pipe(gulp.dest(libCSS).on('end', callback));
          },
          function (callback){
            gulp.src(vendor + 'glightbox/dist/css/glightbox.min.css')
          .pipe(gulp.dest(libCSS + '/glightbox').on('end', callback));
          },
          function (callback){
            gulp.src(vendor + 'glightbox/dist/js/glightbox.min.js')
          .pipe(gulp.dest(libJS + '/glightbox').on('end', callback));
          },
          function (callback){
            gulp.src(vendor + 'swiper/swiper-bundle.css')
          .pipe(gulp.dest(libCSS + '/swiper').on('end', callback));
          },
          function (callback){
            gulp.src(vendor + 'swiper/swiper-bundle.js')
          .pipe(gulp.dest(libJS + '/swiper').on('end', callback));
          },
          function (callback){
            gulp.src(vendor + 'flexboxgrid/dist/flexboxgrid.min.css')
          .pipe(gulp.dest(libCSS + '/flexboxgrid').on('end', callback));
          },
          // function (callback){
          //   gulp.src(vendor + 'jquery-match-height/dist/jquery.matchHeight-min.js')
          // .pipe(gulp.dest(libJS + '/jquery-match-height').on('end', callback));
          // },
          // function (callback){
          //   gulp.src(vendor + 'rellax/rellax.min.js')
          // .pipe(gulp.dest(libJS + '/rellax').on('end', callback));
          // },
          // function (callback){
          //   gulp.src(vendor + 'parallax-js/dist/parallax.min.js')
          // .pipe(gulp.dest(libJS + '/parallax-js').on('end', callback));
          // }
        ],function (err, values) {
          if (err) {
            cb('Error while importing files to libraries');
          }else{
            cb();
        }
      });
    }
    exports.migrate = gulp.series(migrate);

    //Compiles SASS Files into CSS
    function compileScss(cb) {
      return gulp.src(scss + 'style.scss', {allowEmpty: true}).pipe(sourcemaps.init({loadMaps: true}))
      .pipe(sass({
        outputStyle: 'expanded'
      }).on('error', sass.logError))
      .pipe(autoprefixer('last 2 versions'))
      .pipe(sourcemaps.write('./maps/'))
      .pipe(lineec())
      .pipe(gulp.dest(libCSS));
      cb();
    }
    exports.compileScss = gulp.series(compileScss);

    //Minifies the CSS file from compileScss and CSS Libraries into theme-style.min.css
    function compileCss(cb) {
      return gulp.src(cssSRC)
      .pipe(sourcemaps.init({loadMaps: true, largeFile: true}))
      .pipe(concat('theme-style.min.css'))
      .pipe(cleanCSS())
      .pipe(sourcemaps.write('./maps/'))
      .pipe(lineec())
      .pipe(gulp.dest(themeCSS));
      cb();
    }
    exports.compileCss = gulp.series(compileCss);

    //Minifies the Javascript files from custom.script and JS Libraries into theme.min.js
    function javascript() {
      return gulp.src(jsSRC)
      .pipe(concat('theme.min.js'))
      .pipe(uglify())
      .pipe(lineec())
      .pipe(gulp.dest(jsdist));
    }
    exports.javascript = gulp.series(javascript);

    //Watches for any saved files then compiles it and update the theme-style.min.css and theme.min.js automatically
    function watch() {
      gulp.watch(styleWatchFiles, gulp.series([compileScss,compileCss]));
      gulp.watch(jsSRC, javascript);
      gulp.watch([PHPWatchFiles, jsdist + 'theme.min.js', themeCSS + 'theme-style.min.css']);
      console.clear();
      console.log('\n\x1b[32m%s\x1b[0m', '"Any fool can write code that a computer can understand. Good programmers write code that humans can understand."\n– Martin Fowler\n\nDone compiling!\nNote: Once you save your JS and SASS File it will automatically be compiled and minified.');
      //console.log('\n\x1b[32m%s\x1b[0m', 'Note: Once you save your JS and SASS File it will automatically be compiled and minified.');
    }

    //Same as watch functionality but has a browsersync feature
    function watch_sync() {
      browserSync.init(browserSyncConfig);    
      gulp.watch(styleWatchFiles, gulp.series([compileScss,compileCss]));
      gulp.watch(jsSRC, javascript);
      gulp.watch([PHPWatchFiles, jsdist + 'theme.min.js', themeCSS + 'theme-style.min.css']).on('change', browserSync.reload);
    }
    exports.sync = gulp.series(watch_sync);

    //Restarts Gulp to remove unknown bugs along the compiling
    function restart(cb){
      rimraf( vendor, cb );
      rimraf( libs, cb );
      rimraf( root + '/node_modules/', cb );
      rimraf( root + '/package-lock.json', cb );
      cb();
      console.log('\n\x1b[32m%s\x1b[0m', 'Files have been successfully deleted!');
      exit;
    }
    exports.restart = gulp.series(restart);

    //Removes unnecessary files for production before sending it to Live or Staging.
    function release(cb){
      rimraf( root + '/package-lock.json', cb );
      rimraf( root + '/.gitignore', cb );
      rimraf( root + '/.npmignore', cb );
      rimraf( root + '/package.json', cb );
      rimraf( root + '/gulpconfig.json', cb );
      rimraf( root + '/gulpfile.js', cb );
      rimraf( root + '/node_modules/', cb );
      rimraf( root + '/libraries/', cb );
      cb();
      console.log('\n\x1b[32m%s\x1b[0m', 'Theme is now ready for production!');
      exit;
    }
    exports.release = gulp.series(release);


    //Removes unnecessary files for production before sending it to Live or Staging.
    function production(cb){
      rimraf( root + '/package-lock.json', cb );
      rimraf( root + '/node_modules/', cb );
      rimraf( root + '/libraries/*', cb );
      rimraf( root + '/dist/*', cb );
      cb();
      console.log('\n\x1b[32m%s\x1b[0m', 'Theme is now ready for production!');
      exit;
    }
    exports.production = gulp.series(production);

    //Launch all commands and functionality in this logical order to create a wonderful tasty dessert!.
    exports.start = gulp.series(migrate,exports.compileScss, exports.compileCss, exports.javascript, watch);

})();

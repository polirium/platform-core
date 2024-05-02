let mix = require('laravel-mix');
let path = require('path');
let directory = path.basename(path.resolve(__dirname));

//path to folder with compiled assets
const source = 'platform/core/' + directory;
const assets = source + '/resources/assets';
const public = source + '/public';

const productFolder = 'public/vendor/polirium/core/' + directory;

mix.disableNotifications();

const cssFiles = [
    'polirium-core',
    'polirium-flags',
    'polirium-payments',
    'polirium-vendors',
    'app',
];

const jsFiles = [
    'polirium',
    'polirium.esm',
    'theme',
    'app',
];

cssFiles.forEach(function (file) {
    mix.sass(assets + '/scss/' + file + '.scss', productFolder + '/css/' + file + '.min.css');
});

jsFiles.forEach(function (file) {
    mix.js(assets + '/js/' + file + '.js', productFolder + '/js/' + file + '.min.js');
});


// cssFiles.forEach(function (file) {
//     mix.copy(productFolder + '/css/' + file + '.min.css', public + '/css/' + file + '.min.css');
// });

// jsFiles.forEach(function (file) {
//     mix.copy(productFolder + '/js/' + file + '.min.js', public + '/js/' + file + '.min.js');
// });


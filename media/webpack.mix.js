let mix = require('laravel-mix');
let path = require('path');

// Get the relative path from root to this directory
const rootPath = path.resolve(__dirname, '../../..');
const relativePath = path.relative(rootPath, __dirname);

// Use __dirname to get correct paths regardless of where mix is run from
const baseDir = path.resolve(__dirname);
const assets = path.join(baseDir, 'resources/assets');
const publicPath = relativePath + '/public/vendor/polirium/core/media';

mix.disableNotifications();

// Build SCSS
mix.sass(path.join(assets, 'scss/media-manager.scss'), publicPath + '/css');

// Build JS
mix.js(path.join(assets, 'js/media-manager.js'), publicPath + '/js');

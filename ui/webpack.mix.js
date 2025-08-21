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
    'polirium-marketing',
    'polirium-payments',
    'polirium-props',
    'polirium-social',
    'polirium-themes',
    'polirium-vendors',
    'app',
];

const jsFiles = [
    'polirium',
    'theme',
    'app',
];

cssFiles.forEach(function (file) {
    mix.sass(assets + '/scss/' + file + '.scss', productFolder + '/css/' + file + '.min.css');
});

jsFiles.forEach(function (file) {
    mix.js(assets + '/js/' + file + '.js', productFolder + '/js/' + file + '.min.js');
});

mix.copyDirectory(assets + '/libs', productFolder + '/libs');

// Copy files after they are built - using then() to ensure proper order
mix.then(() => {
    cssFiles.forEach(function (file) {
        const fs = require('fs');
        const path = require('path');
        const sourceFile = productFolder + '/css/' + file + '.min.css';
        const targetFile = public + '/css/' + file + '.min.css';

        if (fs.existsSync(sourceFile)) {
            // Ensure target directory exists
            const targetDir = path.dirname(targetFile);
            if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true });
            }
            fs.copyFileSync(sourceFile, targetFile);
        }
    });

    jsFiles.forEach(function (file) {
        const fs = require('fs');
        const path = require('path');
        const sourceFile = productFolder + '/js/' + file + '.min.js';
        const targetFile = public + '/js/' + file + '.min.js';

        if (fs.existsSync(sourceFile)) {
            // Ensure target directory exists
            const targetDir = path.dirname(targetFile);
            if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true });
            }
            fs.copyFileSync(sourceFile, targetFile);
        }
    });
});

mix.copyDirectory(assets + '/libs', public + '/libs');

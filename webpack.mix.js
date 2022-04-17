let mix = require('laravel-mix')
require('./mix.js');

mix.setPublicPath('dist')
    .js('resources/js/tool.js', 'js')
    .vue({ version: 3 })
    .nova('pktharindu/nova-permissions')

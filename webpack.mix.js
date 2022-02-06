let mix = require('laravel-mix')

mix.setPublicPath('dist')
   .vue({ version: 2 })
   .js('resources/js/tool.js', 'js')

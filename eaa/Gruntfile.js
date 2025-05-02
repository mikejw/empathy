
const yaml = require('js-yaml');
const fs = require('fs');
const touch = require("touch")


module.exports = function(grunt) {
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  grunt.registerTask('common', 'Generate common.js.', function() {
    var config = yaml.safeLoad(fs.readFileSync('config.yml', 'utf8'));
    var file = './public_html/js/common.js';
    touch(file, function() {
      var public_dir = config.public_dir || '';
      var output = '\n' +
        'var WEB_ROOT   = "' + config.web_root + "\";\n" +
        'var PUBLIC_DIR = "' + public_dir + "\";\n";
      fs.writeFileSync(file, output);
    });
  });

  grunt.initConfig({

    node: './node_modules',
    dest: './public_html/vendor',
    destfaf: './public_html/vendor/fonts',
    elibdir: './vendor/mikejw/elib-base',

    copy: {
      main: {
        files: [
          {
            expand: true,
            flatten: true,
            src: ['<%= node %>/@fortawesome/fontawesome-free/webfonts/fa-*'],
            dest: '<%= destfaf %>/',
            filter: 'isFile'
          },
          {
            expand: true,
            cwd: '<%= node %>/tinymce/icons/',
            src: [ "*.*", "**/*.*" ],
            dest: '<%= dest %>/js/icons'
          },
          {
            expand: true,
            cwd: '<%= node %>/tinymce/plugins/',
            src: [ "*.*", "**/*.*" ],
            dest: '<%= dest %>/js/plugins'
          },
          {
            expand: true,
            cwd: '<%= node %>/tinymce/themes/',
            src: [ "*.*", "**/*.*" ],
            dest: '<%= dest %>/js/themes'
          },
          {
            expand: true,
            cwd: '<%= node %>/tinymce/skins/',
            src: [ "*.*", "**/*.*" ],
            dest: '<%= dest %>/js/skins'
          },
          {
            expand: true,
            cwd: '<%= node %>/tinymce/models/',
            src: [ "*.*", "**/*.*" ],
            dest: '<%= dest %>/js/models'
          },
          {
            expand: true,
            cwd: '<%= elibdir %>/public/',
            src: [ "blogImages.js" ],
            dest: '<%= dest %>/js'
          }
        ]
      }
    },
    concat: {
      js: {
        files: {
          '<%= dest %>/js/main.js': [
            '<%= node %>/jquery/dist/jquery.min.js',
            '<%= node %>/bootstrap/dist/js/bootstrap.bundle.min.js',
            '<%= node %>/jquery-ui/ui/data.js',
            '<%= node %>/jquery-ui/ui/scroll-parent.js',
            '<%= node %>/jquery-ui/ui/widget.js',
            '<%= node %>/jquery-ui/ui/widgets/mouse.js',
            '<%= node %>/jquery-ui/ui/widgets/sortable.js',
            '<%= node %>/tinymce/tinymce.min.js',
            '<%= node %>/tinymce/jquery.tinymce.min.js',
            '<%= elibdir %>/public/admin.js'
          ]
        }
      }
    },
    uglify: {
      build: {
        files: {
          '<%= dest %>/js/main.min.js': [ '<%= dest %>/js/main.js' ]
        }
      }
    },

    sass: {
      dist: {
        files: [{
          expand: true,
          cwd: './public_html/css/scss/',
          src: ['**/*.scss'],
          dest: '<%= dest %>/css',
          ext: '.css'
        }]
      }
    },

    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: '<%= dest %>/css',
          src: ['*.css', '!*.min.css'],
          dest: '<%= dest %>/css',
          ext: '.min.css'
        }]
      }
    }
  });

  grunt.registerTask('def', [
    'common',
    'concat:js',
    'copy',
    'uglify',
    'sass',
    'cssmin'
  ]);

  grunt.registerTask('default', ['def']);
};

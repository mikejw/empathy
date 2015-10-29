// The MIT License (MIT)

// Copyright (c) 2015 Mike Whiting

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.


module.exports = function(grunt) {
    
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.initConfig({

        node: './node_modules',
        dest: './public_html/vendor',
        destfaf: './public_html/fonts',

        copy: {
            main: {
                files: [                    
                    {
                        expand: true,
                        flatten: true,
                        src: ['<%= node %>/font-awesome/fonts/fontawesome-webfont.*'],
                        dest: '<%= destfaf %>/', 
                        filter: 'isFile'
                    },
                    {
                        expand: true,
                        flatten: true,
                        src: ['<%= node %>/bootstrap/fonts/glyphicons-halflings-regular.*'],
                        dest: '<%= destfaf %>/', 
                        filter: 'isFile'
                    }
                ]
            }
        },
        concat: {           
            css: {
                files: {
                    '<%= dest %>/css.css': [
                        '<%= node %>/bootstrap/dist/css/bootstrap.min.css',
                        '<%= node %>/font-awesome/css/font-awesome.min.css'
                    ]
                }
            },
            js: {
                files: {
                    '<%= dest %>/js.js': [
                        '<%= node %>/jquery/dist/jquery.min.js',
                        '<%= node %>/bootstrap/dist/js/bootstrap.min.js',
                        '<%= node %>/less/dist/less.min.js'
                    ]
                }
            }
        },
        uglify: {
            build: {
                files: {
                    '<%= dest %>/js.min.js': [ '<%= dest %>/js.js' ]
                }
            }
        }
    });

    grunt.registerTask('def', [
        'concat',
        'copy',
        'uglify'
    ]);

    grunt.registerTask('default', ['def']);
};

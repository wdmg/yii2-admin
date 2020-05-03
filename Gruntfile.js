/*!
 * Main gruntfile for Butterfly.CMS assets
 * Homepage: https://wdmg.com.ua/
 * Author: Vyshnyvetskyy Alexsander (alex.vyshyvetskyy@gmail.com)
 * Copyright 2019 W.D.M.Group, Ukraine
 * Licensed under MIT
*/

module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            jquery: {
                src: [
                    'node_modules/jquery/dist/jquery.js'
                ],
                dest: 'assets/js/jquery.js'
            },
            bootstrap: {
                src: [
                    'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'
                ],
                dest: 'assets/js/bootstrap.js'
            },
            helper: {
                src: [
                    'node_modules/jquery-helper/dist/jquery.helper.js',
                    'node_modules/jquery-helper/dist/jquery.touch.js'
                ],
                dest: 'assets/js/helper.js'
            },
            sticky: {
                src: [
                    'node_modules/sticky-sidebar/dist/jquery.sticky-sidebar.js'
                ],
                dest: 'assets/js/sticky-sidebar.js'
            }
        },
        'string-replace': {
            inline: {
                files: {
                    'assets/js/sticky-sidebar.js': 'assets/js/sticky-sidebar.js'
                },
                options: {
                    replacements: [
                        {
                            pattern: 'sourceMappingURL=jquery.sticky-sidebar.js.map',
                            replacement: 'sourceMappingURL=sticky-sidebar.js.map'
                        }
                    ]
                }
            }
        },
        copy: {
            bootstrap: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/bootstrap-sass/assets/stylesheets/bootstrap',
                        src: ['**'],
                        dest: 'assets/scss/bootstrap',
                        filter: 'isFile'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/bootstrap-sass/assets/stylesheets/bootstrap',
                        src: 'mixins/*.scss',
                        dest: 'assets/scss/bootstrap',
                        filter: 'isFile'
                    }
                ]
            },
            glyphicons: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/bootstrap-sass/assets/fonts/bootstrap',
                        src: ['**'],
                        dest: 'assets/fonts/glyphicons',
                        filter: 'isFile'
                    }
                ]
            },
            robotoFontface: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/roboto-fontface/css/',
                        src: '*.scss',
                        dest: 'assets/scss/roboto-fontface',
                        filter: 'isFile'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/roboto-fontface/fonts',
                        src: ['**'],
                        dest: 'assets/fonts',
                        filter: 'isFile'
                    }
                ]
            }
        },
        uglify: {
            jquery: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/jquery.js.map'
                },
                files: {
                    'assets/js/jquery.min.js': ['assets/js/jquery.js']
                }
            },
            bootstrap: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/bootstrap.js.map'
                },
                files: {
                    'assets/js/bootstrap.min.js': ['assets/js/bootstrap.js']
                }
            },
            helper: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/helper.js.map'
                },
                files: {
                    'assets/js/helper.min.js': ['assets/js/helper.js']
                }
            },
            sticky: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/sticky-sidebar.js.map'
                },
                files: {
                    'assets/js/sticky-sidebar.min.js': ['assets/js/sticky-sidebar.js']
                }
            },
            admin: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/admin.js.map'
                },
                files: {
                    'assets/js/admin.min.js': ['assets/js/admin.js']
                }
            }
        },
        sass: {
            style: {
                files: {
                    'assets/css/admin.css': ['assets/scss/admin.scss']
                }
            }
        },
        autoprefixer: {
            dist: {
                files: {
                    'assets/css/admin.css': ['assets/css/admin.css']
                }
            }
        },
        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'assets/css/admin.min.css': ['assets/css/admin.css']
                }
            }
        },
        watch: {
            styles: {
                files: ['assets/scss/admin.scss'],
                tasks: ['sass:style', 'cssmin'],
                options: {
                    spawn: false
                }
            },
            scripts: {
                files: ['assets/js/admin.js'],
                tasks: ['uglify:admin'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify-es');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-css');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-string-replace');

    grunt.registerTask('default', ['concat', 'copy', 'string-replace', 'uglify', 'sass', 'autoprefixer', 'cssmin', 'watch']);

};
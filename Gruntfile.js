/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// Load package data
		pkg: grunt.file.readJSON('package.json'),

		// Set folder vars
		dirs: {
			css: 'css',
			js: 'js',
			compat_wordpress_css: 'compat/wordpress/css',
			compat_wordpress_js: 'compat/wordpress/js',
			compat_plugin_js: 'compat/plugin/js',
			languages: 'languages'
		},

		// Javascript linting with jshint
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js',
				'<%= dirs.compat_plugin_js %>/*.js',
				'<%= dirs.compat_wordpress_js %>/*.js',
				'!**/*.min.js'
			]
		},

		// Minify .js files
		uglify: {
			options: {
				preserveComments: 'some',
				banner: '/* <%= pkg.title %> */\n'
			},
			admin: {
				files: [{
					expand: true,
					cwd: './',
					src: [
						'<%= dirs.js %>/*.js',
						'<%= dirs.compat_plugin_js %>/*.js',
						'<%= dirs.compat_wordpress_js %>/*.js',
						'!**/*.min.js'
					],
					ext: '.min.js'
				}]
			}
		},

		// Minify .css files
		cssmin: {
			minify: {
				options: {
					banner: '/* <%= pkg.title %> */'
				},
				files: [{
					expand: true,
					cwd: './',
					src: [
						'<%= dirs.css %>/*.css',
						'<%= dirs.compat_wordpress_css %>/*.css',
						'!**/*.min.css'
					],
					ext: '.min.css'
				}]
			}
		},

		// Watch changes for assets
		watch: {
			js: {
				files: ['<%= dirs.js %>/*.js'],
				tasks: ['uglify'],
				options: {
					spawn: false
				}
			},
			css: {
				files: ['<%= dirs.css %>/*.css'],
				tasks: ['cssmin'],
				options: {
					spawn: false
				}
			},
			readme: {
				files: ['readme.txt'],
				tasks: ['wp_readme_to_markdown'],
				options: {
					spawn: false
				}
			}
		},

		// Clean build dir
		clean: {
			main: ['build/<%= pkg.name %>']
		},
		
		// Copy the plugin to a versioned release directory
		copy: {
			main: {
				src:  [
					'**',
					'!.git/**',
					'!.gitignore',
					'!.gitmodules',
					'!.jshintrc',
					'!.scrutinizer.yml',
					'!node_modules/**',
					'!build/**',
					'!Gruntfile.js',
					'!package.json',
					'!composer.json',
					'!LICENSE',
					'!README.md',
					'!assets/**',
					'!nbproject/**',
					'!**/*.LCK',
					'!**/_notes/**',
					'!tmp/**'
				],
				dest: 'build/<%= pkg.name %>/'
			}
		},

		// Convert line endings to LF
		lineending: {
			build: {
				options: {
					eol: 'lf',
					overwrite: true
				},
				files: [{
					expand: true,
					cwd: 'build/<%= pkg.name %>/',
					src: ['**/*.{php,css,js,po,txt}']
				}]
			}
		},
		
		// Create zip package
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		},

		// Generate .pot file
		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: ['build/.*'],
					potFilename: 'black-studio-tinymce-widget.pot',
					processPot: function( pot ) {
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/black-studio/black-studio-tinymce-widget/issues\n';
						pot.headers['plural-forms'] = 'nplurals=2; plural=n != 1;';
						pot.headers['last-translator'] = 'Black Studio <info@blackstudio.it>\n';
						pot.headers['language-team'] = 'Black Studio <info@blackstudio.it>\n';
						pot.headers['x-poedit-basepath'] = '.\n';
						pot.headers['x-poedit-language'] = 'English\n';
						pot.headers['x-poedit-country'] = 'United States\n';
						pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
						pot.headers['x-poedit-keywordslist'] = '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;\n';
						pot.headers['x-poedit-bookmarks'] = '\n';
						pot.headers['x-poedit-searchpath-0'] = '.\n';
						pot.headers['x-textdomain-support'] = 'yes\n';
						// Exclude string without textdomain and plugin's meta data
						var translation, delete_translation,
							excluded_strings = [ 'Title:', 'Visual', 'HTML', 'Cheatin&#8217; uh?', 'Automatically add paragraphs' ],
							excluded_meta = [ 'Plugin Name of the plugin/theme', 'Plugin URI of the plugin/theme', 'Author of the plugin/theme', 'Author URI of the plugin/theme' ];
						for ( translation in pot.translations[''] ) {
							delete_translation = false;
							if ( excluded_strings.indexOf( translation ) >= 0 ) {
								delete_translation = true;
								console.log( 'Excluded string: ' + translation );
							}
							if ( typeof pot.translations[''][translation].comments.extracted !== 'undefined' ) {
								if ( excluded_meta.indexOf( pot.translations[''][translation].comments.extracted ) >= 0 ) {
									delete_translation = true;
									console.log( 'Excluded meta: ' + pot.translations[''][translation].comments.extracted );
								}
							}
							if ( delete_translation ) {
								delete pot.translations[''][translation];
							}
						}
						return pot;
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		// Check plugin text domain
		checktextdomain: {
			options:{
				text_domain: 'black-studio-tinymce-widget',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				],
				report_missing: false
			},
			files: {
				src:  [
					'**/*.php',
					'!node_modules/**',
					'!build/**'
				],
				expand: true
			}
		},

		// Generate .mo files from .po files
		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.languages %>',
					src: ['*.po'],
					dest: '<%= dirs.languages %>',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		// Generate README.md from readme.txt
		wp_readme_to_markdown: {
			readme: {
				files: {
					'README.md': 'readme.txt'
				},
				options: {
					screenshot_url: 'https://raw.githubusercontent.com/black-studio/{plugin}/develop/assets/{screenshot}.png'
				}
			}
		},

		// Check version
		checkwpversion: {
			options:{
				readme: 'readme.txt',
				plugin: 'black-studio-tinymce-widget.php'
			},
			plugin_vs_readme: { //Check plugin header version againts stable tag in readme
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			plugin_vs_grunt: { //Check plugin header version against package.json version
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '=='
			},
			plugin_vs_internal: { //Check plugin header version against internal defined version
				version1: 'plugin',
				version2: grunt.file.read('black-studio-tinymce-widget.php').match( /version = '(.*)'/ )[1],
				compare: '=='
			}
		},

		// Deploy to WP repository
		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: '<%= pkg.name %>',
					build_dir: 'build/<%= pkg.name %>',
					assets_dir: 'assets',
					tmp_dir: './tmp'
				}
			}
		}

	});

	// Load NPM tasks to be used here
	require( 'load-grunt-tasks' )( grunt );

	// Register tasks
	grunt.registerTask( 'default', [
		'jshint',
		'cssmin',
		'uglify'
	]);

	grunt.registerTask( 'languages', [
		'checktextdomain',
		'makepot',
		'potomo'
	]);

	grunt.registerTask( 'readme', [
		'wp_readme_to_markdown'
	]);

	grunt.registerTask( 'build', [
		'default',
		'checkwpversion',
		'checktextdomain',
		'readme',
		'clean',
		'copy',
		'lineending',
		'compress'
	]);

	grunt.registerTask( 'deploy', [
		'build',
		'wp_deploy'
	]);

};
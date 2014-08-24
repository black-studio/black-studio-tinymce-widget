module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// Load package data
		pkg: grunt.file.readJSON('package.json'),

		// Set folder vars
		dirs: {
			css: 'css',
			js: 'js'
		},

		// Javascript linting with jshint
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js'
			]
		},

		// Minify .js files
		uglify: {
			options: {
				preserveComments: 'some',
				banner: '/* <%= pkg.title %> - v<%= pkg.version %> */\n'
			},
			admin: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js',
						'!Gruntfile.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			}
		},

		// Minify .css files
		cssmin: {
			minify: {
				options: {
					banner: '/* <%= pkg.title %> - v<%= pkg.version %> */'
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.css %>/',
					src: [
						'*.css',
						'!*.min.css'
					],
					dest: '<%= dirs.css %>/',
					ext: '.min.css'
				}]
			}
		},

		// Watch changes for assets
		watch: {
			js: {
				files: [
					'<%= dirs.js %>/*.js'
				],
				tasks: ['uglify']
			},
			css: {
				files: [
					'<%= dirs.css %>/*.css'
				],
				tasks: ['cssmin']
			}
		},

		// Generate .pot file
		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					potFilename: 'black-studio-tinymce-widget.pot',
					processPot: function( pot, options ) {
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/black-studio/black-studio-tinymce-widget/issues\n';
						pot.headers['plural-forms'] = 'nplurals=2; plural=n != 1;';
						pot.headers['last-translator'] = 'Black Studio <info@blackstudio.it>\n';
						pot.headers['language-team'] = 'Black Studio <info@blackstudio.it>\n';
						pot.headers['x-poedit-basepath'] = '.\n';
						pot.headers['x-poedit-language'] = 'English\n';
						pot.headers['x-poedit-country'] = 'UNITED STATES\n';
						pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
						pot.headers['x-poedit-keywordslist'] = '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;\n';
						pot.headers['x-poedit-bookmarks'] = '\n';
						pot.headers['x-poedit-searchpath-0'] = '.\n';
						pot.headers['x-textdomain-support'] = 'yes\n';
						return pot;
					},
					type: 'wp-plugin'
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
					'**/*.php', // Include all files
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true
			}
		},

		// Generate .mo files from .po files
		po2mo: {
			files: {
				src: 'languages/*.po',
				expand: true
			}
		},

		// Generate README.md from readme.txt
		wp_readme_to_markdown: {
			convert: {
				files: {
					'README.md': 'readme.txt'
				}
			},
			options : {
				banner: 'https://ps.w.org/black-studio-tinymce-widget/assets/banner-772x250.png',
				afterBannerMarkdown: '[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/black-studio/black-studio-tinymce-widget/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/black-studio/black-studio-tinymce-widget/badges/build.png?b=develop)',
				screenshots: {
					enabled: true,
					prefix: 'https://ps.w.org/black-studio-tinymce-widget/assets/screenshot-',
					suffix: '.png'
				}
			}
		},

		// Check version
		checkwpversion: {
			options:{
				readme: 'readme.txt',
				plugin: 'black-studio-tinymce-widget.php',
			},
			plugin_vs_readme: { //Check plug-in version and stable tag match
				version1: 'plugin',
				version2: 'readme',
				compare: '==',
			},
			plugin_vs_grunt: { //Check plug-in version and package.json match
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '==',
			},
			plugin_vs_internal: { //Check plug-in version and internal defined version
				version1: 'plugin',
				version2: grunt.file.read('black-studio-tinymce-widget.php').match( /version = '(.*)'/ )[1],
				compare: '==',
			}
		}

	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-po2mo' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks( 'grunt-checkwpversion' );

	// Register tasks
	grunt.registerTask( 'default', [
		'cssmin',
		'uglify'
	]);
	grunt.registerTask( 'languages', [ 
		'checktextdomain',
		'makepot',
		'po2mo'
	]);
	grunt.registerTask( 'check', [ 
		'checkwpversion',
		'checktextdomain'
	]);
	grunt.registerTask( 'readme', [ 
		'wp_readme_to_markdown'
	]);

};
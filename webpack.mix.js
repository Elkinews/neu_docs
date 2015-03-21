const mix = require('laravel-mix');
const path = require('path');

mix
	.js('resources/js/app.js', 'public/js')
	.vue()
	.sass('resources/sass/app.scss', 'public/css')
	.webpackConfig({
		resolve: { alias: { '@': path.resolve('resources/sass'),
                    '@utils': path.resolve('resources/js/utils'),
                    '@components': path.resolve('resources/js/components'),
                    '@service': path.resolve('resources/js/service'),
                    '@shared': path.resolve('resources/js/shared')  } },
		module: {
			rules: [
				{
					test: /\.scss$/,
					use: [
						{
							loader: "sass-loader",
							options: {
								additionalData: `@import "@/_variables.scss";`
							},
						},
					],
				}
			]
		},
	})
  .sourceMaps(false, 'eval-source-map', '');

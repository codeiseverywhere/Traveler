const path = require('path');

const _ = require('lodash');
const jsonfile = require('jsonfile');
const webpack = require('webpack');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const mix = require('laravel-mix');
const postcssPresetEnv = require('postcss-preset-env');
require('laravel-mix-string-replace');
let config = {
  paths: {
    root: path.resolve(__dirname, './'),
    public: path.resolve(__dirname, './public'),
    css: path.resolve(__dirname, './public/css'),
    js: path.resolve(__dirname, './public/js'),
    sass: path.resolve(__dirname, './resources/assets/sass'),
    images: path.resolve(__dirname, './resources/assets/images'),
    javascript: path.resolve(__dirname, './resources/assets/js'),
  },
};
const isProductionOrStaging =
  process.env.NODE_ENV === 'production' || process.env.NODE_ENV === 'staging';

const fileName =
  process.env.NODE_ENV === 'production' ? '[name]~[contenthash:8]' : '[name]';

const isModernBuild = String(process.env.BUILD_MODERN).toLowerCase() === 'true';

const isSkipClean = String(process.env.SKIP_CLEAN).toLowerCase() === 'true';

const removeHashFromKeyRegex = /~(.+)(\..+)$/g;

const mixManifestGenerated = 'public/mix-manifest.json';
const mixManifestRevised = `public/mix-manifest${
  isModernBuild ? '' : '~legacy'
}.json`;

const babelLoaderModernOptions = {
  presets: [
    [
      '@babel/preset-env',
      {
        corejs: 3,
        modules: false,
        useBuiltIns: 'entry',
        targets: {
          esmodules: true,
          browsers: ['last 2 versions', 'safari > 10', 'edge > 80'],
        },
      },
    ],
  ],
};

const babelLoaderLegacyOptions = {
  presets: [
    [
      '@babel/preset-env',
      {
        targets: {
          browsers: 'last 4 versions, > 3%, ie >= 10',
        },
        bugfixes: true,
        useBuiltIns: 'entry',
        corejs: 3,
      },
    ],
  ],
  plugins: [
    '@babel/plugin-syntax-dynamic-import',
    '@babel/plugin-syntax-class-properties',
    '@babel/plugin-proposal-class-properties',
  ],
};

const plugins = [
  new webpack.ProvidePlugin({
    $: 'jquery',
    jQuery: 'jquery',
    spin: 'spin',
  }),
  new webpack.optimize.ModuleConcatenationPlugin(),
  new webpack.HashedModuleIdsPlugin(), // so that file hashes don't change unexpectedly
  new MiniCssExtractPlugin({
    filename: `css${fileName}${isModernBuild ? '' : '~legacy'}.css`,
    chunkFilename: `css${fileName}${isModernBuild ? '' : '~legacy'}.css`,
    path: '../css',
    outputPath: '../css',
    publicPath: '/wp-content/themes/traveler-marriott-v2/public/css',
  }),
  new webpack.EnvironmentPlugin({
    BUILD_ENV: 'development',
    NODE_ENV: 'development',
    BUILD_MODERN: 'true',
  }),
];

if (!isSkipClean) {
  plugins.push(new CleanWebpackPlugin());
}

function sleep(ms) {
  return new Promise((resolve) => {
    setTimeout(resolve, ms);
  });
}

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your theme. By default, we are compiling the Sass file for the theme
 | as well as bundling up all the JS files.
 |
 */

// Do not use Laravel mix's built-in file loader.
Mix.listen('configReady', function(config) {
  const mixFileLoaderRegex = /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/;
  const rules = config.module.rules;
  for (let rule of rules) {
    if (rule.test.source === mixFileLoaderRegex.source) {
      rules.splice(rules.indexOf(rule), 1);
      return;
    }
  }
});

mix
  .webpackConfig({
    module: {
      rules: [
        {
          test: /\.(csv|gif|ico|jpeg|jpg|json|png|svg|txt|webmanifest|xml)$/,
          use: [
            {
              loader: 'file-loader',
              options: {
                name: '[name]~[contenthash:8].[ext]',
                outputPath: 'images',
                path: path.resolve(config.paths.public, 'images'),
                publicPath:
                  '/wp-content/themes/traveler-marriott-v2/public/images/',
              },
            },
          ],
        },
        {
          test: /\.(s)?css|sass$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
            },
            {
              loader: 'css-loader',
              options: {
                importLoaders: 3,
              },
            },
            {
              loader: 'postcss-loader',
              options: {
                ident: 'postcss',
                plugins: () => [postcssPresetEnv()],
              },
            },
            {
              loader: 'resolve-url-loader',
            },
            {
              loader: `sass-loader${
                isProductionOrStaging ? '?outputStyle=compressed' : ''
              }`,
              options: {
                implementation: require('sass'),
                sourceMap: true,
                sourceMapContents: false,
              },
            },
          ],
        },
        {
          test: require.resolve('jquery'),
          use: [
            {
              loader: 'expose-loader',
              options: 'jQuery',
            },
            {
              loader: 'expose-loader',
              options: '$',
            },
          ],
        },
        {
          test: require.resolve('spin'),
          use: [
            {
              loader: 'expose-loader',
              options: 'spin',
            },
          ],
        },
      ],
    },
    optimization: {
      minimize: isProductionOrStaging,
      minimizer: [
        new TerserPlugin({
          parallel: true,
          terserOptions: {
            mangle: true,
            output: {
              beautify: false,
              comments: false,
            },
            safari10: true,
          },
          extractComments: false,
        }),
      ],
      nodeEnv: process.env.NODE_ENV,
      runtimeChunk: true,
      concatenateModules: true,
      splitChunks: {
        automaticNameDelimiter: '-',
        chunks: 'all',
        name: function(_module, chunks) {
          let isApp = false;
          let isVendor = false;
          for (chunk of chunks) {
            //console.warn(chunk.name);
            if (chunk.name === '/app') {
              isApp = true;
            } else if (chunk.name === '/vendor') {
              isVendor = true;
            }
          }

          if (isVendor || isApp) {
            return chunk.name;
          }

          return '/chunk/other';
        },
      },
    },
    output: {
      filename: `js${fileName}${isModernBuild ? '' : '~legacy'}.js`,
      chunkFilename: `js${fileName}${isModernBuild ? '' : '~legacy'}.js`,
      path: config.paths.public,
      publicPath: `/wp-content/themes/traveler-marriott-v2/public/`,
    },
    plugins,
    resolve: {
      modules: [
        path.resolve(__dirname, 'node_modules'),
        path.resolve(__dirname, 'resources/assets/images/'),
        'node_modules',
      ],
    },
  })
  .options({
    babelConfig: {
      ...(isModernBuild ? babelLoaderModernOptions : babelLoaderLegacyOptions),
    },
  })
  .setPublicPath('public')
  .js('./resources/src/main/app.js', 'public')
  .extract(['jquery', 'axios', 'vue'])
  .copyDirectory(config.paths.images, 'public/images')
  .disableSuccessNotifications()
  .stringReplace({
    test: /.*\.js$/,
    loader: 'string-replace-loader',
    options: {
      search: 'hqdefault.jpg',
      replace: 'maxresdefault.jpg',
    }
  })
  .then(function() {
    // Runs through the list of files created by webpack
    // to create a manifest with the names without a hash
    // for easier WP Enqueue.
    jsonfile.readFile(mixManifestGenerated, async function(err, obj) {
      const newJson = {};
      await _.forIn(obj, async function(value, key) {
        let newKey = (process.env.NODE_ENV === 'production'
          ? key.replace(removeHashFromKeyRegex, '$2')
          : key
        ).slice(1);
        let newValue = value.slice(1);
        if (false === _.includes(value, 'chunk')) {
          newJson[newKey] = newValue;
        }
      });
      jsonfile.writeFile(mixManifestRevised, newJson, { spaces: 2 }, function(
        err,
      ) {
        if (err) console.error(err);
      });
    });
  });

const path = require('path')
const Dotenv = require('dotenv-webpack');

const webpack = require('webpack');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js/src/'),
            '@themeConfig': path.resolve(__dirname, 'resources/js/themeConfig.js'),
            '@core': path.resolve(__dirname, 'resources/js/src/@core'),
            '@validations': path.resolve(__dirname, 'resources/js/src/@core/utils/validations/validations.js'),
            '@axios': path.resolve(__dirname, 'resources/js/src/libs/axios')
        }
    },
    plugins: [
        new Dotenv(),
        new webpack.IgnorePlugin({
            resourceRegExp: /^\.\/locale$/,
            contextRegExp: /moment$/
        })
    ],
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    {
                        loader: 'sass-loader',
                        options: {
                            sassOptions: {
                                includePaths: ['node_modules', 'resources/js/src/assets']
                            }
                        }
                    }
                ]
            },
            {
                test: /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/,
                loaders: {
                    loader: 'file-loader',
                    options: {
                        name: 'images/[path][name].[ext]',
                        context: '../vuexy-vuejs-bootstrap-vue-template/src/assets/images'
                    }
                }
            }
        ]
    },
    output: {
        chunkFilename: 'js/chunks/[name].js'
    }
}

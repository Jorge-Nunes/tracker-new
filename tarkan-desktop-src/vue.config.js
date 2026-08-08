const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;



module.exports = {
    publicPath: '/',
    productionSourceMap: true,

    chainWebpack: config => {
        config.optimization.minimizer('terser').tap((args) => {
            args[0].terserOptions.output = {
                ...args[0].terserOptions.output,
                comments: false  // exclude all comments from output
            }
            return args
        })


        // GraphQL Loader
        config.module
            .rule('conditional')
            .test(/\.vue$/)
            .use('js-conditional-compile-loader')
            .loader('js-conditional-compile-loader')
            .options({

                myFlag: true, // enabled by `npm run build --myflag`
                myFlag2: false

            })
            .end()
    },

    configureWebpack:{
        plugins: [
            new BundleAnalyzerPlugin()
        ],
        resolve: {
            alias: {
                'vue-i18n': 'vue-i18n/dist/vue-i18n.runtime.esm-bundler.js'
            }
        },
    },

    devServer: {
        disableHostCheck: true
    },

    pwa: {
        name: 'Tarkan',
        themeColor: '#05a7e3',
        msTileColor: '#abe6ff',
        appleMobileWebAppCapable: 'yes',
        appleMobileWebAppStatusBarStyle: 'black',
        workboxPluginMode: "InjectManifest",
        manifestPath:"tarkan/assets/custom/manifest.json",
        workboxOptions: {
            swSrc: "src/service-worker.js",
            exclude: [
                /\.map$/,
                /\.mp3$/,
                /\.jpg$/,
                /\.png$/,
                /\.css$/,
                /\.json$/,
                /^manifest.*\.js?$/
            ]
        },
        manifestOptions:{
            icons:[
                {"src":"./icons/android-chrome-192x192.png","sizes":"192x192","type":"image/png"},
                {"src":"./icons/android-chrome-512x512.png","sizes":"512x512","type":"image/png"},
                {"src":"./icons/android-chrome-maskable-192x192.png","sizes":"192x192","type":"image/png","purpose":"maskable"},
                {"src":"./icons/android-chrome-maskable-512x512.png","sizes":"512x512","type":"image/png","purpose":"maskable"}
            ]
        },
        iconPaths:{
            faviconSVG: null,
            favicon32: 'tarkan/assets/custom/icons/favicon-32x32.png',
            favicon16: 'tarkan/assets/custom/icons/favicon-16x16.png',
            appleTouchIcon: 'tarkan/assets/custom/icons/apple-touch-icon-152x152.png',
            maskIcon: null,
            msTileImage: 'tarkan/assets/custom/icons/msapplication-icon-144x144.png'
        }

    },

    pluginOptions: {
      i18n: {
        locale: 'en-US',
        fallbackLocale: 'en-US',
        localeDir: 'locales',
        enableLegacy: false,
        runtimeOnly: false,
        compositionOnly: false,
        fullInstall: true
      }
    }
}

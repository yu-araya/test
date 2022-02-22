module.exports = {
    configureWebpack: {
        devtool: "source-map",
    },
    outputDir: "../TouchPay.standard/app/webroot/tablet",
    publicPath: process.env.NODE_ENV === "production" ? "./" : "/TouchPay.standard/tablet/",
    devServer: {
        port: 8000,
        disableHostCheck: true,
    },
    pwa: {
        workboxOptions: {
          skipWaiting: true
        }
    },
};

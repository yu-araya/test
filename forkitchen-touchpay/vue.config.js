module.exports = {
    configureWebpack: {
        devtool: "source-map",
    },
    outputDir: "../TouchPay.standard/app/webroot/forkitchen",
    publicPath: process.env.NODE_ENV === "production" ? "./" : "/TouchPay.standard/forkitchen/",
    devServer: {
        port: 8888,
        disableHostCheck: true,
    },
};

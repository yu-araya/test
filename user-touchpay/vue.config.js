module.exports = {
    configureWebpack: {
        devtool: "source-map",
    },
    outputDir: "../TouchPay.standard/app/webroot/user",
    publicPath: process.env.NODE_ENV === "production" ? "./" : "/TouchPay.standard/user/",
    devServer: {
        port: 8800,
        disableHostCheck: true,
    },
};

const path = require("path");

module.exports = {
    mode: 'development',
    entry: "./src/js/App.js",
    output: {
        path: path.resolve(__dirname, "./public/js"),
        publicPath: '/public/js',
        filename: "bundle.js"
    },
    watch: true,
    watchOptions: {
        aggregateTimeout: 300,
        poll: 1000
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["babel-preset-env"]
                    }
                }
            }
        ]
    }
};

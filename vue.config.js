module.exports = {
    publicPath: '',
    devServer: {
        proxy: {
            '^/api/': {
                target: 'http://localhost:80/tasker',
                changeOrigin: true,
                secure: true
            }
        }
    }
}

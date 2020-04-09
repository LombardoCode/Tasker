module.exports = {
    publicPath: '',
    devServer: {
        proxy: {
            '^/api/': {
                target: 'http://localhost:80',
                changeOrigin: true,
                secure: false
            }
        }
    }
}

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        settings: path.resolve(process.cwd(), 'src', 'js', 'settings', 'index.tsx'),
    },
    output: {
        ...defaultConfig.output,
        path: path.resolve(process.cwd(), 'build', 'scripts'),
    },
};

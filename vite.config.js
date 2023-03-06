import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
//import fs from 'fs';
//import basicSsl from '@vitejs/plugin-basic-ssl'

export default defineConfig({
    server: {
        hmr: {
            host: 'egyids.com',
        },
    },
/*    https: {
        key: fs.readFileSync('/root/ssl/egyids_tld.key'),
        cert: fs.readFileSync('/root/ssl/egyids_com.crt')
    },
*/
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js']
        }),
        vue({
            template: {
                transformAssetUrls: {
                   base: null,
                   includeAbsolute: false,
                },
            },
        }),
        //basicSsl()
    ],
});

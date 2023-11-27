import { createApp } from 'vue'
import App from './App.vue'
import { setupStore } from '@/store'
import { setupRouterGuard } from '@/router/guard'
import { router, setupRouter } from '@/router'
import { setupDirective } from '@/directive'

import { createFromIconfontCN } from '@ant-design/icons-vue';
const Icon = createFromIconfontCN({
    scriptUrl: import.meta.env.VITE_BASE_PATH + '/js/icon.js',
});

async function bootstrap() {
    const app = createApp(App);
    // setup store
    setupStore(app);
    // setup router
    setupRouter(app);
    // setup router Guard
    setupRouterGuard(router);
    // setup directive
    setupDirective(app);
    // start
    app.component("Icon", Icon);
    app.mount('#app');
}

bootstrap();
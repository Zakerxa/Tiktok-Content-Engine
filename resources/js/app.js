import '../css/app.css';
import './bootstrap';

import { createInertiaApp,router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Z.A.K.E.R.X.A';

router.on('navigate', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

createInertiaApp({
    title: (title) => `${title} ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`,import.meta.glob('./Pages/**/*.vue'),),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);

        // Vue app mount ပြီးတာနဲ့ blank-page loader ကို fade-out ဖျက်ပစ်မယ်
        const loader = document.getElementById('app-loader');
        if (loader) {
            loader.classList.add('app-loader-hidden');
            setTimeout(() => loader.remove(), 300);
        }
 
        return app;

    },
    progress: {
        color: '#4B5563',
    },
});

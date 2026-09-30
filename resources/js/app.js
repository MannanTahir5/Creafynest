import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import AppLayout from './Layouts/AppLayout.vue';
import AdminLayout from './Layouts/AdminLayout.vue';
import GuestLayout from './Layouts/GuestLayout.vue';

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const importer = pages[`./Pages/${name}.vue`];

        if (!importer) {
            throw new Error(`Inertia page not found: ${name}`);
        }

        const page = await importer();

        if (!page?.default?.layout) {
            if (name.startsWith('Admin/')) {
                page.default.layout = AdminLayout;
            } else if (name.startsWith('Auth/')) {
                page.default.layout = GuestLayout;
            } else {
                page.default.layout = AppLayout;
            }
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});

// Import main application CSS file
import '../css/app.css';

// Import bootstrap file (Axios & CSRF setup)
import './bootstrap';

// Import function to create Inertia application
import { createInertiaApp } from '@inertiajs/vue3';

// Helper function to resolve Vue pages dynamically
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Import Vue functions
import { createApp, h } from 'vue';

// Import Ziggy for Laravel route support in JavaScript
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Get application name from environment file
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Create Inertia application
createInertiaApp({
    // Set dynamic page title
    title: (title) => `${title} - ${appName}`,

    // Resolve Vue pages from Pages directory
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),

    // Setup Vue application
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            // Use Inertia plugin
            .use(plugin)
            // Use Ziggy for named routes
            .use(ZiggyVue)
            // Mount app to DOM
            .mount(el);
    },

    // Configure Inertia progress bar
    progress: {
        color: '#4B5563',
    },
});

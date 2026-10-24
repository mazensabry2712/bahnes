import { createIcons, Send } from 'lucide';

// Keep icon output tree-shakeable: only icons used by Blade templates belong here.
createIcons({
    icons: {
        Send,
    },
    attrs: {
        class: 'ui-icon',
        'stroke-width': 1.8,
    },
});

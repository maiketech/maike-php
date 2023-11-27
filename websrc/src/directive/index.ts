import type { App } from 'vue';
import { setupPermissionDirective } from './permission';

export function setupDirective(app: App) {
    setupPermissionDirective(app);
}

import { Alpine, registerDashboardAlpine } from './dashboard';
import { initAdminTheme } from './stores/theme';
import { initAdminSidebar } from './stores/sidebar';
import { initAdminToast } from './stores/toast';
import { initAdminModal } from './stores/modal';
import { initAdminPalette } from './stores/palette';
import { initAdminPanels } from './stores/panels';
import { initAdminPageLoader } from './stores/page-loader';

registerDashboardAlpine();
window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    initAdminTheme();
    initAdminSidebar();
    initAdminToast();
    initAdminModal();
    initAdminPalette();
    initAdminPanels();
    initAdminPageLoader();
});

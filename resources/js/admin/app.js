import { Alpine, registerDashboardAlpine } from './dashboard';
import { registerCategoryForm } from './category-form';
import { registerProductForm } from './product-form';
import { registerCustomerForm } from './customer-form';
import { initAdminTheme } from './stores/theme';
import { initAdminSidebar } from './stores/sidebar';
import { initAdminToast } from './stores/toast';
import { initAdminModal } from './stores/modal';
import { initAdminPalette } from './stores/palette';
import { initAdminPanels } from './stores/panels';
import { initAdminPageLoader } from './stores/page-loader';

registerDashboardAlpine();
registerCategoryForm(Alpine);
registerProductForm(Alpine);
registerCustomerForm(Alpine);
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

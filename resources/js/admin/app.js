import { copyTextToClipboard } from './utils/clipboard';
import { Alpine, registerDashboardAlpine, initAdminDashboard } from './dashboard';
import { registerCategoryForm } from './category-form';
import { registerProductForm } from './product-form';
import { registerCustomerForm } from './customer-form';
import { registerOrderCreateForm } from './order-create-form';
import { registerMediaLibrary } from './media-library';
import { initAdminTheme } from './stores/theme';
import { initAdminSidebar } from './stores/sidebar';
import { initAdminToast } from './stores/toast';
import { initAdminModal } from './stores/modal';
import { initAdminPalette } from './stores/palette';
import { initAdminPanels } from './stores/panels';
import { initAdminNotifications } from './notifications';
import { initAdminPageLoader } from './stores/page-loader';

registerDashboardAlpine();
registerCategoryForm(Alpine);
registerProductForm(Alpine);
registerCustomerForm(Alpine);
registerOrderCreateForm(Alpine);
registerMediaLibrary(Alpine);
window.adminCopyText = copyTextToClipboard;
window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    initAdminTheme();
    initAdminSidebar();
    initAdminToast();
    initAdminModal();
    initAdminPalette();
    initAdminPanels();
    initAdminNotifications();
    initAdminPageLoader();
    initAdminDashboard();
});

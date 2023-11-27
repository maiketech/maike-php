import type { App, Directive, DirectiveBinding } from 'vue';
import { useAdminStoreWithOut } from "@/store/admin";

// 无权限禁用元素
const authDisabled = (el: Element, binding: DirectiveBinding<any>) => {
    const adminStore = useAdminStoreWithOut();
    const AuthList = adminStore.getAuthList;
    const IsSuper = adminStore.getAdminInfo.is_super;
    if (!AuthList.includes(binding.value) && !IsSuper) {
        el.setAttribute("disabled", "true");
        el.setAttribute("title", "无权限");
    }
};
const authDisabledDirective: Directive = {
    mounted: authDisabled,
};

// 无权限删除元素
const authDelete = (el: Element, binding: DirectiveBinding<any>) => {
    const adminStore = useAdminStoreWithOut();
    const AuthList = adminStore.getAuthList;
    const IsSuper = adminStore.getAdminInfo.is_super;
    if (!AuthList.includes(binding.value) && !IsSuper) {
        el.remove();
    }
};
const authDeleteDirective: Directive = {
    mounted: authDelete,
};

export function setupPermissionDirective(app: App) {
    app.directive('auth', authDisabledDirective);
    app.directive('auth-show', authDeleteDirective);
}

export default setupPermissionDirective;
import type { Router } from 'vue-router';
import { Modal, notification } from 'ant-design-vue';
import { useAdminStoreWithOut } from '@/store/admin';

const SYSTEM_NAME = import.meta.env.VITE_APP_TITLE;
const whitePathList = ['/login', '/404'];

// Don't change the order of creation
export function setupRouterGuard(router: Router) {
    createPageLoadingGuard(router);
    createMessageGuard(router);
    createProgressGuard(router);
    createPermissionGuard(router);
}

//判断权限
function createPermissionGuard(router: Router) {
    router.beforeEach(async (to, from, next) => {
        // 白名单直接进入
        if (whitePathList.includes(to.path)) {
            next();
            return;
        }

        const userStore = useAdminStoreWithOut();
        if (userStore.getToken) {
            //已登录
            next();
            return;
        } else {
            // 未登录或失效，跳转到登录页面
            const redirectData: {
                path: string;
                replace: boolean;
                query?: Recordable<string>;
            } = {
                path: "/login",
                replace: true,
            };
            if (to.path) {
                redirectData.query = {
                    ...redirectData.query,
                    redirect: to.path,
                };
            }
            next(redirectData);
            return;
        }
    });
}

// 处理页面加载状态
function createPageLoadingGuard(router: Router) {
    const adminStore = useAdminStoreWithOut();
    const doc = window.document;
    router.beforeEach(async (to) => {
        if (!adminStore.getToken) {
            return true;
        }
        if (to.meta.title) {
            doc.title = to.meta.title + " - " + SYSTEM_NAME
        } else {
            doc.title = SYSTEM_NAME
        }

        return true;
    });
    router.afterEach(async () => {
        return true;
    });
}

//路由开始前关闭相关信息弹框
export function createMessageGuard(router: Router) {
    router.beforeEach(async () => {
        try {
            Modal.destroyAll();
            notification.destroy();
        } catch (error) {
            console.log('message guard error:' + error);
        }
        return true;
    });
}

// 加载效果处理
export function createProgressGuard(router: Router) {
    router.beforeEach(async (to) => {
        if (to.meta.loaded) {
            return true;
        }
        //nProgress.start();
        return true;
    });

    router.afterEach(async () => {
        //nProgress.done();
        return true;
    });
}
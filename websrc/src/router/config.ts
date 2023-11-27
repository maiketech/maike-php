import { RouteRecordRaw } from "vue-router";
import BaseLayout from '@/views/layout/BaseLayout.vue';
import NotFound from '@/views/404.vue';

export const routesConfig: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: BaseLayout,
        redirect: '/home',
        meta: { title: '工作台' },
        children: [
            {
                path: '/home',
                name: 'home',
                meta: { title: '工作台' },
                component: () => import('@/views/home.vue')
            },
        ]
    },
    {
        path: '/yonghu',
        name: 'yonghu',
        component: BaseLayout,
        redirect: '/user',
        meta: { title: '用户管理' },
        children: [
            {
                path: '/user',
                name: 'user_list',
                meta: { title: '用户管理' },
                component: () => import('@/views/user/user.vue')
            }
        ]
    },
    {
        path: '/content',
        name: 'content',
        component: BaseLayout,
        redirect: '/article',
        meta: { title: '内容管理' },
        children: [
            {
                path: '/article',
                name: 'article_list',
                meta: { title: '图文内容' },
                component: () => import('@/views/content/article.vue')
            },
            {
                path: '/article_category',
                name: 'article_category_list',
                meta: { title: '图文内容' },
                component: () => import('@/views/content/article_category.vue')
            },
            {
                path: '/notice',
                name: 'notice_list',
                meta: { title: '公告管理' },
                component: () => import('@/views/content/notice.vue')
            },
            {
                path: '/help',
                name: 'help_list',
                meta: { title: '帮助内容' },
                component: () => import('@/views/content/help.vue')
            },
        ]
    },
    {
        path: '/system',
        name: 'system',
        component: BaseLayout,
        redirect: '/admin',
        meta: { title: '系统设置' },
        children: [
            {
                path: '/admin',
                name: 'admin_list',
                meta: { title: '系统管理员' },
                component: () => import('@/views/system/admin.vue')
            },
            {
                path: '/role',
                name: 'admin_role_list',
                meta: { title: '角色权限' },
                component: () => import('@/views/system/role.vue')
            },
            {
                path: '/dict',
                name: 'dict_list',
                meta: { title: '数据字典' },
                component: () => import('@/views/system/dict.vue')
            },
            {
                path: '/setting',
                name: 'setting_system',
                meta: { title: '参数设置' },
                component: () => import('@/views/system/setting.vue')
            },
            {
                path: '/log',
                name: 'system_log_list',
                meta: { title: '操作日志' },
                component: () => import('@/views/system/log.vue')
            }
        ]
    },
    {
        path: '/login',
        name: 'login',
        meta: { title: '登录' },
        component: () => import('@/views/login.vue'),
    },
    {
        path: '/:pathMatch(.*)',
        redirect: '/404',
    },
    {
        path: '/404',
        component: NotFound,
    },
];

export default routesConfig;
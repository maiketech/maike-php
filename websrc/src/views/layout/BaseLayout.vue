<template>
    <a-layout class="m-layout">
        <a-layout-sider class="m-layout-sider" :collapsedWidth="100" :width="100">
            <div class="logo" />
            <div class="m-sider-menu" v-if="menuData && menuData.length > 0">
                <template v-for="(item, index) in menuData" :key="index">
                    <div class="m-sider-menu__item" @click="onMenuClick(item)"
                        :class="curMenuRoute === item.route || (curMenuRoute == '/home' && item.route == '/') ? 'active' : ''">
                        <icon :type="item.icon" />
                        <div class="">{{ item.title }}</div>
                    </div>
                </template>
            </div>
        </a-layout-sider>
        <a-layout class="m-layout-right">
            <a-layout-header class="m-layout-header">
                <template v-if="subMenuData != null">
                    <menu-unfold-outlined v-if="collapsed" class="trigger" @click="() => (collapsed = !collapsed)" />
                    <menu-fold-outlined v-else class="trigger" @click="() => (collapsed = !collapsed)" />
                </template>

                <MUserMenu></MUserMenu>
            </a-layout-header>

            <a-layout-content>
                <a-layout style="height: 100%;">
                    <a-layout-sider v-model:collapsed="collapsed" width="180" collapsedWidth="0"
                        style="background: #FEFEFE">
                        <div class="m-sub-menu" v-if="subMenuData && subMenuData.length > 0">
                            <template v-for="(sub, index) in subMenuData" :key="index">
                                <div class="m-sub-menu__item" :class="$route.name == sub.auth ? 'active' : ''"
                                    @click="onMenuClick(sub)">
                                    <icon :type="sub.icon" />
                                    <span>{{ sub.title }}</span>
                                </div>
                            </template>
                        </div>
                    </a-layout-sider>
                    <a-layout-content class="m-layout-main">
                        <div class="m-layout-content">
                            <router-view />
                        </div>
                    </a-layout-content>
                </a-layout>
            </a-layout-content>
        </a-layout>
    </a-layout>
</template>

<script setup lang="ts">
import {
    MenuUnfoldOutlined,
    MenuFoldOutlined
} from "@ant-design/icons-vue";
import { ref, watch, onMounted, computed } from "vue";
import { useRouter } from 'vue-router'
import { useSettingStoreWithOut } from '@/store/setting';
import { useAdminStoreWithOut } from '@/store/admin';

const router = useRouter();
const adminStore = useAdminStoreWithOut();

const collapsed = ref(true);
const menuData: SiderMenu[] = adminStore.getMenu;
const curMenuRoute = ref<string>('/');

let subMenuData: any = <any>computed(() => {
    const curRoutePath = <string>router.currentRoute.value.path;
    let subData: SiderMenu[] = [];
    if (menuData && menuData.length > 0) {
        menuData.forEach((item: any) => {
            item.children.forEach((sub: any) => {
                if (sub.route == curRoutePath) {
                    curMenuRoute.value = item.route;
                    subData = <SiderMenu[]>item.children;
                    return;
                }
            });
        })
    }
    if (subData == null || subData.length == 0) {
        collapsed.value = true;
    } else {
        collapsed.value = false;
    }
    return subData;
});

const onMenuClick = (item: any) => {
    if (item.route) {
        curMenuRoute.value = item.route;
        router.push(item.route)
    }
}

onMounted(() => {
    document.body.className = 'm-base-page';
});                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </script>

<style>
@import "@/style/layout.css";
</style>
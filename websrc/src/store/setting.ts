import storage from "@/maike/storage";
import { store } from "./";
import { defineStore } from "pinia";

const SC_KEY = '_sider_collapsed_';

interface settingState {
    siderCollapsed?: boolean;  //左侧菜单栏显示状态
}

export const useSettingStore = defineStore({
    id: "app-setting",
    state: (): settingState => ({
        siderCollapsed: storage.get(SC_KEY, false)
    }),
    getters: {
        getSiderCollapsed(): boolean {
            return this.siderCollapsed || storage.get(SC_KEY, false);
        },
    },
    actions: {
        setSiderCollapsed(value: boolean) {
            this.siderCollapsed = value;
            storage.set(SC_KEY, value);
        }
    },
});

export function useSettingStoreWithOut() {
    return useSettingStore(store);
}

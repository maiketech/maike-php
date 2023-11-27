import { defineStore } from "pinia";
import { store } from ".";
import { router } from "@/router";
import storage from "@/maike/storage";
import request from "@/maike/request";

const CACHE_PREFIX = {
  adminInfo: "admininfo",
  token: "token",
  tokenExpire: "tokenExpire",
  authList: "authList",
  menu: "menu"
}

interface AdminState {
  adminInfo: AdminInfo | null;
  token?: string;
  tokenExpire?: number;
  authList?: any;
  menu?: any;
}

interface LoginParams {
  username?: string;
  password?: string;
  captcha?: string;
}

export const useAdminStore = defineStore({
  id: "app-admin",
  state: (): AdminState => ({
    adminInfo: null,
    token: undefined,
    tokenExpire: 0,
    authList: [],
    menu: null
  }),
  getters: {
    getAdminInfo(): AdminInfo {
      return this.adminInfo || <AdminInfo>storage.get(CACHE_PREFIX.adminInfo) || {};
    },
    getToken(): string {
      return this.token || <string>storage.get(CACHE_PREFIX.token);
    },
    getTokenExpire(): number {
      return this.tokenExpire || <number>storage.get(CACHE_PREFIX.tokenExpire);
    },
    getAuthList(): string[] {
      return this.authList.length > 0
        ? this.authList
        : <string[]>storage.get(CACHE_PREFIX.authList, []);
    },
    getMenu(): any {
      return this.menu != null ? this.menu : storage.get(CACHE_PREFIX.menu);
    }
  },
  actions: {
    setAdminInfo(info: AdminInfo | null, expire: number = 86400) {
      this.adminInfo = info;
      storage.set(CACHE_PREFIX.adminInfo, info, expire);
    },
    setToken(token: string | null, expire: number = 86400) {
      this.token = token ? token : "";
      storage.set(CACHE_PREFIX.token, token, expire);
    },
    setTokenExpire(tokenExpire: number | null, expire: number = 86400) {
      this.tokenExpire = tokenExpire ? tokenExpire : 0;
      storage.set(CACHE_PREFIX.tokenExpire, tokenExpire, expire);
    },
    setAuthList(authList: string[], expire: number = 86400) {
      this.authList = authList;
      storage.set(CACHE_PREFIX.authList, authList, expire);
    },
    setMenu(menu: any | null, expire: number = 86400) {
      this.menu = menu;
      storage.set(CACHE_PREFIX.menu, menu, expire);
    },
    resetState() {
      this.adminInfo = null;
      this.token = "";
      this.tokenExpire = 0;
      this.authList = [];
      this.menu = null;
    },

    // 登录
    login(params: LoginParams) {
      return new Promise((resolve, reject) => {
        request
          .post("/login", params)
          .then((res: any) => {
            const result: any = res.data;
            const { token, tokenExpire, adminInfo, authList, menu } = result;
            this.setToken(token, tokenExpire);
            this.setAdminInfo(adminInfo, tokenExpire);
            this.setTokenExpire(tokenExpire, tokenExpire);
            this.setAuthList(authList, tokenExpire);
            this.setMenu(menu, tokenExpire);
            resolve(result);
          })
          .catch((error: any) => {
            reject(error);
          });
      });
    },

    // 登出
    async logout(goLogin = false) {
      this.setToken(null);
      this.setAdminInfo(null);
      this.setAuthList([]);
      this.setMenu(null);
      goLogin && router.push("/login");
    },
  },
});

export function useAdminStoreWithOut() {
  return useAdminStore(store);
}

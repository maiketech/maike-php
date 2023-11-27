/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_APP_TITLE: string,
    readonly VITE_BASE_PATH: string,
    readonly VITE_API_URL: string,
    readonly VITE_CACHE_PREFIX: string,
    readonly VITE_CACHE_EXPIRE: number,
    readonly TOKEN_KEY: string,
    readonly STATUS_CODE_NOT_LOGIN: string,
    readonly STATUS_CODE_PERMISSION: string,
    readonly STATUS_CODE_SUCCESS: string
}

interface ImportMeta {
    readonly env: ImportMetaEnv
}

type Recordable<T = any> = Record<string, T>

interface AdminInfo {
    admin_id: number;
    role_id: number;
    username?: string;
    name?: string;
    avatar?: string;
    desc?: string;
    is_super?: number;
}

interface SiderMenu {
    key: string | number;
    title: string;
    icon?: string;
    route: string;
    children?: SiderMenu[];
}

interface FormItem {
    label: string,
    name: string,
    type: string,
    placeholder?: string | string[],
    disabled?: boolean,
    options?: any,
    value?: any,
    rules?: any,
    help?: string
    style?: any,
    mode?: string
    show?: any, //条件显示（表单字段值）
    relation?: any, //级联（关联表单字段值为查询参数）
    params?: any // 查询参数透传
}
import axios, { AxiosResponse } from 'axios';
import { message } from 'ant-design-vue';
import { isUrl } from '@/maike';
import { useAdminStoreWithOut } from "@/store/admin";

const STATUS_CODE_SUCCESS = import.meta.env.VITE_STATUS_SUCCESS;
const STATUS_CODE_PERMISSION = import.meta.env.VITE_STATUS_PERMISSION;
const STATUS_CODE_NOT_LOGIN = import.meta.env.VITE_STATUS_NOT_LOGIN;
const TOKEN_KEY = import.meta.env.VITE_TOKEN_KEY || 'M-Token';

const instance = axios.create({
    baseURL: process.env.NODE_ENV === "development" ? '/api' : process.env.VITE_API_URL,
    timeout: 60000, // request timeout,
    headers: {
        'Content-Type': 'application/json;charset=utf-8'
    }
})

// 添加请求拦截器
instance.interceptors.request.use((config: any) => {
    const adminStore = useAdminStoreWithOut();
    const token: string = adminStore.getToken;
    if ((!token || token == '') && config.url != '/login') {
        return Promise.reject();
    }
    if (config.url != '' && isUrl(config.url)) {
        config.baseURL = '';
    } else {
        config.headers[TOKEN_KEY] = token;
    }
    return config;
}, function (error) {
    // 对请求错误做些什么
    return Promise.reject(error);
});

// 添加响应拦截器
instance.interceptors.response.use((response: AxiosResponse) => {
    if (response.status == 200) {
        const result: any = response.data
        if (result.code == STATUS_CODE_SUCCESS || result.code == 204) {
            //业务请求处理成功，code:204拉流请求
            return result;
        } else if (result.code == STATUS_CODE_PERMISSION) {
            //无权限
            return Promise.reject(result);
        } else if (result.code == STATUS_CODE_NOT_LOGIN) {
            //未登录或登录过期、跳转至登录页面
            //message.error('请登录');
            window.location.href = import.meta.env.VITE_BASE_PATH + '/#/login';
            //return Promise.reject(result)
        } else {
            //业务请求处理错误
            return Promise.reject(result)
        }
    }
}, (error: any) => {
    message.error('无法连接服务器，请检查网络是否正常');
    return Promise.reject(error);
});

export default instance
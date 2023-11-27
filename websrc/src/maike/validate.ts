import { isArray, isEmpty } from "lodash-es"

export const checkMobile = (rule: string, value: any) => {
    if (value == '' || value == undefined) {
        return Promise.reject('请填写手机号码');
    } else if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(value))) {
        return Promise.reject('手机号码错误');
    }
    return Promise.resolve();
};

export const checkPassword = (rule: any, value: any) => {
    if (value == '' || value == undefined) {
        return Promise.resolve();
    } else if (value.length < 8) {
        return Promise.reject('密码至少填写8个字符');
    } else {
        return Promise.resolve();
    }
};

export const checkId = (rule: any, value: any) => {
    if (parseInt(value) > 0) {
        return Promise.resolve();
    } else {
        return Promise.reject(rule.message);
    }
};

export const checkAction = (rule: any, value: any) => {
    if (isArray(value) && !isEmpty(value)) {
        return Promise.resolve();
    } else {
        return Promise.reject('请设置权限');
    }
};
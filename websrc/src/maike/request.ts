import axios from './axios';
import { isArray } from 'lodash-es';

export const Request = {
    post(url: string, params: any, headers: any = {}) {
        return axios({
            url: url,
            method: 'post',
            headers: headers ?? { 'Content-Type': 'application/json;charset=utf-8' },
            data: params
        })
    },

    get(url: string, params: any = null, headers: any = {}) {
        return axios({
            url: url,
            method: 'get',
            headers: headers ?? { 'Content-Type': 'application/json;charset=utf-8' },
            data: params
        })
    },

    upload(url: string, params: any = null, field: string = 'image') {
        var data = new FormData();
        if (isArray(params)) {
            params.forEach((item: any) => data.append(field, item.file));
        } else {
            data.append(field, params);
        }
        return axios.post(url, data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    }
}

export default Request;
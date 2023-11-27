<template>
    <a-config-provider :theme="{
        token: {
            borderRadius: '20px'
        },
    }">
        <div class="login-logo" />
        <div class="login-main">
            <a-form class="user-layout-login" :model="formData" :rules="formRules" @finish="handleLogin">
                <div class="title">管理登录</div>
                <a-form-item name="username">
                    <a-input size="large" type="text" placeholder="用户名/手机号" v-model:value="formData.username">
                        <template #prefix>
                            <UserOutlined :style="{ color: 'rgba(0,0,0,.25)' }" />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item name="password">
                    <a-input size="large" type="password" autocomplete="false" placeholder="密码"
                        v-model:value="formData.password">
                        <template #prefix>
                            <LockOutlined :style="{ color: 'rgba(0,0,0,.25)' }" />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item name="captcha">
                    <a-input size="large" type="text" placeholder="验证码" v-model:value="formData.captcha"
                        style="ime-mode: disabled">
                        <template #prefix>
                            <SafetyCertificateOutlined :style="{ color: 'rgba(0,0,0,.25)' }" />
                        </template>
                        <template #addonAfter>
                            <img :src="captchaUrl" class="captcha" ref="captcha" @click="reLoadCaptcha" />
                        </template>
                    </a-input>
                </a-form-item>

                <a-form-item style="margin-top:18px">
                    <a-button block shape="round" size="large" type="primary" html-type="submit" :loading="loading"
                        :disabled="loading">登录系统</a-button>
                </a-form-item>
            </a-form>
        </div>
        <div class="copyright">© {{ SYSTEM_NAME }}</div>
    </a-config-provider>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { notification, message } from 'ant-design-vue';
import { UserOutlined, LockOutlined, SafetyCertificateOutlined } from '@ant-design/icons-vue';
import type { FormInstance } from 'ant-design-vue';
import { useAdminStoreWithOut } from '@/store/admin';

const SYSTEM_NAME = import.meta.env.VITE_APP_TITLE;
//window.document.title = SYSTEM_NAME;

let router = useRouter();
const adminStore = useAdminStoreWithOut();
const loading = ref(false);
// 登录表单
interface FormState {
    username: string;
    password: string;
    captcha: string;
}
const formRef = ref<FormInstance>();
const formData = reactive<FormState>({
    username: '',
    password: '',
    captcha: ''
});
const formRules = {
    username: [{
        required: true,
        message: "请填写用户名/手机号",
        trigger: "blur"
    }],
    password: [{
        required: true,
        message: "请填写密码",
        trigger: "blur"
    }],
    captcha: [{
        required: true,
        message: "请填写验证码",
        trigger: "blur"
    }]
}

// 验证码
const captchaUrl = ref<string>("");
const getCaptchaUrl = () => {
    return (process.env.NODE_ENV === "development" ? '/api' : '') + "/captcha?t=" + (Math.random() * 10).toString()
};
const reLoadCaptcha = () => {
    captchaUrl.value = getCaptchaUrl();
    formData.captcha = '';
};

const handleLogin = (values: FormState) => {
    loading.value = true;
    adminStore.login(values).then((res: any) => {
        message.success('登录成功', 4);
        router.push("/");
    }).catch((error: any) => {
        notification.error({
            message: '登录失败',
            description: error?.msg || ''
        })
        reLoadCaptcha();
    }).then(() => {
        setTimeout(() => {
            loading.value = false;
        }, 3000);
    });
};

onMounted(() => {
    captchaUrl.value = getCaptchaUrl();
})
</script>

<style scoped>
@import "@/style/base.css";
@import "@/style/login.css";
</style>
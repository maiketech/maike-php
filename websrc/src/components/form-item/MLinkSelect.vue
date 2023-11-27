<template>
    <div class="m-link-select" @click="onShowModal">{{ linkDesc == '' ? placeholder : linkDesc }}</div>

    <MLinkModal v-model:show="modalShow" v-model:value="linkData" @close="onCloseModal" @set="onSetDone"></MLinkModal>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';

const emit = defineEmits(["update:value", "change"]);
//组件属性
interface Props {
    value?: any,
    placeholder?: string
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    placeholder: '设置链接'
});

const modalShow = ref<boolean>(false);
//linkData：类型|链接及参数|appid|重定向（1/0，类型为page时有效）
const linkData = ref<string>('');
const linkDesc = ref<string>('');

const onSetDone = (data: any) => {
    linkData.value = data.value;
    linkDesc.value = data.desc;
    emit("change", data.value)
    emit("update:value", data.value)
}

const onShowModal = () => {
    modalShow.value = true;
}

const onCloseModal = () => {
    modalShow.value = false;
}

const parseUrl = (url: string) => {
    //value：类型|链接及参数|appid|重定向（1/0，类型为page时有效）
    if (!url || url == '' || url == undefined) {
        linkData.value = '';
        linkDesc.value = '设置链接';
    }
    let urlArr = url.split("|");
    let desc = '';
    linkData.value = url;
    if (urlArr[0] == 'page') {
        desc = '小程序页面（' + urlArr[1] + '）';
    } else if (urlArr[0] == 'app') {
        desc = '跳转小程序（' + urlArr[1] + '|' + urlArr[2] + '）';

    } else if (urlArr[0] == 'web') {
        desc = '外部链接（' + urlArr[1] + '）';
    }
    linkDesc.value = desc;
}

watch(
    () => props.value,
    val => {
        parseUrl(val);
    }
);
</script>

<style lang="less">
.m-link-select {
    color: #14AFAC;
    cursor: pointer;
    display: inline-block;
}
</style>
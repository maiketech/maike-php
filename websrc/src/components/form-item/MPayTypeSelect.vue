<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="optionsData" @change="onChange"></a-select>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { isEmpty, isNull, isArray } from '@/utils/is';

const emit = defineEmits(["update:value", "change"]);
//组件属性
interface Props {
    value?: any,
    style?: any,
    placeholder?: string,
    disabled?: boolean,
    options?: any
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    style: {},
    placeholder: "请选择",
    disabled: false,
    options: null
})

interface SelectOption {
    value: any,
    label: string
}
const loading = ref<boolean>(true);
//支付方式：wechat微信支付、 alipay支付宝、cash现金、offline_wechat线下微信、offline_alipay线下支付宝、other其他
const optionsData = ref<SelectOption[]>([
    { label: '线下微信', value: 'offline_wechat' },
    { label: '线下支付宝', value: 'offline_alipay' },
    { label: '现金', value: 'cash' },
    { label: '其他', value: 'other' }
]);

const onChange = (value: any) => {
    emit("change", value);
    emit("update:value", value);
}

onMounted(() => {
    let opt = optionsData.value;
    if (!isNull(props.options) && !isEmpty(props.options) && isArray(props.options)) {
        opt = [...props.options, ...opt];
        optionsData.value = opt;
    }

    setTimeout(() => {
        loading.value = false;
    }, 2000);
});
</script>
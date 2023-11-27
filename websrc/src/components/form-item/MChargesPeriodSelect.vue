<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="optionsData" @change="onChange"></a-select>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { isEmpty, isNull, isArray } from '@/utils/is';

const emit = defineEmits(["update:value","change"]);
//组件属性
interface Props {
    value?: string,
    style?: any,
    placeholder?: string,
    disabled?: boolean,
    options?: any
}
const props = withDefaults(defineProps<Props>(), {
    value: 'AM',
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
//计费周期：CM自然月，AM实际月，BT指定周期，CY自然年，AY实际年，D天，NONE一次性
const optionsData = ref<SelectOption[]>([
    { value: 'NONE', label: '一次性' },
    { value: 'CM', label: '自然月' },
    { value: 'AM', label: '实际月' },
    { value: 'BT', label: '指定周期' },
    { value: 'CY', label: '自然年' },
    { value: 'AY', label: '实际年' },
    { value: 'D', label: '天' }
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
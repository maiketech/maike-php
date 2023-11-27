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
//计算公式：0单价，1店铺面积x单价，2单价x数量，3电费（单价x用电量），4水费（单价x用水量
const optionsData = ref<SelectOption[]>([
    { value: 0, label: '单价' },
    { value: 1, label: '店铺面积x单价' },
    { value: 2, label: '单价x数量' },
    { value: 3, label: '电费（单价x用电量）' },
    { value: 4, label: '水费（单价x用水量' }
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
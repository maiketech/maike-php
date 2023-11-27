<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="options" :show-arrow="false" :filter-option="false" :not-found-content="null"
        :default-active-first-option="false" show-search @change="onChange" @search="onSearch"></a-select>
</template>



<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import req from '@/utils/request';
import { isEmpty, isNull, isArray, isObjectEq } from '@/utils/is';

const emit = defineEmits(["update:value", "change"]);
//组件属性
interface Props {
    value?: any,
    params?: any,
    style?: any,
    placeholder?: string,
    disabled?: boolean,
    options?: any
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    params: {},
    style: {},
    placeholder: "选择缴费项目",
    disabled: false,
    options: null
})

interface SelectOption {
    value: any,
    label: string
}
const loading = ref<boolean>(true);
const options = ref<SelectOption[]>([]);
const paramsData = ref<any>({});

const initData = (keyword: string = '') => {
    let params = {
        ...paramsData.value,
        keyword: keyword
    }
    req.post("/fee.charges/get_options", params).then((res) => {
        let opt = res.data;
        if (!isNull(props.options) && !isEmpty(props.options) && isArray(props.options)) {
            opt = [...props.options, ...opt];
        }
        options.value = opt;
    }).finally(() => {
        loading.value = false;
    });
}

watch(
    () => props.params,
    val => {
        paramsData.value = val;
        initData("");
    },
    { deep: true, immediate: true }
);

const onSearch = (val: string) => {
    initData(val);
};

const onChange = (value: any) => {
    emit("change", value);
    emit("update:value", value);
}

//页面
onMounted(() => {
    paramsData.value = props.params;
    initData();
});
</script>
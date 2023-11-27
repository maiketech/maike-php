<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="options" :mode="mode == 'm' ? 'multiple' : (mode == 't' ? 'tags' : 'combobox')" :show-arrow="false"
        :filter-option="filterOption" allowClear show-search @change="onChange"></a-select>
</template>



<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import type { SelectProps } from 'ant-design-vue';
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
    options?: any,
    mode?: 'm' | 't' | 's' //m多选，t:tag，s单选
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    params: {},
    style: {},
    placeholder: "选择商铺",
    disabled: false,
    options: null,
    mode: 's'
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
    req.post("/wuye.store/get_options", params).then((res) => {
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

const filterOption = (input: string, option: any) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

// const onSearch = (val: string) => {
//     initData(val);
// };

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
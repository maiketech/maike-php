<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="options" :mode="mode == 'm' ? 'multiple' : (mode == 't' ? 'tags' : 'combobox')"
        @change="onChange"></a-select>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import req from '@/utils/request';
import { isEmpty, isNull, isArray } from '@/utils/is';

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
    placeholder: "请选择",
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

const setOptions = (opt: any = []) => {
    if (!isNull(props.options) && !isEmpty(props.options) && isArray(props.options)) {
        if (!isNull(opt) && !isEmpty(opt) && isArray(opt)) {
            opt = [...props.options, ...opt];
        } else {
            opt = [...props.options];
        }
    }
    options.value = opt;
}

const initData = () => {
    let params: any = {
        ...props.params
    };
    req.post("/system.dict/get_item_options", params).then((res) => {
        setOptions(res.data);
    }).finally(() => {
        loading.value = false;
    });
}

const onChange = (value: any) => {
    emit("change", value);
    emit("update:value", value);
}

//页面
onMounted(() => {
    initData();
});
</script>
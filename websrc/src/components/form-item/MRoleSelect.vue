<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="options" @change="onChange"></a-select>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import req from '@/utils/request';
import { isEmpty, isNull, isArray } from '@/utils/is';

const emit = defineEmits(["update:value","change"]);
//组件属性
interface Props {
    value?: any,
    style?: any,
    placeholder?: string,
    options?: any,
    disabled?: boolean,
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    style: {},
    placeholder: "选择角色",
    disabled: false,
    options: null,
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
        }
    } else {
        opt = [...props.options];
    }
    options.value = opt;
}

const initData = () => {
    req.post("/system.admin_role/get_options", {}).then((res) => {
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
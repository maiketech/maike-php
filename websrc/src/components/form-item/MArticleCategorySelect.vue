<template>
    <a-select ref="select" :value="value" :style="style" :placeholder="placeholder" :disabled="disabled" :loading="loading"
        :options="options" @change="onChange"></a-select>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import req from '@/utils/request';
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
    placeholder: "选择分类",
    disabled: false,
    options: null
})

interface SelectOption {
    value: any,
    label: string
}
const loading = ref<boolean>(true);
const options = ref<SelectOption[]>([]);

const initData = () => {
    req.post("/content.article_category/get_options", {}).then((res) => {
        let opt = res.data;
        if (!isNull(props.options) && !isEmpty(props.options) && isArray(props.options)) {
            opt = [...props.options, ...opt];
        }
        options.value = opt;
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
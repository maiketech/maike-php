<template>
    <a-tree :show-line="true" :checkable="true" :checkedKeys="value" :tree-data="options" @check="onChange"
        :style="style ? style : defaultStyle" :multiple="true" class="m-action-tree">
    </a-tree>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import type { TreeProps } from 'ant-design-vue';
import req from '@/utils/request';

const emit = defineEmits(["update:value","change"]);
//组件属性
interface Props {
    value?: any,
    style?: any,
    placeholder?: string,
    disabled?: boolean,
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    style: null,
    placeholder: "选择权限",
    disabled: false,
})

const loading = ref<boolean>(true);
const options = ref<TreeProps['treeData']>([]);

const defaultStyle = ref({
    height: '200px',
    overflowX: 'hidden',
    overflowY: 'auto',
    border: '1px solid #d9d9d9',
    borderRadius: '3px'
})

const initData = () => {
    req.post("/system.action/get_options", {}).then((res) => {
        options.value = res.data;
    }).finally(() => {
        loading.value = false;
    });
}

const onChange = (value: any) => {
    console.log("onChange tree", value)
    emit("change", value);
    emit("update:value", value);
}

//页面
onMounted(() => {
    initData();
});
</script>

<style lang="less">
.m-action-tree {
    border: 1px solid #D9D9D9;
    background-color: #FAFAFA;
    padding: 8px;
    box-sizing: border-box;
}
</style>
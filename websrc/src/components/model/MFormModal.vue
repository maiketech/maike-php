<!--
 * @LastEditTime: 2023-08-08 15:48:31
-->
<template>
    <a-modal :visible="show" :title="title" :confirmLoading="loading" :maskClosable="false"
        :cancelButtonProps="{ props: { disabled: loading } }" :okText="okText" :cancelText="cancelText" :width="width"
        @ok="onModalOk" @cancel="onModalCancel" :destroyOnClose="destroyOnClose">
        <div class="m-form-wrap">
            <MForm ref="formRef" :loading="loading" width="auto" :height="height" :showButton="false" :layout="layout"
                :label-span="labelSpan" :wrapper-span="wrapperSpan" :items="items" v-model:value="values"
                @submit="onFormSubmit">
            </MForm>
        </div>
    </a-modal>
</template>

<script lang="ts" setup>
import { ref, reactive, watch } from 'vue';
import { isNumber, isObjectEq } from '@/utils/is';

const emit = defineEmits(["submit", "update:show", "update:value"]);

//组件属性
interface Props {
    show: boolean,
    title?: string,
    width?: (number | string),
    height?: (number | string),
    items?: any,
    value?: any,
    okText?: string,
    cancelText?: string,
    labelSpan?: any,
    wrapperSpan?: any,
    layout?: string,
    loading?: boolean,
	destroyOnClose?: boolean
}
const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '新增',
    width: 600,
    height: 'auto',
    items: null,
    value: null,
    okText: "确定",
    cancelText: "取消",
    labelSpan: { style: { width: '120px' } },
    wrapperSpan: { span: 19 },
    layout: 'h', //'h'|'v'|'hm'|'vm'|'i'
    loading: false,
	destroyOnClose: false
})

//变量
const values = ref<any>({});
const formRef = ref();

watch(
    () => props.value,
    val => {
        if (!isObjectEq(values.value, val)) {
            values.value = val;
        }
    },
    { deep: true, immediate: true }
);

watch(
    () => values.value,
    (val, old) => {
        if (!isObjectEq(old, val)) {
            emit("update:value", val);
        }
    },
    { deep: true, immediate: true }
);

const onFormSubmit = (values: any) => {
    emit("submit", values);
}
const onModalOk = () => {
    formRef.value.submit();
}

const onModalCancel = () => {
    emit("update:show", false);
}

</script>

<style lang="less">
</style>
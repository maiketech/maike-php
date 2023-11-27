<template>
    <a-modal :visible="show" :title="title" :confirmLoading="loading" :maskClosable="false" :width="width"
        @cancel="onCancel">
        <template #footer>
            <a-button type="primary" :disabled="(saveLoading || importDone)" :loading="saveLoading" @click="onSaveData">{{
                okText
            }}</a-button>
            <a-button class="ml20" :disabled="saveLoading" @click="onCancel">{{ cancelText }}</a-button>
            <a-button class="ml20" :disabled="saveLoading" @click="onDownTpl">
                <template #icon>
                    <download-outlined />
                </template>
                {{ downText }}
            </a-button>
        </template>
        <slot name="header"></slot>
        <a-steps size="small" class="mb20">
            <a-step title="下载模板" status="process" description="下载空白的导入模板" />
            <a-step title="填写信息" status="process" description="在空白模板中填写信息" />
            <a-step title="导入数据" status="process" description="选择导入填好的文件" />
            <a-step title="保存数据" status="process" description="提交保存数据" />
        </a-steps>

        <MForm ref="formRef" width="auto" :disabled="saveLoading" layout="i" :items="formItems" v-model:value="formValues"
            @submit="onFormSubmit" v-if="formItems && formItems != null" submitText="上传导入文件" resetText="清空"
            @reset="onFormReset">
        </MForm>

        <a-table class="mt20" size="small" bordered :dataSource="importData" :columns="fields"
            :scroll="{ y: '500px', x: 'max-content' }" :pagination="false">
            <template #bodyCell="{ text, column, index, record }">
                <template v-if="column.key === 'delete_action'">
                    <div class="m-action-group" v-if="!record.status">
                        <a-button size="small" danger class="action-btn" title="删除" :disabled="(saveLoading || importDone)"
                            @click="deleteRow(index)">
                            <DeleteOutlined />
                        </a-button>
                    </div>
                    <div v-if="(record.status && record.status != '')">
                        <a-tag color="#87d068" v-if="record.status == 'success'">成功</a-tag>
                        <a-tag color="red" v-if="record.status == 'fail'">失败</a-tag>
                        <a-tag color="orange" v-if="record.status == 'has'">跳过</a-tag>
                    </div>
                </template>
                <template v-if="($slots && $slots.tableCell)">
                    <slot name="tableCell" v-bind="{ text, column, index, record }"></slot>
                </template>
            </template>
        </a-table>
        <slot name="footer"></slot>
    </a-modal>
</template>

<script lang="ts" setup>
import { ref, reactive, watch } from 'vue';
import { DeleteOutlined, DownloadOutlined } from '@ant-design/icons-vue';
import { isNumber } from '@/utils/is';
import { importExcel, downloadByUrl, selectLocalFile } from '@/utils/file';
import req from '@/utils/request';

const emit = defineEmits(["success", "error", "update:show"]);

//组件属性
interface Props {
    show: boolean,
    title?: string,
    width?: (number | string),
    height?: (number | string),
    okText?: string,
    cancelText?: string,
    downText?: string,
    tplUrl: string,
    tplFileName?: string,
    loading?: boolean,
    fields?: any,
    formItems?: any,
    saveUrl: string,
}
const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '导入',
    width: 600,
    height: 'auto',
    okText: "确定保存数据",
    cancelText: "关闭",
    downText: "下载导入模板",
    tplUrl: '',
    tplFileName: '导入模板',
    loading: false,
    fields: null,
    formItems: null,
    saveUrl: ''
})

const importData = ref<any>([]);
const importDone = ref<boolean>(false);
const saveLoading = ref<boolean>(false);
const formRef = ref();
const formValues = ref<any>({});
const importTotal = ref<any>({});
const importTotalDesc = ref<string>('');

const onSaveData = (e: any) => {
    const formData = formValues.value;
    formData['data'] = importData.value;
    saveLoading.value = true;
    req.post(props.saveUrl, formData).then(res => {
        const result = res.data;
        importDone.value = true;
        importData.value = result.data;
        importTotal.value = result.total;
        importTotalDesc.value = result.total_desc;

        emit("success", result);
        setTimeout(() => {
            onFormReset();
        }, 1500)
    }).catch(err => {
        console.log(err)
        emit("error", err);
    }).finally(() => {
        setTimeout(() => {
            saveLoading.value = false;
        }, 1500)
    })
};

const deleteRow = (index: number) => {
    importData.value.splice(index, 1);
}

const onDownTpl = () => {
    if (props.tplUrl == '' || props.tplUrl == null) return;
    downloadByUrl(props.tplUrl, props.tplFileName);
}

const onFormSubmit = (values: any) => {
    selectLocalFile((files: any) => {
        importDone.value = false;
        importExcel(files[0]).then((data: any) => {
            data[0].shift(); //删除标题行
            importData.value = data[0];
        });
    })
}

const onFormReset = () => {
    formValues.value = [];
    importData.value = null;
}

const onCancel = () => {
    importData.value = null;
    formRef.value.reset();
    emit("update:show", false);
}

</script>

<style lang="less">
.m-import-input {
    position: absolute;
    left: 0;
    top: 5px;
    outline: none;
    filter: alpha(opacity=0);
    -moz-opacity: 0;
    -khtml-opacity: 0;
    opacity: 0;
    cursor: pointer;
}

.ant-steps {
    background-color: #FAFAFA !important;
    padding: 15px 10px !important;

    .ant-steps-item-description {
        font-size: 12px !important;
        color: #666 !important;
    }
}
</style>
<template>
    <a-modal :visible="show" :title="title" :maskClosable="false" :width="width" :footer="null" @cancel="onModalCancel">
        <a-button v-if="showCreateButton" style="margin-bottom: 8px;" type="primary" danger @click="onCreate">{{ createText
        }}</a-button>
        <MListTable v-if="show" ref="DataTable" :config="tableConfig" v-model:query="query" :autoload="true"
            :formatDataSource="formatDataSource">
            <template #table__action="{ value, row }">
                <a-button @click="onView(row)" size="small" type="link" title="查看"
                    v-if="showViewButton && !row.not_allow_view">查看</a-button>
                <a-button @click="onEdit(row)" size="small" type="link" title="编辑"
                    v-if="showEditButton && !row.not_allow_edit" :disabled="row.disabled_edit">编辑</a-button>
                <a-button @click="onDelete(row)" size="small" type="link" title="删除"
                    v-if="showDeleteButton && !row.not_allow_delete" :disabled="row.disabled_delete">删除</a-button>
                <slot name="table__action" v-bind="{ value, row }"></slot>
            </template>
            <template #cell="{ value, row, column }">
                <template v-for="(item, key, index) in $slots" :key="index">
                    <slot :name="key" v-bind="{ value, row, column }" v-if="column.key === key && key != 'action__btn'">
                    </slot>
                </template>
            </template>
        </MListTable>
    </a-modal>

    <MFormModal :title="modalTitle" :width="modalConfig.width || 500" :height="modalConfig.height || ''"
        :layout="modalConfig.layout || 'h'" v-model:show="formModalShow" :items="modalFormItem" v-model:value="formValues"
        :loading="actionLoading" :okText="modalConfig.okText" :cancelText="modalConfig.cancelText" @submit="onModalSubmit"
        :destroyOnClose="destroyOnClose"></MFormModal>
</template>

<script lang="ts" setup>
import { ref, watch, computed, onMounted } from 'vue';
import { Modal, message } from 'ant-design-vue';
import { isObjectEq, isArray } from '@/utils/is';
import req from '@/utils/request';

const emit = defineEmits(["update:show", "submit", "showDetail", "done"]);
//组件属性
interface Props {
    show: boolean,
    title?: string,
    width?: (number | string),
    height?: (number | string),
    okText?: string,
    cancelText?: string,
    tableConfig?: any,
    tableQuery?: any,
    tableRowButton?: any,
    createText?: string,
    modalConfig?: any,
    createFormConfig?: any,
    editFormConfig?: any,
    formatFormData?: any,
    formatEditData?: any,
    formatDataSource?: any,
    destroyOnClose?: boolean
}
const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '列表',
    width: 1000,
    height: 'auto',
    data: null,
    okText: "确定",
    cancelText: "取消",
    tableConfig: {},
    tableQuery: {},
    tableRowButton: ['create', 'edit', 'delete'],
    createText: '新增',
    modalConfig: {
        title: '',
        width: 500,
        height: 460,
        layout: 'h'
    },
    createFormConfig: [],
    editFormConfig: [],
    formatFormData: null,
    formatEditData: null,
    formatDataSource: null,
    destroyOnClose: true
})

const onModalCancel = () => {
    emit("update:show", false);
}

//表格
const DataTable = ref();
const query = ref<any>({});
//新增、编辑
const actionLoading = ref<boolean>(false);
const formModalShow = ref<boolean>(false);
const formIsCreate = ref<number>(1);
const formValues = ref<any>({});
const pkValue = ref<any>(null);
const modalTitle = computed(() => {
    return (formIsCreate.value == 1 ? '新增' : '编辑') + props.modalConfig.title;
})
const modalFormItem = computed(() => {
    return formIsCreate.value == 1 ? props.createFormConfig : props.editFormConfig;
})
//查看详情
const detailModalShow = ref<boolean>(false);
const detail = ref<any>({});

const hasCreateFormConfig = computed(() => {
    if (!props.createFormConfig || !isArray(props.createFormConfig) || props.createFormConfig.length == 0) {
        return false;
    }
    return true;
})
const hasEditFormConfig = computed(() => {
    if (!props.editFormConfig || !isArray(props.editFormConfig) || props.editFormConfig.length == 0) {
        return false;
    }
    return true;
})

const showCreateButton = computed(() => {
    const showAction = props.tableRowButton || [];
    return showAction && showAction.length > 0 && showAction.includes("create");
});
const showViewButton = computed(() => {
    const showAction = props.tableRowButton || [];
    return showAction && showAction.length > 0 && showAction.includes("view");
});
const showEditButton = computed(() => {
    const showAction = props.tableRowButton || [];
    return showAction && showAction.length > 0 && showAction.includes("edit");
});
const showDeleteButton = computed(() => {
    const showAction = props.tableRowButton || [];
    return showAction && showAction.length > 0 && showAction.includes("delete");
});

//打开新增
const onCreate = () => {
    if (!hasCreateFormConfig) {
        Modal.error({
            title: '无效配置',
            content: '请配置新增表单配置参数',
        });
        return;
    }
    pkValue.value = null;
    formValues.value = {};
    formIsCreate.value = 1;
    formModalShow.value = true;
}

//打开编辑
const onEdit = (record: any) => {
    if (!hasEditFormConfig) {
        Modal.error({
            title: '无效配置',
            content: '请配置编辑表单配置参数',
        });
        return;
    }
    const { pk } = props.tableConfig;
    pkValue.value = record[pk];
    formValues.value = {};
    // 如果需要格式化数据（如果需要通过接口线获取detail数据）
    /**
     * 处理方法
     * @param function (row, callback) {}
     * 通过callback回传处理好的数据
     * @callback (res) {}
     */
    if (props.formatEditData) {
        props.formatEditData(record, (res: any) => {
            formValues.value = res;
        });
    }
    // 正常获取行内数据
    else formValues.value = record;
    formIsCreate.value = 0;
    formModalShow.value = true;
}

//打开详情
const onView = (record: any) => {
    // detail.value = { ...record };
    // detailModalShow.value = true;
    emit("showDetail", record)
}

//保存新增/编辑信息
const onModalSubmit = (values: any) => {
    const { pk, model } = props.tableConfig;
    values[pk] = pkValue.value;
    let url = '/' + model + (formIsCreate.value !== 1 ? '/update' : '/create');

    if (formIsCreate.value == 1 && props.modalConfig.createUrl) url = props.modalConfig.createUrl;
    if (formIsCreate.value !== 1 && props.modalConfig.editUrl) url = props.modalConfig.editUrl;

    // 如果需要格式化数据（提交前格式化）
    if (props.formatFormData) {
        let data = props.formatFormData(values);
        values = data;
    }
    values = { ...values, ...query.value };
    actionLoading.value = true;
    req.post(url, values).then((res: any) => {
        //刷新列表
        DataTable.value.refresh();
        //提示
        message.success(res.msg ? res.msg : '操作成功', 2, () => {
            formModalShow.value = false;
            actionLoading.value = false
        });
    }).catch((err: any) => {
        message.error(err.msg ? err.msg : '操作失败');
        actionLoading.value = false
    }).finally(() => {
        //actionLoading.value = false
    })
}

//删除
const onDelete = (row: any) => {
    Modal.confirm({
        title: '是否删除所选择的?',
        content: '删除后无法恢复',
        okText: '确定删除',
        okType: 'danger',
        cancelText: '取消',
        async onOk() {
            try {
                const { pk, model } = props.tableConfig;
                const pkValue = row && row.hasOwnProperty(pk) ? row[pk] : null;
                if (!pkValue || pkValue == null) {
                    message.error("请选择需要删除的数据");
                    return false;
                }
                const url = '/' + model + '/delete';
                const values = {
                    [pk]: pkValue
                }
                actionLoading.value = true;
                return await req.post(url, values).then((res: any) => {
                    //刷新列表
                    DataTable.value.refresh();
                    message.success(res.msg ? res.msg : '删除成功');
                }).catch((err: any) => {
                    message.error(err.msg ? err.msg : '删除失败');
                }).finally(() => {
                    actionLoading.value = false;
                })
            } catch (e) {
            }
        }
    });
};

//监听父组件的值
watch(
    () => props.tableQuery,
    (val, old) => {
        if (val != undefined && !isObjectEq(val, old)) {
            query.value = val;
        }
    },
    { deep: true, immediate: true }
);

const refresh = () => {
    DataTable.value && DataTable.value.refresh();
}

// 暴露方法
defineExpose({ refresh });
</script>
<template>
    <div class="m-list-table">
        <div class="m-list-table__inner">
            <div class="m-list-table__filter" v-if="config.form && config.form != null">
                <MForm layout="i" submitText="查询" :items="config.form" @submit="onTableQuerySubmit"
                    @reset="onTableQueryReset">
                </MForm>
            </div>
            <div class="m-list-table__toolber" v-if="config.batch || config.export || config.tabs">
                <a-row type="flex">
                    <a-col flex="auto">
                        <a-dropdown class="mr30" :disabled="!hasSelected" v-if="config.batch && config.batch != null">
                            <template #overlay>
                                <a-menu @click="onBatchMenuClick">
                                    <a-menu-item v-for="(menu, index) in config.batch" :key="index" :data="menu">{{
                                        menu.title
                                    }}</a-menu-item>
                                </a-menu>
                            </template>
                            <a-button size="small">{{ hasSelected ? `(${selectedRowKeys.length}) ` : ''
                            }}{{ config.batchText }}
                                <DownOutlined />
                            </a-button>
                        </a-dropdown>

                        <template v-for="(tab, index) in config.tabs">
                            <a-radio-group :default-value="tab.value" button-style="solid" size="small" :disabled="loading"
                                :value="tableQuery[tab.field]" v-if="tab.options" :key="index"
                                @change="onTabsChange($event, tab.field)" class="mr30">
                                <a-radio-button v-for="item in tab.options" :value="item.value" :key="item.value">
                                    {{ item.label }}
                                </a-radio-button>
                            </a-radio-group>
                        </template>
                    </a-col>
                    <a-col flex="auto" class="tr">
                        <a-dropdown v-if="config.export"
                            :disabled="!dataSource || dataSource.length == 0 || loading || actionLoading">
                            <template #overlay>
                                <a-menu @click="onExportClick">
                                    <a-menu-item v-for="(menu, index) in config.export" :key="index" :data="menu">{{
                                        menu.title
                                    }}</a-menu-item>
                                </a-menu>
                            </template>
                            <a-button size="small" :loading="actionLoading">
                                <template #icon>
                                    <DownloadOutlined />
                                </template>
                                {{ config.exportText }}
                                <DownOutlined />
                            </a-button>
                        </a-dropdown>
                    </a-col>
                </a-row>
            </div>
            <a-table class="m-list-table__table" sticky :scroll="{ scrollToFirstRowOnChange: true, x: 'max-content' }"
                :rowKey="config.pk" :columns="config.columns" :data-source="dataSource" :row-selection="config.batch ? {
                    selectedRowKeys: selectedRowKeys,
                    onChange: onRowSelectChange,
                } : null" :pagination="pagination" :size="size" :bordered="showBorder" :loading="loading"
                defaultExpandAllRows @change="onTableChange">
                <template #bodyCell="{ record, column }">
                    <view class="m-list-table__action" v-if="column.key == 'table__action' && $slots.table__action">
                        <slot name="table__action" v-bind="{ value: record[column.dataIndex], row: record, column }">
                        </slot>
                    </view>
                    <slot name="cell" v-bind="{ value: record[column.dataIndex], row: record, column }">
                        <template v-for="(item, key, index) in $slots" :key="index">
                            <slot :name="key" v-if="column.key === key && key != 'table__action'"
                                v-bind="{ value: record[column.dataIndex], row: record, column }">
                            </slot>
                        </template>
                    </slot>
                </template>
                <slot></slot>
            </a-table>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, watch, computed, onMounted } from 'vue';
import { DownOutlined, DownloadOutlined, FormOutlined, DeleteOutlined } from '@ant-design/icons-vue';
import { Modal, message } from 'ant-design-vue';
import { isArray, isObject, isObjectEq, isNumber } from '@/utils/is';
import { download, exportExcel, columns2XlsxHeader } from '@/utils/file';
import { proxyTo } from '@/utils';
import req from '@/utils/request';
import appConfig from '@/config/app';

// update: 需要双向绑定的属性名 
const emit = defineEmits(["update:query", "export", "batch"])



//组件属性
interface Props {
    config?: any,
    showBorder?: boolean,
    size?: string,
    query?: any, //表格查询参数
    autoload?: boolean,
	formatDataSource?: any
}
const props = withDefaults(defineProps<Props>(), {
    config: {
        tabs: null, //标签按钮
        batch: {}, //批量操作下拉菜单
        batchText: '批量操作', //批量操作按钮文字
        export: {}, //导出下拉菜单
        exportText: '导出',//导出下拉菜单文字
        model: '',
		customListAction: '', //自定义列表后缀
        pk: 'id',
        pagina: true,
        columns: [],
        form: [] //查询表单
    },
    showBorder: true,
    size: 'small',
    query: {},
    autoload: true,
	formatDataSource: null
})

type Key = string | number;
const loading = ref<boolean>(false);
const actionLoading = ref<boolean>(false);
const defaultTableQuery = ref<any>({});
const tableQuery = ref<any>({});
const dataSource = ref();
const pagination = ref<any>({
    total: 0,
    current: 1,
    defaultPageSize: 15,
    pageSizeOptions: ["15", "30", "50", "100"],
    showQuickJumper: true,
    showSizeChanger: true,
    showTotal: (total: number, range: any) => { return `本页：${range[0]}-${range[1]}，总计：${total}` }
});
const selectedRowKeys = ref<Key[]>([]);
const hasSelected = computed(() => selectedRowKeys.value.length > 0);

//属性初始化处理
const initProps = () => {
    const { tabs, form, config, query } = props.config;
    //查询参数
    let params: any = {}
    if (isArray(tabs) || isObject(tabs)) {
        tabs.map((item: any) => {
            params[item.field] = item.value;
        });
    }
    if (isArray(form) || isObject(form)) {
        form.map((row: any) => {
            row.map((item: any) => {
                params[item.name] = item.value;
            });
        });
    }
    if (isArray(query) || isObject(query)) {
        query.map((item: any) => {
            params[item.name] = item.value;
        });
    }
    defaultTableQuery.value = params;
    tableQuery.value = params;
    //分页参数
    if (isObject(config) && config.hasOwnProperty("pagina")) {
        const pageSize = config.pagina === true ? appConfig.LIST_PAGE_SIZE : (isNumber(config.pagina) && config.pagina > 0) ? config.pagina : appConfig.LIST_PAGE_SIZE;
        pagination.value = {
            total: 0,
            current: 1,
            defaultPageSize: pageSize,
            pageSizeOptions: [`${pageSize}`, "30", "50", "100"],
            showQuickJumper: true,
            showSizeChanger: true,
            showTotal: (total: number, range: any) => { return `本页：${range[0]}-${range[1]}，总计：${total}` }
        };
    }
};

//加载表格数据
const getListData = (params: any = {}, callback = (data: any) => { }) => {
    loading.value = true;
    if (!params.pageSize || params.pageSize == undefined) {
        params.pageSize = pagination.value.defaultPageSize;
    }
    req.post('/' + props.config.model + '/' + (props.config.customListAction || 'list'), Object.assign(defaultTableQuery.value, params)).then(res => {
        if (res && res.data) {
            const result = res.data;
            if (props.config.pagina) {
				// 如果请求回来的数据需要处理
				if (props.formatDataSource) dataSource.value = props.formatDataSource(result.data);
				// 正常赋值
                else dataSource.value = result.data;
                //分页参数处理
                pagination.value.total = result.total || 0;
                pagination.value.current = result.current_page || 1;
                pagination.value.pageSize = result.per_page;
            } else {
				// 如果请求回来的数据需要处理
				if (props.formatDataSource) dataSource.value = props.formatDataSource(result);
				// 正常赋值
                else dataSource.value = result;
            }
        }
    }).finally(() => {
        loading.value = false;
        typeof callback === "function" && callback(params);
    })
};

//表格事件-------------------
//表格变动重新加载数据
const onTableChange = (
    pag: { pageSize: number; current: number },
    filters: any,
    sorter: any,
) => {
    tableQuery.value.pageSize = pag.pageSize;
    tableQuery.value.page = pag?.current;
    tableQuery.value.sorter = sorter;
    getListData(tableQuery.value);
};
//表格行选择
const onRowSelectChange = (keys: Key[]) => {
    selectedRowKeys.value = keys;
};
//批量菜单点击
const onBatchMenuClick = (e: any) => {
    const { data } = e.item;
    const pkArr = [...selectedRowKeys.value];
    if (data.hasOwnProperty("action")) {
        if (data.action === 'custom') {
            emit("batch", { data, pkArr: pkArr });
        } else if (data.action === 'delete') {
            onDeleteRow(null);
        } else {
            //批量操作
            const url = '/' + props.config.model + '/' + data.action;
            const values = {
                [props.config.pk]: pkArr,
                data: data.data
            }
            actionLoading.value = true;
            req.post(url, values).then((res: any) => {
                getListData(tableQuery.value);
                selectedRowKeys.value = [];
                message.success(res.msg ? res.msg : '操作成功');
            }).catch((err: any) => {
                message.error(err.msg ? err.msg : '操作失败');
            }).finally(() => {
                actionLoading.value = false
            })
        }
    }
};
//tabs 点击
const onTabsChange = (e: any, field: string) => {
    tableQuery.value.page = 1;
    tableQuery.value[field] = e.target.value;
    getListData(tableQuery.value);
}
//导出菜单点击
const onExportClick = (e: any) => {
    const { data } = e.item;
    const exportType = data.hasOwnProperty("data") && data.data.hasOwnProperty("type") ? data.data['type'] : 'custom';
    if (exportType === 'custom') {
        emit("export", data);
    } else {
        let _fileName = data.hasOwnProperty("data") && data.data.hasOwnProperty("filename") ? data.data['filename'] : '导出数据';
        if (_fileName == '' || _fileName == null) {
            _fileName = '导出数据';
        }
        //后台处理返回数据
        const url = '/' + props.config.model + '/export';
        const params = { ...tableQuery.value, type: exportType }
        actionLoading.value = true;
        req.post(url, params).then((res: any) => {
            const result = res.data;
            if (!result.data || result.data == null || !isArray(result.data) || result.data.length == 0) {
                message.error('无可导出数据');
            } else {
                let _header: any = [];
                if (!result.header || result.header == null || result.header == '') {
                    _header = columns2XlsxHeader(props.config.columns);
                } else {
                    _header = result.header;
                }
                if (result.filename && result.filename != '' && result.filename != null) {
                    _fileName = result.filename;
                }
                exportExcel({
                    data: result.data,
                    header: _header,
                    filename: _fileName
                })
                message.success(res.msg ? res.msg : '导出成功');
            }
        }).catch((err: any) => {
            message.error(err.msg ? err.msg : '导出失败');
        }).finally(() => {
            actionLoading.value = false
        })
    }
};

//删除
const onDeleteRow = (row: any) => {
    Modal.confirm({
        title: '是否删除所选择的?',
        content: '删除后无法恢复',
        okText: '确定删除',
        okType: 'danger',
        cancelText: '取消',
        async onOk() {
            try {
                let ids: Key[] = [];
                const { pk } = props.config;
                const pkValue = row && row.hasOwnProperty(pk) ? row[pk] : null;
                if (row && row != null && pkValue && pkValue != null) {
                    ids.push(pkValue)
                } else if (selectedRowKeys.value.length > 0) {
                    ids = [...selectedRowKeys.value];
                }
                if (!ids || ids.length < 1) {
                    message.error("请选择需要删除的数据");
                    return false;
                }
                const url = '/' + props.config.model + '/delete';
                const values = {
                    [pk]: ids
                }
                actionLoading.value = true;
                return await req.post(url, values).then((res: any) => {
                    selectedRowKeys.value = [];
                    getListData(tableQuery.value);
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


//表单事件
const onTableQuerySubmit = (query: any) => {
    tableQuery.value = { ...tableQuery.value, ...query };
    getListData(tableQuery.value);
}
const onTableQueryReset = () => {
    tableQuery.value = defaultTableQuery.value;
    getListData(tableQuery.value);
}
const refresh = () => {
    getListData(tableQuery.value);
}

//监听父组件的值
watch(
    () => props.query,
    (val, old) => {
        if (!isObjectEq(val, old)) {
            tableQuery.value = val;
        }
    },
    { deep: true, immediate: true }
);


// 暴露方法
defineExpose({ refresh });

//页面
onMounted(() => {
    initProps();
    if (props.autoload) {
        getListData(props.query);
    }
});
</script>

<style lang="less">
.m-list-table {
    background-color: #FFF;
    box-shadow: 0px 0px 4px 0px rgba(131, 131, 131, 0.15);
    border-radius: 6px;
    padding: 10px;
    position: relative;

    &__inner {}

    &__filter {
        background-color: #FAFAFA;
        border-radius: 6px;
        padding: 10px 15px;
        margin-bottom: 20px;
    }

    &__toolber {
        margin-bottom: 10px;
    }

    &__table {
        position: relative;
    }

    &__action {
        .ant-btn {
            color: @table-action-button-text-color !important;
            border-color: @table-action-button-color !important;
            margin: 0 !important;
            font-size: 13px !important;
            border-right-width: 0;

            &:hover {
                background: @table-action-button-hover-color !important;
                border-color: @table-action-button-hover-color !important;
                color: @table-action-button-text-hover-color !important;
            }

            &:last-child {
                border-radius: 0 10px 10px 0 !important;
                border-right-width: 1px;
            }

            &:first-child {
                border-radius: 10px 0 0 10px !important;
            }

            &:last-child:first-child {
                border-radius: 10px !important;
            }

            &:disabled,
            &:disabled:hover {
                border-color: #ccc !important;
                color: #999 !important;
                background-color: #F5F5F5 !important;
            }
        }
    }

}
</style>
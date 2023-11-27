<template>
    <div class="page-main">
        <MkListPage title="帮助管理" subTitle="Notice List" :tableConfig="tableConfig" :createFormConfig="createFormConfig"
            :editFormConfig="createFormConfig" :modalConfig="modalConfig">
            <template #cover_image="{ value }">
                <a-popover title="封面图片预览">
                    <template #content>
                        <a-image :width="200" :src="value" />
                    </template>
                    <a-avatar shape="square" :src="value" />
                </a-popover>
            </template>
            <template #status_desc="{ value, row }">
                <a-tag :color="row.status == 1 ? 'green' : 'red'" class="mr0">{{ value }}</a-tag>
            </template>
        </MkListPage>
    </div>
</template>

<script setup lang="ts">
const modalConfig = {
    title: '帮助',
    width: 800,
    height: 400
}

const createFormConfig = [
    <FormItem[]>[
        {
            label: '标题',
            name: 'title',
            type: 'text',
            placeholder: '请输入标题',
            options: null,
            value: '',
            rules: [{
                required: true,
                message: '请填写标题',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '内容',
            name: 'content',
            type: 'editor',
            placeholder: '请填写内容',
            options: [],
            value: '',
            rules: [{
                required: true,
                message: '请填写内容',
                trigger: 'blur'
            }]
        }
    ]
];

const tableConfig = <any>{
    model: 'help',
    pk: 'help_id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "标题",
            dataIndex: "title",
            key: "title",
        },
        {
            title: "阅读量",
            dataIndex: "views",
            key: "views",
            width: 100,
            align: 'right',
            sorter: true
        },
        {
            title: "发布时间",
            dataIndex: "create_time",
            key: "create_time",
            width: 175,
            align: 'center',
            sorter: true
        },
        {
            title: "状态",
            key: "status_desc",
            dataIndex: "status_desc",
            width: 90,
            align: 'center'
        },
        {
            title: "操作",
            key: "action__btn",
            width: 140,
            align: "center",
        }
    ],
    form: [
        <FormItem[]>[
            {
                label: '标题',
                name: 'keyword',
                type: 'text',
                placeholder: '标题模糊查询',
                options: null,
                value: '',
                rules: null,
                style: { width: '220px' }
            }
        ]
    ],
    //列表头部标签筛选
    tabs: [
        {
            field: 'status',
            value: -1,
            options: [
                { label: '全部', value: -1 },
                { label: '已发布', value: 1 },
                { label: '未发布', value: 0 }
            ]
        }
    ],
    //批量操作菜单
    batchText: '批量操作',
    batch: [
        { title: '批量删除', action: 'delete', data: null },
        { title: '设置发布', action: 'set_status', data: { status: 1 } },
        { title: '取消发布', action: 'set_status', data: { status: 0 } }
    ],
    //导出菜单
    exportText: '导出',
    export: [
        { title: '导出Excel', data: { type: 'xlsx', filename: '导出Excel' } }
    ]
};
</script>
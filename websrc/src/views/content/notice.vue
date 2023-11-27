<template>
    <div class="page-main">
        <MListPage title="公告管理" subTitle="Notice" :tableConfig="tableConfig" :createFormConfig="createFormConfig"
            :editFormConfig="createFormConfig" :modalConfig="modalConfig">
            <template #status_desc="{ value, row }">
                <a-tag :color="row.status == 1 ? 'green' : 'red'" class="mr0">{{ value }}</a-tag>
            </template>
        </MListPage>
    </div>
</template>

<script setup lang="ts">
const modalConfig = {
    title: '公告',
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
    model: 'notice',
    pk: 'notice_id',
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
            title: "状态",
            key: "status_desc",
            dataIndex: "status_desc",
            width: 90,
            align: 'center'
        },
        {
            title: "操作",
            key: "table__action",
            width: 140,
            align: "center",
            fixed: 'right'
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
    export: null
};
</script>
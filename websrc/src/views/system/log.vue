<template>
    <div class="page-main">
        <MListPage title="操作日志" subTitle="System Log" :tableConfig="tableConfig">
        </MListPage>
    </div>
</template>

<script setup lang="ts">
const tableConfig = <any>{
    model: 'system.log',
    pk: 'id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "操作时间",
            width: 170,
            dataIndex: "create_time",
            align: "center"
        },
        {
            title: "动作",
            width: 300,
            dataIndex: "action"
        },
        {
            title: "描述",
            dataIndex: "desc",
            width: 260
        },
        {
            title: "IP",
            dataIndex: "ip",
            width: 110,
        },
        {
            title: "操作人",
            dataIndex: "operator_name",
        }
    ],
    form: [
        <FormItem[]>[
            {
                label: '关键词',
                name: 'keyword',
                type: 'text',
                placeholder: '模糊查询',
                options: null,
                value: '',
                rules: null,
                style: { width: '220px' }
            }
        ]
    ],
    //列表头部标签筛选
    tabs: null,
    //批量操作菜单
    batchText: '批量操作',
    batch: null,
    //导出菜单
    exportText: '导出',
    export: null
};
</script>
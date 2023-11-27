<template>
    <div class="page-main">
        <MListPage title="图文分类管理" subTitle="Category List" :tableConfig="tableConfig" :createFormConfig="createFormConfig"
            :editFormConfig="createFormConfig" :modalConfig="modalConfig">
        </MListPage>
    </div>
</template>

<script setup lang="ts">
const modalConfig = {
    title: '图文分类',
    width: 500,
    height: 130
}

const createFormConfig = [
    <FormItem[]>[
        {
            label: '分类名称',
            name: 'category_name',
            type: 'text',
            placeholder: '请输入分类名称',
            options: null,
            value: '',
            rules: [{
                required: true,
                message: '请输入分类名称',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '排序',
            name: 'sort',
            type: 'number',
            placeholder: '升序',
            value: 100
        },
    ]
];

const tableConfig = <any>{
    model: 'article_category',
    pk: 'category_id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "分类名称",
            dataIndex: "category_name",
            key: "category_name",
        },
        {
            title: "排序",
            dataIndex: "sort",
            key: "sort",
            width: 100,
            align: 'right',
            sorter: true
        },
        {
            title: "创建/更新时间",
            dataIndex: "update_time",
            key: "update_time",
            width: 175,
            align: 'center',
            sorter: true
        },
        {
            title: "操作",
            key: "table__action",
            width: 140,
            align: "center",
        }
    ],
    form: null,
    //列表头部标签筛选
    tabs: null,
    //批量操作菜单
    batchText: '批量操作',
    batch: [
        { title: '批量删除', action: 'delete', data: null }
    ]
};
</script>
<template>
    <div class="page-main">
        <MListPage title="数据字典" subTitle="Data dict" :tableConfig="tableConfig"
            :createFormConfig="createFormConfig" :editFormConfig="createFormConfig" :modalConfig="modalConfig" :btnAction="btnAction">
        </MListPage>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const modalConfig = {
    title: '数据',
    width: 500,
    height: 230
}

const btnAction = ref<any>({
	view: '',
	add: 'dict_create',
	update: 'dict_update',
	delete: 'dict_delete',
})

const createFormConfig = [
    <FormItem[]>[
        {
            label: '数据字典',
            name: 'dict',
            type: 'dict_group_select',
            placeholder: '请选择数据字典',
            value: null,
            rules: [{
                required: true,
                message: '请选择数据字典',
                trigger: 'change'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '名称',
            name: 'title',
            type: 'text',
            placeholder: '请填写名称',
            value: '',
            rules: [{
                required: true,
                message: '请填写名称',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: 'key',
            name: 'key',
            type: 'text',
            placeholder: '请填写key',
            value: '',
            rules: [{
                required: true,
                message: '请填写key',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: 'value',
            name: 'value',
            type: 'text',
            placeholder: '请填写value',
            value: '',
            rules: [{
                required: true,
                message: '请填写value',
                trigger: 'blur'
            }]
        },
    ],

];

const tableConfig = <any>{
    model: 'system.dict',
    pk: 'key',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "字典",
            dataIndex: "dict_title",
            width: 200
        },
        {
            title: "名称",
            dataIndex: "title",
            width: 200,
        },
        {
            title: "key",
            dataIndex: "key",
            width: 160,
        },
        {
            title: "value",
            dataIndex: "value"
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
                label: '数据字典',
                name: 'dict',
                type: 'dict_group_select',
                placeholder: '所有字典',
                options: [{ label: '所有字典', value: '' }],
                value: null,
                style: { width: '200px' }
            },
        ],
        <FormItem[]>[
            {
                label: '关键字',
                name: 'keyword',
                type: 'text',
                placeholder: '名称/key/value',
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
    batch: [
        { title: '批量删除', action: 'delete', data: null }
    ],
    //导出菜单
    exportText: '导出',
    export: null
};
</script>
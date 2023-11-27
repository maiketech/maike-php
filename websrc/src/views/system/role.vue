<template>
    <div class="page-main">
        <MListPage title="角色权限" subTitle="Role" :tableConfig="tableConfig" :createFormConfig="createFormConfig"
            :editFormConfig="createFormConfig" :modalConfig="modalConfig" :btnAction="btnAction">
            <template #status_desc="{ value, row }">
                <a-tag :color="row.status == 1 ? 'green' : 'red'" class="mr0">{{ value }}</a-tag>
            </template>
        </MListPage>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { checkAction } from "@/maike/validate";

const modalConfig = {
    title: '角色权限',
    width: 600,
    height: 450
}

const btnAction = ref<any>({
    view: '',
    add: 'admin_role_create',
    update: 'admin_role_update',
    delete: 'admin_role_delete',
})

const createFormConfig = [
    <FormItem[]>[
        {
            label: '角色名称',
            name: 'role_name',
            type: 'text',
            placeholder: '请填写角色名称',
            options: null,
            value: '',
            rules: [{
                required: true,
                message: '请填写角色名称',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '管理权限',
            name: 'action_ids',
            type: 'action_select',
            placeholder: '请设置权限',
            options: null,
            value: null,
            rules: [{
                required: true,
                message: '请设置权限',
                trigger: 'blur'
            }, {
                validator: checkAction,
                trigger: 'change'
            }],
            style: { height: '300px' }
        }
    ],
    <FormItem[]>[
        {
            label: '备注',
            name: 'desc',
            type: 'text',
            placeholder: '请填写备注',
            options: [],
            value: '',
            rules: null
        },
    ]
];

const tableConfig = <any>{
    model: 'system.admin_role',
    pk: 'role_id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "角色名称",
            width: 200,
            dataIndex: "role_name"
        },
        {
            title: "描述",
            dataIndex: "desc"
        },
        {
            title: "创建/更新时间",
            dataIndex: "update_time",
            width: 175
        },
        {
            title: "状态",
            key: "status_desc",
            dataIndex: "status_desc",
            align: "center",
            width: 120
        },
        {
            title: "操作",
            key: "table__action",
            width: 140,
            align: "center",
            fixed: 'right'
        }
    ],
    form: null,
    //列表头部标签筛选
    tabs: [
        {
            field: 'status',
            value: -1,
            options: [
                { label: '全部', value: -1 },
                { label: '正常', value: 1 },
                { label: '已禁用', value: 0 }
            ]
        }
    ],
    //批量操作菜单
    batchText: '批量操作',
    batch: [
        { title: '批量删除', action: 'delete', data: null },
        { title: '设置正常', action: 'set_status', data: { status: 1 } },
        { title: '设置禁用', action: 'set_status', data: { status: 0 } }
    ],
    //导出菜单
    exportText: '导出',
    export: null
};
</script>
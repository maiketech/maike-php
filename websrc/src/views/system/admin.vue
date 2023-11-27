<template>
    <div class="page-main">
        <MListPage ref="ListPage" title="系统管理员" subTitle="System Admin" :tableConfig="tableConfig"
            :createFormConfig="createFormConfig" :editFormConfig="editFormConfig" :modalConfig="modalConfig" :btnAction="btnAction">
            <template #cover_image="{ value }">
                <a-popover title="头像预览">
                    <template #content>
                        <a-image :width="200" :src="value" />
                    </template>
                    <a-avatar shape="square" :src="value" />
                </a-popover>
            </template>
            <template #status_desc="{ value, row }">
                <a-tag :color="row.status == 1 ? 'green' : 'red'" class="mr0">{{ value }}</a-tag>
            </template>
        </MListPage>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { checkPassword, checkId } from "@/maike/validate";

const ListPage = ref();

const btnAction = ref<any>({
	view: '',
	add: 'admin_create',
	update: 'admin_update',
	delete: 'admin_delete',
})

//信息列表
const modalConfig = {
    title: '管理员',
    width: 600,
    height: 400
}

const createFormConfig = [
    <FormItem[]>[
        {
            label: '角色',
            name: 'role_id',
            type: 'role_select',
            placeholder: '请选择角色',
            value: null,
            rules: [{
                required: true,
                validator: checkId,
                trigger: 'change'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '姓名',
            name: 'name',
            type: 'text',
            placeholder: '请填写姓名',
            value: '',
            rules: [{
                required: true,
                message: '请填写姓名',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '手机号码',
            name: 'mobile',
            type: 'text',
            placeholder: '请填写手机号码',
            value: '',
            rules: [{
                required: true,
                message: '请填写手机号码',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '登录名',
            name: 'username',
            type: 'text',
            placeholder: '请填写登录名',
            value: '',
            rules: [{
                required: true,
                message: '请填写登录名',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '密码',
            name: 'password',
            type: 'password',
            placeholder: '请填写密码',
            value: null,
            rules: [{
                required: true,
                validator: checkPassword,
                trigger: 'blur'
            }],
            help: '密码至少8位字符以上'
        },
    ],
    <FormItem[]>[
        {
            label: '头像',
            name: 'avatar',
            type: 'image_select',
            placeholder: '请上传头像图片',
            options: {
                count: 1
            },
            value: [],
            rules: null
        },
    ],
    <FormItem[]>[
        {
            label: '邮箱',
            name: 'email',
            type: 'text',
            placeholder: '请填写邮箱地址',
            options: null,
            value: '',
            rules: null
        },
    ]
];

const editFormConfig = [
    <FormItem[]>[
        {
            label: '角色',
            name: 'role_id',
            type: 'role_select',
            placeholder: '请选择角色',
            options: null,
            value: null,
            rules: [{
                required: true,
                validator: checkId,
                trigger: 'change'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '姓名',
            name: 'name',
            type: 'text',
            placeholder: '请填写姓名',
            value: '',
            rules: [{
                required: true,
                message: '请填写姓名',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '手机号码',
            name: 'mobile',
            type: 'text',
            placeholder: '请填写手机号码',
            value: '',
            rules: [{
                required: true,
                message: '请填写手机号码',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '登录名',
            name: 'username',
            type: 'text',
            placeholder: '请填写登录名',
            value: '',
            rules: [{
                required: true,
                message: '请填写登录名',
                trigger: 'blur'
            }]
        },
    ],
    <FormItem[]>[
        {
            label: '密码',
            name: 'password',
            type: 'password',
            placeholder: '留空则不修改密码',
            options: null,
            value: null,
            rules: [{
                validator: checkPassword,
                trigger: 'blur'
            }],
            help: '密码至少8位字符以上'
        },
    ],
    <FormItem[]>[
        {
            label: '头像',
            name: 'avatar',
            type: 'image_select',
            placeholder: '请上传头像图片',
            options: {
                count: 1
            },
            value: [],
            rules: null
        },
    ],
    <FormItem[]>[
        {
            label: '邮箱',
            name: 'email',
            type: 'text',
            placeholder: '请填写邮箱地址',
            options: null,
            value: '',
            rules: null
        },
    ]
];

const tableConfig = <any>{
    model: 'system.admin',
    pk: 'admin_id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "姓名",
            dataIndex: "name",
            width: 150,
            fixed: 'left'
        },
        {
            title: "登录名",
            width: 150,
            dataIndex: "username"
        },
        {
            title: "手机号码",
            width: 150,
            dataIndex: "mobile"
        },
        {
            title: "角色",
            dataIndex: "role_name"
        },
        {
            title: "状态",
            key: "status_desc",
            dataIndex: "status_desc",
            align: "center",
            width: 90
        },
        {
            title: "操作",
            key: "table__action",
            width: 120,
            align: "center",
            fixed: 'right'
        }
    ],
    form: [
        <FormItem[]>[
            {
                label: '角色',
                name: 'role_id',
                type: 'role_select',
                placeholder: '请选择角色',
                options: [{ label: '所有角色', value: 0 }],
                value: 0,
                style: { width: '220px' }
            },
        ],
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
    ]
};
</script>
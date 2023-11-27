<template>
    <div class="page-main">
        <MListPage ref="ListPage" title="用户管理" subTitle="User" :tableConfig="tableConfig"
            :editFormConfig="editFormConfig" :modalConfig="modalConfig" :btnAction="btnAction"
            :tableRowButton="['view', 'edit']">
            <template #avatar="{ value }">
                <a-popover title="头像预览">
                    <template #content>
                        <a-image :width="200" :src="value" />
                    </template>
                    <a-avatar shape="square" :src="value" />
                </a-popover>
            </template>
            <template #status_text="{ value, row }">
                <a-tag :color="row.status == 1 ? 'green' : 'red'" class="mr0">{{ value }}</a-tag>
            </template>

            <template #table__action>
                <a-dropdown>
                    <template #overlay>
                        <a-menu>
                            <a-menu-item key="1">1st item</a-menu-item>
                            <a-menu-item key="2">2nd item</a-menu-item>
                            <a-menu-item key="3">3rd item</a-menu-item>
                        </a-menu>
                    </template>
                    <a-button type="" size="small">更多<DownOutlined /></a-button>
                </a-dropdown>
            </template>

            <template #modal__detail="{ detail }">
                <a-tabs v-model:activeKey="detailTabsIndex">
                    <a-tab-pane :key="1" tab="顾客信息">
                        <div class="sbox">
                            <a-descriptions bordered size="small" :column="1" :labelStyle="{ width: '150px' }">
                                <a-descriptions-item label="顾客编号">{{ detail.customer_no }}</a-descriptions-item>
                                <a-descriptions-item label="顾客姓名">{{ detail.name }}</a-descriptions-item>
                                <a-descriptions-item label="手机号码">{{ detail.mobile }}</a-descriptions-item>
                                <a-descriptions-item label="性别">{{ detail.gender }}</a-descriptions-item>
                                <a-descriptions-item label="年龄">{{ detail.age }}</a-descriptions-item>
                                <a-descriptions-item label="出生日期">{{ detail.birth_date }}</a-descriptions-item>
                                <a-descriptions-item label="身份证号">{{ detail.idcard }}</a-descriptions-item>
                                <a-descriptions-item label="联系地址">{{ detail.address }}</a-descriptions-item>
                                <a-descriptions-item label="紧急联系人">{{ detail.contacts_name }}</a-descriptions-item>
                                <a-descriptions-item label="紧急联系电话">{{ detail.contacts_phone }}</a-descriptions-item>
                            </a-descriptions>
                        </div>
                    </a-tab-pane>
                    <a-tab-pane :key="2" tab="预约记录">
                        <div class="sbox"></div>
                    </a-tab-pane>
                    <a-tab-pane :key="3" tab="咨询记录">
                        <div class="sbox"></div>
                    </a-tab-pane>
                </a-tabs>
            </template>
        </MListPage>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { DownOutlined } from '@ant-design/icons-vue'

const ListPage = ref();

const btnAction = ref<any>({
    view: 'user_detail',
    add: '',
    update: 'user_update',
    delete: 'user_delete',
})

//信息列表
const modalConfig = {
    title: '顾客',
    width: 700,
    height: 300
}

const detailTabsIndex = ref<number>(1);

const editFormConfig = [
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
            label: '性别',
            name: 'gender',
            type: 'radio',
            placeholder: '请选择性别',
            options: [{ label: '男', value: 1 }, { label: '女', value: 2 }, { label: '未知', value: 0 }],
            value: 0
        },
    ],
    <FormItem[]>[
        {
            label: '年龄',
            name: 'age',
            type: 'number',
            placeholder: '请填写年龄',
            value: ''
        },
    ],
    <FormItem[]>[
        {
            label: '身份证号',
            name: 'idcard',
            type: 'text',
            placeholder: '请填写身份证号',
            value: ''
        },
    ],
    <FormItem[]>[
        {
            label: '出生日期',
            name: 'birth_date',
            type: 'date',
            placeholder: '请填写出生日期',
            options: null,
            value: '',
            rules: null
        },
    ],
    <FormItem[]>[
        {
            label: '紧急联系人',
            name: 'contacts_name',
            type: 'text',
            placeholder: '请填写紧急联系人',
            value: ''
        },
    ],
    <FormItem[]>[
        {
            label: '紧急联系电话',
            name: 'contacts_phone',
            type: 'text',
            placeholder: '请填写紧急联系电话',
            value: ''
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
            value: ''
        },
    ]
];

const tableConfig = <any>{
    model: 'user.user',
    pk: 'user_id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "昵称",
            dataIndex: "nickname",
            width: 150,
            fixed: 'left'
        },
        {
            title: "头像",
            key: "avatar",
            dataIndex: "avatar",
            width: 80,
            align: "center"
        },
        {
            title: "姓名",
            dataIndex: "name",
            width: 150
        },
        {
            title: "性别",
            width: 90,
            dataIndex: "gender"
        },
        {
            title: "年龄",
            width: 90,
            dataIndex: "age"
        },
        {
            title: "手机号码",
            dataIndex: "mobile",
            width: 'auto'
        },
        {
            title: "状态",
            key: "status_text",
            dataIndex: "status_text",
            align: "center",
            width: 90
        },
        {
            title: "操作",
            key: "table__action",
            width: 230,
            align: "center",
            fixed: 'right'
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
        //{ title: '批量删除', action: 'delete', data: null },
        { title: '设置正常', action: 'set_status', data: { status: 1 } },
        { title: '设置禁用', action: 'set_status', data: { status: 0 } }
    ]
};
</script>
<template>
    <div class="page-main">
        <MListPage title="图文内容管理" subTitle="Article List" :tableConfig="tableConfig" :createFormConfig="createFormConfig"
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
        </MListPage>
    </div>
</template>

<script setup lang="ts">
import { checkId } from "@/maike/validate";

const modalConfig = {
    title: '图文内容',
    width: 800,
    height: 400
}

const createFormConfig = [
    <FormItem[]>[
        {
            label: '分类',
            name: 'category_id',
            type: 'article_category_select',
            placeholder: '请选择分类',
            options: null,
            value: null,
            rules: [{
                required: true,
                validator: checkId,
                trigger: 'change',
                message: '请选择分类'
            }]
        },
    ],
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
            label: '封面图片',
            name: 'cover_image',
            type: 'image_select',
            placeholder: '请上传封面图片',
            options: {
                count: 1
            },
            value: [],
            rules: [{
                required: true,
                message: '请上传封面图片',
                trigger: 'change'
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
    model: 'article',
    pk: 'article_id',
    pagina: true,
    //表格列设置
    columns: [
        {
            title: "标题",
            dataIndex: "title",
            key: "title",
        },
        {
            title: "封面图片",
            dataIndex: "cover_image",
            key: "cover_image",
            width: 90,
            align: 'center'
        },
        {
            title: "分类",
            dataIndex: "category_name",
            key: "category_name",
            width: 180,
            align: 'center'
        },
        {
            title: "阅读量",
            dataIndex: "views",
            key: "views",
            width: 85,
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
            key: "table__action",
            width: 140,
            align: "center",
        }
    ],
    form: [
        <FormItem[]>[
            {
                label: '分类',
                name: 'category_id',
                type: 'article_category_select',
                placeholder: '所有分类',
                options: [{ label: "所有分类", value: 0 }],
                value: 0,
                style: { width: '220px' }
            }
        ],
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
    ]
};
</script>
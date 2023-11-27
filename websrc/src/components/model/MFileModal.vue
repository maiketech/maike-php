<template>
    <a-modal :visible="visible"
        :title="title == '' ? (fileType == 'video' ? '视频库' : (fileType == 'file' ? '文件库' : '图片库')) : title"
        :confirmLoading="loading || uploading" :maskClosable="false" :bodyStyle="modalBodyStyle"
        :cancelButtonProps="{ props: { disabled: loading || uploading } }" :okText="okButtonText"
        :cancelText="cancelButtonText" :width="width" @cancel="onModalClose">
        <template #footer>
            <a-row type="flex" :gutter="16">
                <a-col flex="auto" class="tl">
                    <a-button type="" class="creat-btn" :disabled="loading || uploading || groupLoading"
                        :loading="groupLoading" @click="onGroupCreate">新增分组
                    </a-button>
                </a-col>
                <a-col flex="auto" class="tr">
                    <span class="selected-desc">已选择{{ selectedIds.length }}项</span>
                    <a-button key="back" :disabled="loading || uploading" @click="onModalClose">取消</a-button>
                    <a-button key="submit" type="primary" :disabled="loading || uploading" @click="onModalOk">确定
                    </a-button>
                </a-col>
            </a-row>
        </template>
        <a-spin :spinning="loading" tip="加载中...">
            <a-row type="flex" :gutter="16">
                <a-col flex="180px" class="m-file-modal-group" :style="{ height: modalBodyHeight }">
                    <div class="m-file-modal-group__list">
                        <template v-for="(item, index) in fileGroupList" :key="index">
                            <div class="item" :class="curGroupID == item.group_id ? 'active' : ''"
                                @click="onGroupClick(item.group_id)">
                                {{ item.group_name }}
                                <template v-if="item.group_id != -1 && item.group_id != 0">
                                    <span class="edit" @click.stop="onGroupEdit(item)">
                                        <icon type="xi-bianji" />
                                    </span>
                                    <span class="close" @click.stop="onGroupDelete(item)">
                                        <icon type="xi-qingkong" />
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>
                </a-col>
                <a-col flex="auto">
                    <a-form class="m-file-modal__upload">
                        <a-row :gutter="16">
                            <a-col class="tl" :span="12">
                                <a-tooltip placement="topLeft" :title="uploadSizeLimitDesc">
                                    <a-form-item>
                                        <a-upload :action="uploadUrl" :headers="uploadHeaders" :disabled="loading"
                                            :accept="uploadAccept" :name="fileType" :showUploadList="false"
                                            :data="uploadData" :beforeUpload="beforeUpload" @change="handleUploadChange">
                                            <a-button type="primary" ghost :disabled="loading || uploading"
                                                :loading="uploading">
                                                <upload-outlined></upload-outlined>
                                                上传
                                            </a-button>
                                        </a-upload>
                                    </a-form-item>
                                </a-tooltip>
                            </a-col>
                            <a-col class="tr" :span="12">
                                <a-form-item>
                                    <a-input-search placeholder="搜索文件名称" style="width: 200px" allow-clear
                                        @search="onSearch" />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-form>
                    <div class="m-file-modal__list">
                        <div class="empty" v-if="!fileList || fileList.length == 0">
                            <a-empty :image="Empty.PRESENTED_IMAGE_SIMPLE"
                                :description="fileType == 'video' ? '暂无视频' : (fileType == 'file' ? '暂无文件' : '暂无图片')" />
                        </div>
                        <template v-for="fileItem in fileList" :key="fileItem.attach_id">
                            <div class="file-item" :title="fileItem.file_name"
                                :class="{ active: selectedIds.indexOf(fileItem.attach_id) > -1 }"
                                @click="onFileItemClick(fileItem)">
                                <div class="file-item__cover"
                                    :style="{ backgroundImage: `url('${fileItem.thumb}')`, width: '95px' }">
                                </div>
                                <div class="file-item__name">{{ fileItem.file_name }}</div>
                                <div class="file-item__selected-mask">
                                    <icon class="file-item__selected-icon" type="mk-gou" />
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="m-file-modal__paging">
                        <a-pagination v-model:current="currentPage" :pageSize="15" :total="listTotal"
                            :showSizeChanger="false" size="small" @change="onPaginChange" />
                    </div>
                </a-col>
            </a-row>
        </a-spin>
    </a-modal>

    <a-modal v-model:visible="groupModalShow" :title="groupModalTitle" :maskClosable="false" centered :okButtonProps="{
        props: { disabled: groupLoading, loading: groupLoading },
    }" :cancelButtonProps="{ props: { disabled: groupLoading } }" okText="保存" :width="300" @ok="submitGroupForm">
        <a-form ref="formRef" :model="groupFormData" :label-col="{ span: 8 }" :wrapper-col="{ span: 16 }" autocomplete="off"
            @finish="saveGroup">
            <a-form-item label="分组名称" name="group_name" :rules="[{ required: true, message: '请输入分组名称' }]">
                <a-input v-model:value="groupFormData.group_name" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script lang="ts" setup>
import { ref, computed, watch, createVNode } from 'vue';
import type { UploadChangeParam } from 'ant-design-vue';
import { UploadOutlined, ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { message, Modal, Empty } from 'ant-design-vue';
import { useAdminStoreWithOut } from "@/store/modules/admin";
import req from '@/utils/request';
import appConfig from '@/config/app';
import { isNumber } from '@/utils/is';

const emit = defineEmits(["ok", "close", "update:show"]);

//组件属性
interface Props {
    title?: string,
    show?: boolean,
    fileType?: string,
    width?: string | number,
    height?: string | number,
    okButtonText?: string,
    cancelButtonText?: string,
    count?: number
}
const props = withDefaults(defineProps<Props>(), {
    title: '',
    show: false,
    fileType: 'image',
    width: 800,
    height: 500,
    okButtonText: '确定',
    cancelButtonText: '取消',
    count: 1
})

const visible = ref<boolean>(false);
const currentPage = ref(1);
const listTotal = ref(0);
const loading = ref<boolean>(true);
const uploading = ref<boolean>(false);
const fileGroupList = ref<any>([{
    group_name: '全部',
    key: -1,
    group_id: -1
}, {
    group_name: '未分组',
    key: 0,
    group_id: 0
}]);
const fileList = ref<any>([]);
const modalBodyStyle = computed(() => {
    let _style: any = { padding: '0 6px', overflowY: 'auto', overflowX: 'hidden' }
    if (props.height !== null || props.height !== '' || props.height !== 'auto') {
        _style['height'] = isNumber(props.height) ? props.height + 'px' : props.height;
    }
    return _style;
});
const modalBodyHeight = computed(() => {
    if (props.height !== null || props.height !== '' || props.height !== 'auto') {
        return isNumber(props.height) ? props.height + 'px' : props.height;
    }
    return 'auto';
});
const selectedIds = ref<any>([]);
const selectedFiles = ref<any>([]);
const uploadFileSizeLimit = computed<number>(() => {
    switch (props.fileType) {
        case 'video':
            return appConfig.UPLOAD_VIDEO_SIZE
        case 'file':
            return appConfig.UPLOAD_FILE_SIZE
        default:
            return appConfig.UPLOAD_IMAGE_SIZE
    }
});
const uploadSizeLimitDesc = computed<string>(() => {
    return `图片大小不能超过${uploadFileSizeLimit.value}M`;
});
const curGroupID = ref(-1);
const keyword = ref('');
//新增编辑分组
const groupModalTitle = ref('新增分组');
const groupModalShow = ref<boolean>(false);
const groupLoading = ref<boolean>(false);
const groupFormData = ref<any>({});
const editGroupID = ref(0);
const formRef = ref();
//上传
const uploadUrl = computed(() => { 
    const BASE_URL = (process.env.NODE_ENV === "development" ? '/api' : process.env.VITE_API_URL)
    return BASE_URL + '/upload/' + props.fileType 
});
const uploadAccept = computed(() => {
    switch (props.fileType) {
        case 'video':
            return '.mp4, .mpg, .mpge';
        case 'file':
            return '.zip, .rar, .doc, .docx, .xls, .xlsx, .txt';
        default:
            return '.png, .jpg, .jpeg, .gif';
    }
});
const uploadHeaders = computed(() => {
    return {
        "M-Token": useAdminStoreWithOut().getToken
    };
});

const uploadData = computed(() => {
    return { group_id: curGroupID.value };
});

const beforeUpload = (file: any, fileList: any) => {
    // 验证文件大小
    const limitSize = Number(uploadFileSizeLimit)
    const fileSizeMb = file.size / 1024 / 1024
    if (fileSizeMb > limitSize) {
        message.error(`文件大小不能超出${limitSize}MB`)
        return false
    }
    // 验证文件上传数量
    if (fileList.length > props.count) {
        message.error(`每次上传最多同时上传${props.count}个`);
        return false;
    }
    return true;
};

const handleUploadChange = (info: UploadChangeParam) => {
    if (info.file.status === 'done') {
        const { response } = info.file
        if (response.code == 10000) {
            message.success("上传成功");
            currentPage.value = 1;
            getFileList({});
        } else {
            message.error("上传失败：" + response.msg);
        }
        uploading.value = false;
    } else if (info.file.status === 'error') {
        uploading.value = false;
        message.error("上传失败");
    } else {
        uploading.value = true;
    }
};

const onFileItemClick = (file: any) => {
    //selectedFiles
    var index = selectedIds.value.indexOf(file.attach_id);
    if (!(props.count > 1)) {
        if (index > -1) {
            selectedIds.value = [];
            selectedFiles.value = [];
        } else {
            selectedIds.value = [file.attach_id];
            selectedFiles.value = [{
                attach_id: file.attach_id,
                file_name: file.file_name,
                file_url: file.url
            }];
        }
        return;
    }

    if (index > -1) {
        selectedIds.value.splice(index, 1);
        selectedFiles.value.splice(index, 1);
    } else {
        if (selectedIds.value.length >= props.count) {
            message.warning(`最多可选${props.count}个文件`, 1)
            return
        }
        selectedIds.value.push(file.attach_id);
        selectedFiles.value.push({
            attach_id: file.attach_id,
            file_name: file.file_name,
            file_url: file.url
        });
    }
};

const getGroupList = () => {
    groupLoading.value = true;
    let params = {
        type: props.fileType
    };
    req.post('/system.attachment/group_list', params).then(res => {
        fileGroupList.value = res.data;
        fileGroupList.value.unshift({
            group_name: '全部',
            key: -1,
            group_id: -1
        }, {
            group_name: '未分组',
            key: 0,
            group_id: 0
        })
    }).finally(() => {
        groupLoading.value = false;
    })
};

const getFileList = (params: any) => {
    loading.value = true;
    params.pageSize = 15;
    params.page = currentPage.value;
    params.keyword = keyword.value;
    params.group_id = curGroupID.value;
    params.type = props.fileType;
    req.post('/system.attachment/list', params).then(res => {
        const result = res.data;
        fileList.value = result.data;
        //分页参数处理
        listTotal.value = result.total;
        currentPage.value = result.current_page;
    }).finally(() => {
        loading.value = false;
    })
};

const onGroupClick = (groupId: number) => {
    curGroupID.value = groupId;
    getFileList({});
};

const onGroupCreate = () => {
    groupModalTitle.value = '新增分组';
    groupModalShow.value = true;
};

const onGroupEdit = (item: any) => {
    groupModalTitle.value = '编辑分组';
    editGroupID.value = item.group_id;
    groupFormData.value = {
        group_id: item.group_id,
        group_name: item.group_name
    }
    groupModalShow.value = true;
};

const saveGroup = (params: any) => {
    let _url = '/system.attachment/group_create';
    if (editGroupID.value > 0) {
        params.group_id = editGroupID.value;
        _url = '/system.attachment/group_update';
    }
    groupLoading.value = true;
    req.post(_url, params).then(res => {
        getGroupList();
        editGroupID.value = 0;
        groupModalShow.value = false;
    }).finally(() => {
        groupLoading.value = false;
    });
};

const submitGroupForm = (item: any) => {
    formRef.value
        .validateFields()
        .then((values: any) => {
            saveGroup(values);
        })
        .catch((info: any) => {
        });
};

const onGroupDelete = (item: any) => {
    let params = {
        group_id: item.group_id
    };
    Modal.confirm({
        title: `确定删除分组【${item.group_name}】吗?`,
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode('div', { style: 'color:red;' }, '删除后无法恢复'),
        onOk() {
            groupLoading.value = true;
            req.post('/system.attachment/group_delete', params).then((res: any) => {
                message.success(res.msg ? res.msg : "删除成功");
                getGroupList();
            }).catch((err: any) => {
                message.error(err.msg ? err.msg : "删除失败");
            }).finally(() => {
                groupLoading.value = false;
            })
        }
    });
};

const onPaginChange = (page: number) => {
    currentPage.value = page;
    getFileList({});
};

const onSearch = (searchValue: string) => {
    keyword.value = searchValue;
    getFileList({});
};

watch(() => props.show, (val) => {
    visible.value = val;
    if (val === true) {
        getFileList({});
        getGroupList();
    }
});

watch(() => props.fileType, (val, old) => {
    if (val !== old) {
        getFileList({});
        getGroupList();
    }
});

const onModalClose = () => {
    selectedIds.value = [];
    selectedFiles.value = [];
    //emit("update:show", false);
    emit("close", false);
};

const onModalOk = () => {
    emit("ok", selectedIds.value, selectedFiles.value);
    onModalClose();
};
</script>

<style lang="less">
.m-file-modal-group {
    border-right: 1px solid #e6e6e6;
    overflow-y: auto;
    overflow-x: auto;
    padding: 10px 0;

    &__list {
        .item {
            height: 40px;
            line-height: 40px;
            padding: 0 10px;
            font-size: 13px;
            border-bottom: 1px solid #f2f2f2;
            position: relative;
            cursor: pointer;
        }

        .item:last-child {
            border-bottom: 0;
        }

        .item .edit,
        .item .close {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #333;
            color: #fff;
            position: absolute;
            right: 10px;
            top: 50%;
            margin-top: -10px;
            display: none;
            text-align: center;
            font-size: 14px;
            line-height: 20px;
            cursor: pointer;
        }

        .item .edit {
            right: 36px;
        }

        .item .edit:hover,
        .item .close:hover {
            background: @primary-color;
        }

        .item:hover {
            background: #ededed;
        }

        .item:hover .edit,
        .item:hover .close {
            display: inline-block;
        }

        .item.active {
            background: @file-modal-selected-color;
        }
    }
}

.m-file-modal__list {
    height: 400px;
    width: 600px;
    position: relative;
    overflow: hidden;

    .empty {
        position: relative;
        padding-top: 100px;
    }

    .file-item {
        width: 110px;
        position: relative;
        cursor: pointer;
        border-radius: 2px;
        padding: 4px;
        border: 1px solid rgba(0, 0, 0, .05);
        float: left;
        margin: 5px;
        -webkit-transition: All .2s ease-in-out;
        transition: All .2s ease-in-out;

        &__cover {
            margin: 0 auto;
            width: 95px;
            height: 95px;
            background: no-repeat 50%/100%;
        }

        &__name {
            font-size: 12px;
            text-align: center;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
        }

        &__selected-mask {
            display: none;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: @file-modal-mask-color;
            text-align: center;
            border-radius: 2px;
            z-index: 9;
            vertical-align: middle;
        }


        &__selected-icon {
            font-size: 48px;
            color: @primary-color;
            text-align: center;
            vertical-align: middle;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-left: -23px;
            margin-top: -30px;
        }

        &.active {
            border-color: @primary-color;
        }

        &.active .file-item__selected-mask {
            display: block !important;
        }
    }
}

.m-file-modal__paging {
    text-align: center;
    margin-top: 10px;
}

.m-file-modal__upload {
    padding: 10px 11px 10px 5px;

    .ant-form-item {
        margin-bottom: 0 !important;
    }
}

.m-file-modal__upload_desc {
    font-size: 12px;
    padding-left: 10px;
    color: #999;
}

.selected-desc {
    color: #999;
    margin-right: 10px;
}
</style>
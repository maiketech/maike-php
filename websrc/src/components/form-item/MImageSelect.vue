<template>
    <div class="m-image-select">
        <a-image-preview-group>
            <draggable v-if="selectedItems.length" v-model="selectedItems" @start="dragging = true" @end="dragging = false"
                item-key="file_id">
                <template #item="{ element }">
                    <div class="m-image-select__item">
                        <a-tooltip placement="topLeft" :title="element.file_name">
                            <div class="image-cover" :style="{ backgroundImage: `url('${element.thumb}')` }"></div>
                        </a-tooltip>
                        <a-tooltip placement="topRight" title="删除">
                            <icon class="m-image-select__item-remove" type="m-qingkong"
                                @click="deleteSelected(element.file_id)" />
                        </a-tooltip>
                    </div>
                </template>
            </draggable>
        </a-image-preview-group>
        <div class="m-image-select__button" v-if="fileCount > 0" @click="onShowModal">
            <plus-outlined class="icon-plus" />
        </div>

        <MFileModal :show="modalShow" file-type="image" :count="fileCount" @ok="onSelectedChange" @close="onCloseModal">
        </MFileModal>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch, computed } from 'vue';
import { PlusOutlined } from '@ant-design/icons-vue';
import draggable from 'vuedraggable-es';
import { isArray } from '@/utils/is';
import req from '@/utils/request';
import { isEmpty } from 'lodash-es';

const emit = defineEmits(["update:value","change"]);
//组件属性
interface Props {
    value?: any,
    count?: number,
    resultType?: string
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    count: 10,
    resultType: 'url' //url图片地址，ID图片附件ID
});

const loading = ref<boolean>(true);
const modalShow = ref<boolean>(false);
const dragging = ref<boolean>(false);
const selectedItems = ref<any>([]);

const fileCount = computed(() => {
    return props.count - selectedItems.value.length
});

const getFileList = (fileIds: any) => {
    if (!fileIds || fileIds == undefined || fileIds == null || fileIds == '' || fileIds.length == 0) {
        selectedItems.value = [];
    } else {
        loading.value = true;
        req.post('/system.attachment/get_options', { type: 'image', file_ids: fileIds }).then(res => {
            selectedItems.value = res.data;
        }).catch(() => {
            selectedItems.value = [];
        }).finally(() => {
            loading.value = false;
        })
    }
};

const buildList = (attachs: any) => {
    if (isEmpty(attachs)) return;
    if (!isArray(attachs)) {
        attachs = [attachs];
    }
    let items: any = [];
    for (let i = 0; i < attachs.length; i++) {
        items[i] = {
            file_id: i + 1,
            file_name: '图片' + (i + 1),
            thumb: attachs[i],
            file_url: attachs[i]
        }
    }
    selectedItems.value = items;
}

const deleteSelected = (fid: number) => {
    let idx = 0;
    selectedItems.value.map((item: any, index: number) => {
        if (item.file_id == fid) {
            idx = index;
        }
    });
    selectedItems.value.splice(idx, 1);
    buildResult(selectedItems.value);
};

const onShowModal = () => {
    modalShow.value = true;
}

const onCloseModal = () => {
    modalShow.value = false;
}

const buildResult = (attachList: any) => {
    if (attachList.length > 0) {
        if (props.count == 1) {
            emit("change", attachList[0].file_url);
            emit("update:value", attachList[0].file_url);
        } else {
            let urls: any = [];
            for (let i = 0; i < attachList.length; i++) {
                urls[i] = attachList[i].file_url;
            }
            emit("change", urls);
            emit("update:value", urls);
        }
    }
}

//onSelectedChange
const onSelectedChange = (attachIds: any, attachList: any) => {
    selectedItems.value = [...selectedItems.value, ...attachList];
    if (props.resultType === 'url') {
        buildResult(selectedItems.value);
    } else {
        if (props.count == 1) {
            emit("change", attachList[0]);
            emit("update:value", attachIds[0]);
        } else {
            emit("change", attachIds);
            emit("update:value", attachIds);
        }
    }
}

watch(
    () => props.value,
    (val, old) => {
        if (old !== val) {
            if (props.resultType === 'id') {
                getFileList(val);
            } else {
                buildList(val);
            }
        }
    },
    { deep: true, immediate: true }
);
</script>

<style lang="less">
.m-image-select {

    &__item {
        width: 80px;
        height: 80px;
        float: left;
        border: 1px solid #e2e2e2;
        border-radius: 3px;
        text-align: center;
        color: #dad9d9;
        cursor: pointer;
        background-color: #FFFFFF;
        margin-right: 5px;
        margin-bottom: 5px;
        overflow: hidden;
        position: relative;

        .image-cover {
            display: block;
            width: 100% !important;
            height: 100% !important;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        &-remove {
            font-size: 20px;
            color: #FFF;
            text-align: center;
            position: absolute;
            right: 5px;
            top: 5px;
            background-color: #FF6764;
            border-radius: 24px;
            z-index: 30;
            display: none;
        }

        &:hover {
            border: 1px dashed @primary-color;
            color: @primary-color;

            .m-image-select__item-remove {
                display: inline-block;
            }
        }
    }

    &__button {
        width: 80px;
        height: 80px;
        float: left;
        border: 1px dashed #e2e2e2;
        border-radius: 3px;
        text-align: center;
        color: #dad9d9;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #FAFAFA;
        overflow: hidden;

        &:hover {
            border: 1px dashed @primary-color;
            color: @primary-color;
        }

        .icon-plus {
            font-size: 32px;
        }
    }
}
</style>
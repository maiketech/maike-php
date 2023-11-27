<!--
 * @LastEditTime: 2023-11-16 00:42:23
-->
<template>
	<div class="m-file-select">
		<a-button @click="onShowModal"><upload-outlined></upload-outlined>上传文件</a-button>
		<div class="file-list">
			<div class="file-item" v-for="(file, index) in selectedItems" :key="index">
				<div class="name">{{ index + 1 }}、{{ file.file_name }}</div>
				<a-button type="text" @click="deleteSelected(index)"><delete-outlined /></a-button>
			</div>
		</div>

		<MFileModal :show="modalShow" file-type="file" :count="fileCount" @ok="onSelectedChange" @close="onCloseModal">
		</MFileModal>
	</div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch, computed } from 'vue';
import { UploadOutlined, DeleteOutlined } from '@ant-design/icons-vue';
import { isArray } from '@/utils/is';
import req from '@/utils/request';
import { isEmpty } from 'lodash-es';

const emit = defineEmits(["update:value", "change"]);
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
const selectedItems = ref<any>([]);

const fileCount = computed(() => {
	return props.count - selectedItems.value.length
});

const getFileList = (fileIds: any) => {
	if (!fileIds || fileIds == undefined || fileIds == null || fileIds == '' || fileIds.length == 0) {
		selectedItems.value = [];
	} else {
		loading.value = true;
		req.post('/system.attachment/get_options', { type: 'file', file_ids: fileIds }).then(res => {
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
			file_name: '文件' + (i + 1),
			thumb: attachs[i],
			file_url: attachs[i]
		}
	}
	selectedItems.value = items;
}

const deleteSelected = (index: number) => {
	selectedItems.value.splice(index, 1);
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
			emit("change", attachIds[0]);
			emit("update:value", attachIds[0]);
		} else {
			emit("change", attachIds);
			emit("update:value", attachIds);
		}
	}
}

// watch(
// 	() => props.value,
// 	(val, old) => {
// 		if (old !== val) {
// 			if (props.resultType === 'id') {
// 				getFileList(val);
// 			} else {
// 				buildList(val);
// 			}
// 		}
// 	},
// 	{ deep: true, immediate: true }
// );
</script>

<style lang="less" scoped>
.file-item {
	display: flex;
	align-items: center;
	justify-content: space-between;

	.name {}
}
</style>
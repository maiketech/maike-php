<template>
	<div class="page-main">
		<MPageHeader title="系统参数" sub-title="Config"></MPageHeader>
		<MCard>
			<MForm ref="formRef" submitText="保存设置" :resetText="null" :loading="formLoading" :items="formItems" :value="formValues" @submit="onFormSubmit">
			</MForm>
		</MCard>
	</div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Modal, message } from 'ant-design-vue';
import req from '@/maike/request';

const formLoading = ref<boolean>(false);
const formItems = ref<any>([]);
const formValues = ref<any>([]);

const onFormSubmit = (values: any) => {
	req.post('/system.setting/system', { values: values }).then((res) => {
		Modal.success({
			title: '成功',
			content: "系统设置已保存"
		})
	}).catch((err) => {
		Modal.error({
			title: '失败',
			content: "系统设置失败"
		})
	});
}

const getSettingForm = () => {
	req.get('/system.setting/system', {}).then((res) => {
		formItems.value = res.data;
	})
}

onMounted(() => {
	getSettingForm();
})
</script>
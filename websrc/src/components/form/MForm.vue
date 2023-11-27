<template>
	<div :style="{ width: (isNumber(width) ? `${width}px` : width), height: (isNumber(height) ? `${height + 70}px` : height) }"
		class="m-form">
		<a-form ref="formRef" :model="formModel" :layout="formLayout" :label-col="labelCol" :wrapper-col="wrapperCol"
			autocomplete="off" @finish="onSubmit" @finishFailed="onFinishFailed">
			<template v-if="!isTabForm">
				<template v-for="(row, index) in formItems" :key="index">
					<div :class="'m-form__row_' + layout">
						<template v-for="(item, index2) in row" :key="index2">
							<div class="m-form__col">
								<MFormItem :config="itemConfig[item.name]" v-model:value="formModel[item.name]"
									:show="showFormItem(item.show)" @change="onItemChange($event, item.name)">
								</MFormItem>
							</div>
						</template>
					</div>
				</template>
			</template>
			<template v-if="isTabForm">
				<a-tabs v-model:activeKey="curTabsActive" :tab-position="tabsPosition"
					:style="{ height: (isNumber(height) ? height + 'px' : height), overflowY: 'auto', overflowX: 'hidden' }">
					<a-tab-pane v-for="(group, idx) in formItems" :key="idx" :tab="group.title">
						<template v-for="(row, index) in group.items" :key="index">
							<div :class="'m-form__row_' + layout">
								<template v-for="(item, index2) in row" :key="index2">
									<div class="m-form__col">
										<MFormItem :config="itemConfig[item.name]" v-model:value="formModel[item.name]"
											:show="showFormItem(item.show)" @change="onItemChange($event, item.name)">
										</MFormItem>
									</div>
								</template>
							</div>
						</template>
					</a-tab-pane>
				</a-tabs>
			</template>
			<div :class="'m-form__row_' + layout" v-if="showButton && (submitText != '' && submitText != null)">
				<div class="m-form__btn">
					<a-form-item>
						<a-button type="primary" html-type="submit" :disabled="disabled || loading" :loading="loading">
							{{ submitText }}</a-button>
						<a-button style="margin-left: 10px" @click="reset" :disabled="loading || disabled"
							v-if="resetText != '' && resetText != null">{{ resetText }}</a-button>
					</a-form-item>
				</div>
			</div>
		</a-form>
	</div>
</template>

<script lang="ts" setup>
import { ref, computed, watch } from 'vue';
import dayjs from 'dayjs';
import 'dayjs/locale/zh-cn';
import _ from "lodash-es";
import { isArray, isNumber, isObjectEq, isEmpty, isString, isObject } from '@/utils/is';

dayjs.locale('zh-cn');

const emit = defineEmits(["submit", "reset", "update:value", "itemChange", "change"]);

//组件属性
interface Props {
	showButton?: boolean,
	submitText?: (string | null),
	resetText?: (string | null),
	labelSpan?: any,
	wrapperSpan?: any,
	layout?: string,
	width?: (number | string),
	height?: (number | string),
	items?: any,
	value?: any,
	loading?: boolean,
	disabled?: boolean,
	tabsActive?: number,
	tabsPosition?: "left" | "right" | "top" | "bottom",
}
const props = withDefaults(defineProps<Props>(), {
	showButton: true,
	submitText: "提交",
	resetText: "重置",
	labelSpan: { style: { width: '150px' } },
	wrapperSpan: { span: 17 },
	layout: 'h', //'h'|'v'|'hm'|'vm'|'i'
	width: '100%',
	height: 'auto',
	items: null,
	value: null,
	loading: false,
	disabled: false,
	tabsActive: 0,
	tabsPosition: 'left'
})

const curTabsActive = ref<number>(0);
const formModel = ref<any>({});
const formRef = ref();
const formItems = ref<any>([]);
const formItemsRelation = ref<any>({});
const itemConfig = ref<any>({});
const initDone = ref<boolean>(false);

const labelCol = computed(() => {
	if (props.layout == 'v') {
		return {};
	} else if (props.layout == 'hm') {
		return {};
	} else if (props.layout == 'vm') {
		return {};
	} else if (props.layout == 'i') {
		return {};
	}
	return props.labelSpan;
})

const wrapperCol = computed(() => {
	//'h'|'v'|'hm'|'vm'|'i'
	if (props.layout == 'v') {
		return {};
	} else if (props.layout == 'hm') {
		return {};
	} else if (props.layout == 'vm') {
		return {};
	} else if (props.layout == 'i') {
		return {};
	}
	return props.wrapperSpan;
})

const formLayout = computed(() => {
	//'horizontal'|'vertical'|'inline',
	if (props.layout == 'i') {
		return 'inline';
	} else if (props.layout == 'h' || props.layout == 'hm') {
		return 'horizontal';
	} else if (props.layout == 'v' || props.layout == 'vm') {
		return 'vertical';
	} else {
		return 'horizontal';
	}
})

//判断是否Tab类型表单
const isTabForm = computed(() => {
	let _is = false;
	if (!props.items || !isArray(props.items)) return false;
	props.items.map((item: any) => {
		if (item.hasOwnProperty("items")) {
			_is = true;
			return;
		}
	})
	return _is;
})


//初始化表单
const initFormValues = (items: any, values: any = {}) => {
	let newValues: any = {};
	for (let k in items) {
		if (values && isObject(values) && values.hasOwnProperty(k)) {
			newValues[k] = values[k];
		} else {
			newValues[k] = items[k].value;
		}
	}
	formModel.value = newValues;
	initDone.value = true;
}
const initFormItems = (items: any = []) => {
	let itemsList: any = _.cloneDeep(items);
	let config: any = {};

	if (isTabForm.value) {
		//Tab型表单
		itemsList.forEach((tab: any, tabIndex: number) => {
			const tabItems: any = _.toArray(tab.items);
			tabItems.forEach((row: any, rowIndex: number) => {
				row.forEach((subRow: any, subIndex: number) => {
					if (isArray(subRow)) {
						subRow.forEach((item: any, itemIndex: number) => {
							config[item.name] = item;
						});
					} else {
						config[subRow.name] = subRow;
					}
				});
			});
		});
	} else {
		//普通表单
		itemsList.forEach((row: any, rowIndex: number) => {
			row.forEach((subRow: any, subIndex: number) => {
				if (isArray(subRow)) {
					subRow.forEach((item: any, itemIndex: number) => {
						config[item.name] = item;
					});
				} else {
					config[subRow.name] = subRow;
				}
			});
		});
	}
	formItems.value = itemsList;
	itemConfig.value = config;
	initFormValues(config, props.value);
}
//更新关联查询参数
const buildFormItemQuery = (field: string, value: any) => {
	let config = _.cloneDeep(itemConfig.value);
	for (let k in config) {
		if (config[k].hasOwnProperty("relation") && !isEmpty(config[k].relation)) {
			let relation = _.toArray(config[k].relation);
			if (relation.includes(field)) {
				itemConfig.value[k]['params'][field] = value;
			}
		}
	}
}
/**
 * 判断是否显示表单项
 * {s:'=',field:'name',value:'李小'}
 */
const showFormItem = (config: any) => {
	if (!config || config == undefined || config == null) return true;
	let formData: any = _.cloneDeep(formModel.value);
	if (config.s === "=") {
		return formData[config.field] == config.value;
	} else if (config.s === ">") {
		return formData[config.field] > config.value;
	} else if (config.s === "<") {
		return formData[config.field] < config.value;
	} else if (config.s === ">=") {
		return formData[config.field] >= config.value;
	} else if (config.s === "<=") {
		return formData[config.field] <= config.value;
	} else if (config.s === "!=") {
		return formData[config.field] != config.value;
	} else if (config.s === "in") {
		return formData[config.field].indexOf(config.value) > -1;
	} else if (config.s === "out") {
		return formData[config.field].indexOf(config.value) < 0;
	}
	return false;
};

//表单项值变动事件
const onItemChange = (value: any, field: string = '') => {
	buildFormItemQuery(field, value)
	emit("itemChange", value, field);
}

//表单提交事件
const onSubmit = (values: any) => {
	emit("submit", values);
};

//表单提交错误事件
const onFinishFailed = (errorInfo: any) => {
	console.log('Failed:', errorInfo);
};

//重置表单
const reset = () => {
	formRef.value.resetFields();
	curTabsActive.value = 0;
	emit("reset", {});
};

//提交表单
const submit = () => {
	formRef.value
		.validateFields()
		.then((values: any) => {
			emit("submit", values);
		})
		.catch((info: any) => {
		});
}

//监听
watch(
	() => props.items,
	(val, old) => {
		console.log("form-items",val)
		if (val && val != null && !isObjectEq(val, old)) {
			initFormItems(val);
		}
	},
	{ deep: true, immediate: true }
);

watch(
	() => props.value,
	(val, old) => {
		if (initDone.value && val && !isEmpty(val) && !isObjectEq(val, old)) {
			initFormValues(itemConfig.value, val);
		}
	},
	{ deep: true, immediate: true }
);

watch(
	() => formModel.value,
	(val: any, old: any) => {
		emit("change", val);
	},
	{ deep: true, immediate: true }
);

watch(
	() => props.tabsActive,
	val => {
		if (curTabsActive.value != val) {
			curTabsActive.value = val;
		}
	}
);

//暴露方法
defineExpose({ submit, reset });
</script>

<style lang="less">
.m-form {
	overflow: auto;
	padding: 0 !important;
	margin: 0 !important;
	padding-right: 10px !important;

	.ant-form.ant-form-inline {
		.ant-row.ant-form-item {
			margin-top: 5px !important;
			margin-bottom: 5px !important;
		}
	}

	//'horizontal'|'vertical'|'inline'
	&__row_h {
		display: flex;
		justify-content: flex-start;
		flex-flow: wrap;

		.m-form__col {
			display: inline-block;
			margin-right: 10px;
			width: 100%;
		}

		.m-form__btn {
			display: inline-block;
			margin: 0 auto;
		}
	}

	&__row_hm {
		display: flex;
		justify-content: flex-start;
		flex-flow: wrap;

		.m-form__col {
			display: inline-block;
			margin-right: 10px;
			width: auto;
		}

		.ant-form-item {
			margin-bottom: 10px !important;
		}
	}

	&__row_v {}

	&__row_vm {
		display: flex;
		justify-content: flex-start;
		flex-flow: wrap;

		.m-form__col {
			display: inline-block;
		}

		.ant-form-item {
			margin-bottom: 10px !important;
			margin-right: 10px !important;
		}
	}

	&__row_i {

		.m-form__col {
			display: inline-block;
		}
	}
}

.m-file-input {
	position: absolute;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	outline: none;
	filter: alpha(opacity=0);
	-moz-opacity: 0;
	-khtml-opacity: 0;
	opacity: 0;
	cursor: pointer;
}
</style>
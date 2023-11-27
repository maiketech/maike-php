<template>
    <a-form-item :label="config.label" :name="config.name" :rules="config.rules" :help="config.help" :key="key" v-if="show">
        <a-input :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" v-if="config.type == 'text'" @change="onItemChange" />
        <a-input :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" type="password" v-if="config.type == 'password'" @change="onItemChange" />
        <a-input-number :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" v-if="config.type == 'number'" @change="onItemChange" />
        <a-textarea :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" :rows="4" v-if="config.type == 'textarea'" @change="onItemChange" />
        <a-checkbox-group :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" :options="config.options" v-if="config.type == 'checkbox'" @change="onItemChange" />
        <a-radio-group :disabled="config.disabled" button-style="solid" v-model:value="itemValue"
            :placeholder="config.placeholder" :style="config.style" :options="config.options" v-if="config.type == 'radio'"
            @change="onItemChange" />
        <a-date-picker :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style"
            :format="config.options && config.options.valueFormat ? config.options.valueFormat : 'YYYY-MM-DD'"
            :valueFormat="config.options && config.options.valueFormat ? config.options.valueFormat : 'YYYY-MM-DD'"
            :picker="config.options && config.options.picker ? config.options.picker : 'date'"
            :show-time="config.options && config.options.show_time ? config.options.show_time : false"
            v-if="config.type == 'date'" @change="onItemChange" />
        <a-range-picker :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :valueFormat="config.options && config.options.valueFormat ? config.options.valueFormat : 'YYYY-MM-DD HH:mm:ss'"
            :style="config.style" :show-time="config.options.show_time" v-if="config.type == 'date_range'"
            :locale="datePickerLocale" @change="onItemChange" />
        <a-time-picker :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" v-if="config.type == 'time'" @change="onItemChange" />
        <a-time-range-picker :disabled="config.disabled" v-model:value="itemValue" :placeholder="config.placeholder"
            :style="config.style" v-if="config.type == 'time_range'" @change="onItemChange" />
        <a-select :disabled="config.disabled" :options="config.options ? config.options : null"
            :mode="config?.mode || 'select'" :style="config.style" v-model:value="itemValue"
            :placeholder="config.placeholder" v-if="config.type == 'select'" @change="onItemChange" />
        <a-switch :disabled="config.disabled" :checkedChildren="config.options[0].label"
            :checkedValue="config.options[0].value" :unCheckedChildren="config.options[1].label"
            :unCheckedValue="config.options[1].value" v-model:checked="itemValue" :placeholder="config.placeholder"
            v-if="config.type == 'switch'" @change="onItemChange" />

        <!--自定义组件-->
        <MImageSelect :disabled="config.disabled" :style="config.style" v-model:value="itemValue"
            :count="config.options?.count || 10" v-if="config.type == 'image_select'" @change="onItemChange" />
        <MVideoSelect :disabled="config.disabled" :style="config.style" v-model:value="itemValue"
            :count="config.options?.count || 10" v-if="config.type == 'video_select'" @change="onItemChange" />
        <MEditor :disabled="config.disabled" v-model:value="itemValue" :width="config.style?.width"
            :height="config.style?.height" v-if="config.type == 'editor'" @change="onItemChange" />
        <MActionSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :options="config.options ? config.options : null" :mode="config?.mode"
            v-if="config.type == 'action_select'" @change="onItemChange" />
        <MRoleSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :options="config.options ? config.options : null" :mode="config?.mode"
            v-if="config.type == 'role_select'" @change="onItemChange" />
        <MDictItemSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :params="config.params || {}" :options="config.options ? config.options : null"
            :mode="config?.mode" v-if="config.type == 'dict_item_select'" @change="onItemChange" />
        <MDictSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :options="config.options ? config.options : null" :mode="config?.mode"
            v-if="config.type == 'dict_select'" @change="onItemChange" />
        <MArticleCategorySelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :mode="config?.mode" :options="config.options ? config.options : null"
            v-if="config.type == 'article_category_select'" @change="onItemChange" />
        <MAdminSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :mode="config?.mode" :options="config.options ? config.options : null"
            v-if="config.type == 'admin_select'" @change="onItemChange" />
        <MChargesType v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :options="config.options ? config.options : null"
            v-if="config.type == 'charges_type_select'" @change="onItemChange" />
        <MChargesPeriodSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :options="config.options ? config.options : null"
            v-if="config.type == 'charges_period_select'" @change="onItemChange" />
        <MStoreSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :params="config.params" :disabled="config.disabled" :mode="config?.mode"
            :options="config.options ? config.options : null" v-if="config.type == 'store_select'" @change="onItemChange" />
        <MChargesSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :params="config.params" :disabled="config.disabled" :options="config.options ? config.options : null"
            v-if="config.type == 'charges_select'" @change="onItemChange" />
        <MPayTypeSelect v-model:value="itemValue" :style="config.style" :placeholder="config.placeholder"
            :disabled="config.disabled" :options="config.options ? config.options : null"
            v-if="config.type == 'pay_type_select'" @change="onItemChange" />
    </a-form-item>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import datePickerLocale from 'ant-design-vue/es/date-picker/locale/zh_CN';
import dayjs from 'dayjs';
import 'dayjs/locale/zh-cn';
dayjs.locale('zh-cn');

const emit = defineEmits(["update:value", "change"]);
//组件属性
interface Props {
    value?: any,
    config?: any
    show?: boolean,
    key?: string
}
const props = withDefaults(defineProps<Props>(), {
    value: null,
    config: {},
    show: true,
    key: ''
})

const itemValue = ref<any>();

const onItemChange = (value: any) => {
    emit("change", value);
    //emit("update:value", value);
}

watch(
    () => props.value,
    val => {
        itemValue.value = val;
    },
    { deep: true, immediate: true }
);

watch(
    () => itemValue.value,
    val => {
        emit("update:value", val);
    },
    { deep: true, immediate: true }
);

</script>
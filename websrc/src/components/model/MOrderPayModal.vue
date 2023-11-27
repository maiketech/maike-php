<template>
    <a-modal :visible="show" :title="title" :maskClosable="false" :width="width" @cancel="onClose">
        <template #footer>
            <!-- <a-button class="ml20" :disabled="payLoading" @click="onClose">取消</a-button>
            <a-button type="primary" :disabled="payLoading" :loading="payLoading" danger
                @click="onConfirmPay">确认缴费</a-button> -->
        </template>

    </a-modal>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch, computed } from 'vue';
import { Modal, message } from 'ant-design-vue';
import { isNumber } from '@/utils/is';
import req from '@/utils/request';

const emit = defineEmits(["update:show"]);

//组件属性
interface Props {
    show: boolean,
    title: string,
    width: (number | string),
    height: (number | string),
    info?:any
}
const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '缴费单详情',
    width: 900,
    height: '360px',
    info:{}
});


const onClose = () => {
    emit("update:show", false);
}

</script>
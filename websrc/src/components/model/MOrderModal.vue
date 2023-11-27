<template>
    <a-modal :visible="show" :title="modalTitle" :maskClosable="false" :width="width" @cancel="onClose">
        <template #footer>
            <!-- <a-button class="ml20" :disabled="payLoading" @click="onClose">取消</a-button>
            <a-button type="primary" :disabled="payLoading" :loading="payLoading" danger
                @click="onConfirmPay">确认缴费</a-button> -->
        </template>

        <a-descriptions size="small" :labelStyle="{ width: '100px' }" bordered :column="2">
            <a-descriptions-item label="状态" :span="2">
                <a-tag :color="info.status_color">{{ info.status_text }}</a-tag>
            </a-descriptions-item>
            <a-descriptions-item label="账单编号">{{ info.order_no }}</a-descriptions-item>
            <a-descriptions-item label="账单日期">{{ info.order_date }}</a-descriptions-item>
            <a-descriptions-item label="缴费项目">{{ info.charges_title }}</a-descriptions-item>
            <a-descriptions-item label="单价">{{ info.price }}</a-descriptions-item>
            <a-descriptions-item label="账单金额"><strong>{{ info.order_money }}</strong></a-descriptions-item>
            <a-descriptions-item label="实付金额"><strong style="color:red;">{{ info.pay_money }}</strong></a-descriptions-item>
            <a-descriptions-item label="计费区间" :span="2"><strong>{{ info.time_desc }}</strong></a-descriptions-item>
            <!-- // 计算公式：0单价，1店铺面积x单价，2单价x数量，3电费（单价x用电量），4水费（单价x用水量） -->
            <a-descriptions-item label="面积" :span="2" v-if="info.charges_type==1"><strong>{{ info.num }}</strong>m²</a-descriptions-item>
            <a-descriptions-item label="用量" :span="2" v-if="info.charges_type==2"><strong>{{ info.num }}</strong></a-descriptions-item>
            <a-descriptions-item label="用量" :span="2" v-if="info.charges_type==3">
                <strong>{{ info.num }}</strong>度 （{{ info.value2 }}-{{ info.value1 }}={{ info.num }}）
            </a-descriptions-item>
            <a-descriptions-item label="用量" :span="2" v-if="info.charges_type==4">
                <strong>{{ info.num }}</strong>立方 （{{ info.value2 }}-{{ info.value1 }}={{ info.num }}）
            </a-descriptions-item>
            
            <a-descriptions-item label="商铺"><strong>{{ info.store?.store_no }}</strong></a-descriptions-item>
            <a-descriptions-item label="所属区域">{{ info.store?.area_title }}</a-descriptions-item>
            <a-descriptions-item label="创建时间">{{ info.create_time }}</a-descriptions-item>
            <a-descriptions-item label="缴费时间">{{ info.pay?.pay_time_text }}</a-descriptions-item>
            <a-descriptions-item label="缴费渠道">{{ info.pay?.channel_text }}</a-descriptions-item>
            <a-descriptions-item label="付款方式">{{ info.pay?.pay_type_text }}</a-descriptions-item>
            <a-descriptions-item label="付款人">{{ info.pay?.payer_name }}</a-descriptions-item>
            <a-descriptions-item label="收款人">{{ info.pay?.admin_name }}</a-descriptions-item>
        </a-descriptions>
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
    title?: string,
    width?: (number | string),
    height?: (number | string),
    info?: any
}
const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '账单详情',
    width: 900,
    height: '360px',
    info: {}
});

const modalTitle = computed(() => {
    return props.title + ' - ' + (props.info ? props.info.order_no : '');
})

const onClose = () => {
    emit("update:show", false);
}
</script>
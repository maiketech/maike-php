<template>
    <a-modal :visible="show" :title="title" :maskClosable="false" :width="width">
        <template #footer>
            <a-button class="ml20" :disabled="payLoading" @click="onClose">取消</a-button>
            <a-button type="primary" :disabled="payLoading" :loading="payLoading" danger
                @click="onConfirmPay">确认缴费</a-button>
        </template>

        <a-row type="flex" :gutter="20" :style="{ height: height, }">
            <a-col flex="450px">
                <MForm ref="payForm" layout="h" :showButton="false" :items="payFormItems" v-model:value="payFormData"
                    @submit="onPayFormSubmit">
                </MForm>
            </a-col>
            <a-col flex="auto">
                <template v-if="payOrderData&&payOrderData.length>0">
                    <div class="order-select-list" :style="{ height: 'calc(' + height + ' - 20px)' }">
                        <a-checkbox-group v-model:value="payOrderIds">
                            <div class="r-item" v-for="(item, index) in payOrderData" :key="index">
                                <a-checkbox class="r-item__header" :value="item.order_id">
                                    {{ item.order_date }} {{ item.charges_title }}
                                    <div class="r-item__price">{{ item.order_money }}元</div>
                                </a-checkbox>
                                <div class="r-item__inner">
                                    <div>计费区间：{{ item.time_desc }}</div>
                                    <div>
                                        单价：{{ item.price }}
                                        <span class="ml20">用量：{{ item.num }}</span>
                                        <span>（{{ item.value2 }} - {{ item.value1 }}）</span>
                                    </div>
                                </div>
                            </div>
                        </a-checkbox-group>
                    </div>
                    <div class="r-checkall">
                        <a-checkbox :checked="payCheckAll" :indeterminate="payIdm" @change="onPayCheckAllChange">
                            全选
                        </a-checkbox>
                    </div>
                </template>
                <template v-if="!payOrderData || payOrderData.length < 1">
                    <a-empty description="暂无账单" style="margin-top: 50px;" />
                </template>
            </a-col>
        </a-row>
        <a-alert type="warning" class="mt20">
            <template #message>
                <div class="r-total">
                    <div class="r-total__h">【合计】</div>
                    <div class="r-total__t"><strong>{{ payOrderTotal.total_money }}</strong>元</div>
                </div>
            </template>
        </a-alert>
    </a-modal>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch, computed } from 'vue';
import { Modal, message } from 'ant-design-vue';
import { isNumber } from '@/utils/is';
import req from '@/utils/request';

const emit = defineEmits(["success", "error", "update:show"]);

//组件属性
interface Props {
    show: boolean,
    title: string,
    width: (number | string),
    height: (number | string)
}
const props = withDefaults(defineProps<Props>(), {
    show: false,
    title: '缴费',
    width: 900,
    height: '360px'
});

const payLoading = ref<boolean>(false);
const payForm = ref();
const payFormData = ref<any>({
    area_id: null,
    store_id: null,
    charges_id: [],
    fangwu_id: null
});
const payFormItems = ref<any>([
    <FormItem[]>[
        {
            label: "区域",
            name: "area_id",
            type: "dict_item_select",
            params: { dict: "area" },
            placeholder: "请选择区域",
            value: null
        },
    ],
    <FormItem[]>[
        {
            label: "商铺",
            name: "store_id",
            type: "store_select",
            placeholder: "请选择商铺",
            value: null,
            relation: ['area_id'],
            rules: [
                {
                    required: true,
                    message: "请请选择商铺",
                    trigger: " change",
                },
            ]
        },
    ],
    <FormItem[]>[
        {
            label: "缴费项目",
            name: "charges_id",
            type: "charges_select",
            placeholder: "请选择缴费项目",
            value: null,
            relation: ['area_id', 'store_id']
        },
    ],
    <FormItem[]>[
        {
            label: "付款方式",
            name: "pay_type",
            type: "pay_type_select",
            placeholder: "请选择付款方式",
            value: null,
            rules: [
                {
                    required: true,
                    message: "请选择付款方式",
                    trigger: " change",
                },
            ]
        },
    ],
    <FormItem[]>[
        {
            label: '备注',
            name: 'desc',
            type: 'textarea',
            placeholder: '填写备注',
            value: ''
        },
    ],
]);
const payOrderData = ref<any>([]);
const payOrderIds = ref<any>([]);
const payOrderTotal = ref<any>({
    total_money: 0
});
const payCheckAll = ref<boolean>(false);
const payIdm = ref<boolean>(false);

const onPayCheckAllChange = (e: any) => {
    let arr: any = [];
    if (e.target.checked) {
        payOrderData.value.forEach((item: any) => {
            arr.push(item.order_id);
        });
        payOrderIds.value = arr;
    } else {
        payOrderIds.value = [];
    }
};

const updateTotal = (selectIds: any) => {
    let totalInfo = {
        total_money: 0
    };
    const selectIdsArr: any = Object.values(selectIds);
    payOrderData.value.forEach((item: any) => {
        if (selectIdsArr.includes(item.order_id)) {
            totalInfo.total_money += parseFloat(item.order_money);
        }
    });
    totalInfo.total_money = parseFloat(totalInfo.total_money.toFixed(2));
    payOrderTotal.value = totalInfo;
};

const handlePayCheckout = (isPay: number = 0) => {
    payLoading.value = true;
    const params = {
        ...payFormData.value,
        is_pay: isPay,
        order_id: payOrderIds.value
    }
    req.post("/fee.order_pay/pay", params).then((res: any) => {
        if (isPay == 1) {
            message.success(res.msg ? res.msg : '缴费成功');
            emit("success", res.data);
        } else {
            payOrderData.value = res.data;
        }
    }).catch((err: any) => {
        if (isPay == 1) {
            Modal.error({
                centered: true,
                title: '缴费失败',
                content: err.msg ? err.msg : '缴费失败',
            });
            emit("error", err);
        }
    }).finally(() => {
        payLoading.value = false;
    })
}

const onPayFormSubmit = (values: any) => {
    if (payOrderIds.value.length < 1) {
        message.error("请选择账单");
        return;
    }
    Modal.confirm({
        centered: true,
        title: '确定缴费吗?',
        content: '请核对账单及金额',
        okText: '确定缴费',
        okType: 'danger',
        cancelText: '取消',
        async onOk() {
            try {
                handlePayCheckout(1);
            } catch (e) {
            }
        }
    })
}

const onConfirmPay = () => {
    payForm.value.submit();
}

const onClose = () => {
    emit("update:show", false);
}

watch(
    () => payFormData.value,
    (val, old) => {
        if (!payLoading.value) {
            handlePayCheckout(0);
        }
    }, { immediate: true, deep: true }
);

watch(
    () => payOrderIds.value,
    (val, old) => {
        if (val != old) {
            updateTotal(val);
            payIdm.value = !!val.length && val.length < payOrderData.value.length;
            payCheckAll.value = val.length === payOrderData.value.length;
        }
    }
);

</script>

<style lang="less">
.order-select-list {
    height: calc(100% - 15px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 5px;
    margin-bottom: 10px;

    .ant-checkbox-group {
        width: 100%;
    }

    .r-item {
        width: 100%;
        background-color: #FAFAFA;
        border-radius: 5px;
        border: 1px solid #EFEFEF;
        margin-bottom: 10px;

        &__header {
            border-bottom: 1px solid #EFEFEF;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            width: 100% !important;
        }

        &__price {
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            color: #F44C46;
        }

        &__inner {
            padding: 10px 20px;
            font-size: 12px;
        }
    }
}

.r-checkall {
    padding-bottom: 15px;
    padding-left: 10px;
}

.r-total {
    text-align: center;

    div {
        display: inline-block;
        margin-right: 10px;
    }

    &__h {
        font-weight: bold;
        margin-right: 10px;
    }

    strong {
        color: red;
    }
}
</style>
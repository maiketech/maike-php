<template>
    <div class="m-n-card" :style="{ background: background, color: color }">
        <div class="m-n-card__inner">
            <div class="m-n-card__icon">
                <icon :type="icon" :style="{ fontSize: '40px', color: iconColor }"></icon>
            </div>
            <div class="m-n-card__content">
                <div class="c-value">{{ value }}</div>
                <div class="c-title">{{ title }}</div>
                <div class="c-desc" v-if="!$slots.desc">{{ desc }}</div>
                <div class="c-desc" v-if="$slots.desc">
                    <slot name="desc"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';

//组件属性
interface Props {
    title: string,
    icon: string,
    value: number | string,
    desc: string,
    color: string,
    iconColor: string,
    background: string
}
const props = withDefaults(defineProps<Props>(), {
    title: '标题',
    icon: 'xi-zhibiaoshouye',
    value: 0,
    desc: '',
    color: '#333333',
    iconColor: '#333333',
    background: '#FFFFFF'
})

</script>

<style lang="less">
.m-n-card {
    padding: 10px;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0px 0px 6px 0px rgba(129, 129, 129, 0.2);
    -webkit-box-shadow: 0px 0px 6px 0px rgba(129, 129, 129, 0.2);
    -moz-box-shadow: 0px 0px 6px 0px rgba(129, 129, 129, 0.2);

    &__inner {
        display: flex;
        flex-wrap: nowrap;
        justify-content: flex-start;
        min-height: 110px;
    }

    &__icon {
        width: 90px;
        position: relative;

        .anticon {
            width: 60px;
            height: 60px;
            border-radius: 45px;
            background: rgba(255, 255, 255, 0.3);
            position: absolute;
            top: 50%;
            margin-top: -30px;
            left: 10px;
            text-align: center;
            line-height: 65px;
            vertical-align: middle;
        }
    }

    &__content {
        padding: 10px 0;
        box-sizing: border-box;

        .c-title {
            font-size: 16px;
            font-weight: bold;
        }

        .c-value {
            font-size: 30px;
            font-weight: bold;
            line-height: 40px;

        }
    }
}
</style>
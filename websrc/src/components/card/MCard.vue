<template>
    <div class="m-card" :style="{ background: background, width: width, height: height }">
        <div class="m-card__inner">
            <div class="m-card__header" v-if="title != ''">
                <div class="c-title">{{ title }}</div>
                <div class="c-toolbar"></div>
            </div>
            <div class="m-card__content" :style="{ height: (title == '' ? '100%' : 'calc(100% - 45px)') }">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';

//组件属性
interface Props {
    title?: string,
    width?: string,
    height?: string,
    background?: string
}
const props = withDefaults(defineProps<Props>(), {
    title: '',
    width: '100%',
    height: 'auto',
    background: '#FFFFFF'
})
</script>

<style lang="less">
.m-card {
    padding: 15px 10px;
    box-sizing: border-box;
    border-radius: 8px;
    box-shadow: 0px 0px 6px 0px rgba(129, 129, 129, 0.2);
    position: relative;

    &__inner {height: 100%;}

    &__header {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        position: relative;
        height: 30px;
        line-height: 30px;
        padding: 0 15px;

        .c-title {
            font-size: 16px;
            font-weight: bold;
        }

        .c-toolbar {}
    }

    &__content {
        padding: 10px 15px;
        width: 100%;
        height: calc(100% - 45px);
        overflow: hidden;
        overflow-y: auto;
    }
}
</style>
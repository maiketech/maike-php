<template>
  <a-config-provider :locale="zhCN" :theme="{
    token: {
      colorPrimary: '#2F54EB',
    },
  }">
    <RouterView />
  </a-config-provider>
</template>

<script lang="ts" setup>
import zhCN from 'ant-design-vue/es/locale/zh_CN';
</script>

<style>
@import "./style/base.css";
@import "./style/theme.css";
</style>

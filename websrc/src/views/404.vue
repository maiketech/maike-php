<template>
    <div class="container-404">
        <div class="inner">
            <div class="title">Page Not Found</div>
            <div class="desc">访问的页面不存在</div>
            <div class="btnbar">
                <a-button type="primary" shape="round" size="large" class="btn" @click="onBack">返回</a-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
const onBack = () => {
    window.history.back();
}
</script>


<style >
.container-404 {
    width: 100vw;
    height: 100vh;
    background-color: #F9FBFC;

    .inner {
        padding: 200px 300px;
    }

    .title {
        font-size: 45px;
        color: #333;
        font-weight: bold;
    }

    .desc {
        font-size: 30px;
        color: #555;
    }

    .btnbar {
        margin: 50px 0;

        .btn {
            height: 40px;
            width: 140px;
            font-weight: bold;
            font-size: 18px;
        }
    }
}
</style>
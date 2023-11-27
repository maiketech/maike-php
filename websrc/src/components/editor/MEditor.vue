<template>
    <div :style="style" style="border: 1px solid #ccc">
        <Toolbar style="border-bottom: 1px solid #ccc" :editor="editorRef" :defaultConfig="toolbarConfig"
            :mode="editorMode" />
        <Editor :style="{ height: height }" v-model="content" :defaultConfig="editorConfig" :mode="editorMode"
            @onCreated="handleCreated" />

        <MFileModal v-model:show="imageSelectShow" :file-type="selectFileType" :count="10" @ok="onImageSelected">
        </MFileModal>
    </div>
</template>

<script lang="ts" setup>
import '@wangeditor/editor/dist/css/style.css' // 引入 css
import { onBeforeUnmount, ref, shallowRef, onMounted, watch } from 'vue'
import { useRoute } from "vue-router";
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import { IToolbarConfig, IButtonMenu, IDomEditor, Boot } from '@wangeditor/editor'
import req from '@/utils/request';

const emit = defineEmits(["update:value"]);

//组件属性
interface Props {
    value?: any,
    width?: string,
    height?: string,
    style?: any
}
const props = withDefaults(defineProps<Props>(), {
    value: '',
    width: '100%',
    height: '300px',
    style: {}
})

const content = ref('');
// 编辑器实例，必须用 shallowRef
const editorRef = shallowRef()
const toolbarConfig: Partial<IToolbarConfig> = {}
const editorConfig = <any>{ placeholder: '请输入内容...', MENU_CONF: {} };
editorConfig.MENU_CONF['uploadImage'] = {
    async customUpload(file: any, insertFn: any) {
        req.upload("/upload/image", file).then(res => {
            const images = res.data;
            images.map((item: any) => {
                insertFn(item.url)
            });
        }).catch(err => {
        })
    },
};
editorConfig.MENU_CONF['uploadVideo'] = {
    async customUpload(file: any, insertFn: any) {
        req.upload("/upload/video", file, 'video').then(res => {
            const videos = res.data;
            videos.map((item: any) => {
                insertFn(item.url)
            });
        }).catch(err => {
        })
    },
}
const editorMode = 'default'; // 或 'simple'
const imageSelectShow = ref<boolean>(false);
const selectFileType = ref('image');

const onImageSelected = () => {
};

watch(
    () => props.value,
    val => {
        if (content.value !== val) {
            content.value = val || '';
        }
    },
    { deep: true, immediate: true }
);

watch(
    () => content,
    val => {
        emit("update:value", val.value);
    },
    { deep: true, immediate: true }
);

const handleCreated = (editor: any) => {
    editorRef.value = editor // 记录 editor 实例，重要！
}

onMounted(() => {
})

// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
    const editor = editorRef.value
    if (editor == null) return
    editor.destroy()
})
</script>
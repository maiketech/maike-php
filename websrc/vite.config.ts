import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import Components from 'unplugin-vue-components/vite';
import { AntDesignVueResolver } from 'unplugin-vue-components/resolvers';
import path from "path";
const resolvePath = (dir: string) => path.join(__dirname, dir);

// https://vitejs.dev/config/
export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, "./");
  return {
    base: env.VITE_BASE_PATH,
    server: {
      hmr: true, // 开启热更新
      port: 3000,
      open: true,
      proxy: {
        "/api": {
          target: env.VITE_API_URL,
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api/, ""),
        },
      },
    },
    resolve: {
      alias: [
        {
          find: /@\//,
          replacement: resolvePath("src") + "/",
        }
      ],
    },
    plugins: [
      vue(),
      Components({
        resolvers: [
          AntDesignVueResolver({
            importStyle: false,
          }),
        ]
      })
    ]
  }
})

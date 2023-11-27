import fs from 'fs';
// 更新版本号
let data = fs.readFileSync('./package.json'); //fs读取文件
let packageData = JSON.parse(data.toString()); //转换为json对象
let arr = packageData.version.split('.'); //切割后的版本号数组
arr[2] = parseInt(arr[2]) + 1;
packageData.version = arr.join('.');
fs.writeFile(
    './package.json',
    JSON.stringify(packageData, null, "\t"),
    (err) => { }
);
fs.writeFile(
    './public/version.json',
    '{\r\t"version":"' + packageData.version + '"\r}',
    (err) => { }
);

console.log("ver", packageData.version);
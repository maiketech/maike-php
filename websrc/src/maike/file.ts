import * as XLSX from "xlsx";
import { isEmpty, isArray } from "lodash-es";

/**
 * 文件转Base64
 * @param file 
 * @returns 
 */
export function fileToBase64(file: any) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}

/**
 * 通过Blob下载文件
 * @param blobData 
 * @param fileName 
 */
export function download(fileData: BlobPart, fileName: string) {
    const blob = new Blob([fileData]);//处理文档流
    const down = document.createElement('a');
    down.download = fileName;
    down.style.display = 'none';
    down.href = URL.createObjectURL(blob);
    document.body.appendChild(down);
    down.click();
    URL.revokeObjectURL(down.href); // 释放URL 对象
    document.body.removeChild(down);//下载完成移除
}

export function downloadByUrl(url: string, fileName: string) {
    const down = document.createElement('a');
    down.download = fileName;
    down.style.display = 'none';
    down.href = url;
    document.body.appendChild(down);
    down.click();
    document.body.removeChild(down);//下载完成移除
}

export function selectLocalFile(callback: any) {
    var inputObj = document.createElement('input')
    inputObj.setAttribute('id', 'file');
    inputObj.setAttribute('type', 'file');
    inputObj.setAttribute('name', 'file');
    inputObj.setAttribute("style", 'visibility:hidden');
    inputObj.addEventListener('change', (e: any) => {
        const files = e.target.files;
        typeof callback === 'function' && callback(files);
    });
    document.body.appendChild(inputObj);
    inputObj.value;
    inputObj.click();
}


export function get_obj_first_attr(data: any, type = 'value') {
    for (var key in data)
        return type == 'key' ? key : data[key];
}

//导入Excel
export function importExcel(file: Blob) {
    return new Promise((resolve, reject) => {
        var reader = new FileReader();
        FileReader.prototype.readAsBinaryString = function (f) {
            var binary = "";
            //var rABS = false; //是否将文件读取为二进制字符串
            var workbook: any; //读取完成的数据
            var excelData: any = [];
            var reader: any = new FileReader();
            reader.onload = function () {
                try {
                    var bytes = new Uint8Array(reader.result);
                    var length = bytes.byteLength;
                    for (var i = 0; i < length; i++) {
                        binary += String.fromCharCode(bytes[i]);
                    }
                    workbook = XLSX.read(binary, {
                        type: "binary"
                    });
                } catch (e) {
                    reject(e)
                }
                // const sheet2JSONOpts: any = {
                //     /** Default value for null/undefined values */
                //     defval: '' //给defval赋值为空的字符串
                // }
                for (var sheet in workbook.Sheets) {
                    if (workbook.Sheets.hasOwnProperty(sheet)) {
                        var roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheet], { header: 1, defval: '', blankrows: false });
                        if (roa.length > 0) excelData.push(roa);
                    }
                }
                resolve(excelData);
            };
            reader.readAsArrayBuffer(f);
        };
        reader.readAsBinaryString(file);
    });
}

/**
   * 导出Excel的处理函数--针对table
   * @param {Array} headers: [{key: 'date', title: '日期'}, {key: 'name', title: '名称'}]
   * @param {Array} data   : [{date: '2019-05-31', name: 'megen.huang'}, {date: 'name', name: '小明'}]
   * @param {String} fileName: '导出结果.xlsx'
   * */
export const exportExcel = ({
    data,
    header,
    filename = '导出列表数据',
    merge_cell = []
}: any) => {
    let json2sheetOpts: any = {};
    //let write2excelOpts: any = { bookType: 'xlsx' };
    let arrData: any = [...data];
    let merges: any = [];
    let colWidth: any = [];
    if (header) {
        let headRow: any = [];
        arrData = data
            .map((item: any, i: number) => {
                let row: any = {};
                for (let key in header) {
                    headRow[key] = header[key].text;
                    if (key == "_index") {
                        row[key] = (i + 1);
                    } else {
                        if (item.hasOwnProperty(key)) {
                            row[key] = item[key];
                        } else {
                            row[key] = '';
                        }
                    }
                }
                if (!isEmpty(row)) return row;
            });
        arrData.unshift(headRow);
        json2sheetOpts.skipHeader = true;

        //设置列宽
        for (let key in header) {
            colWidth.push({ wch: header[key].width });
        }

        //增加标题行
        let titleRow: any = {};
        let fKey = get_obj_first_attr(header, 'key');
        titleRow[fKey] = filename;
        for (let key in header) {
            if (key != fKey) {
                titleRow[key] = '';
            }
        }
        arrData.unshift(titleRow);
        // 设置合并标题行单元格
        let merges_title = { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } };
        merges_title['e']['c'] = Object.keys(titleRow).length - 1;
        merges.push(merges_title);
    }

    const worksheet = XLSX.utils.json_to_sheet(arrData, json2sheetOpts);
    //列宽设置
    if (header && isArray(colWidth) && colWidth.length > 0) {
        worksheet['!cols'] = colWidth;
    } else {
        arrData.map((row: any) => {
            let rowSet: any[] = [];
            for (let [value] of Object.entries(row) as any) {
                if (value == null || value == undefined || value == 'unknown') {
                    rowSet.push({
                        'wch': 10
                    });
                } else if (value.toString().charCodeAt(0) > 255) {
                    rowSet.push({
                        'wch': value.toString().length * 2
                    });
                } else {
                    rowSet.push({
                        'wch': value.toString().length
                    });
                }
            }
            colWidth.push(rowSet)
        })
        /*以第一行为初始值*/
        let result: any = colWidth[0];
        for (let i = 1; i < colWidth.length; i++) {
            for (let j = 0; j < colWidth[i].length; j++) {
                if (result[j]['wch'] < colWidth[i][j]['wch']) {
                    result[j]['wch'] = colWidth[i][j]['wch'];
                }
            }
        }
        worksheet['!cols'] = result;
    }

    if (isArray(merge_cell) && merge_cell.length > 0) {
        merges = [...merges, ...merge_cell];
    }

    if (merges != null && isArray(merges)) {
        worksheet['!merges'] = merges;
    }

    //设置单元格样式
    for (let [key] of Object.entries(worksheet)) {
        if (key.startsWith('!')) continue;
        // value是一个单元格对象（Cell Objects）,s表格为样式对象（Style Objects）
        worksheet[key].s = {
            alignment: { vertical: 'center', horizontal: 'center' },
            border: {
                top: { style: 'thin', color: '000000' },
                bottom: { style: 'thin', color: '000000' },
                left: { style: 'thin', color: '000000' },
                right: { style: 'thin', color: '000000' }
            }
        }
    }

    /* add worksheet to workbook */
    const workbook = {
        SheetNames: [filename],
        Sheets: {
            [filename]: worksheet,
        },
    };

    let wopts: any = { bookType: 'xlsx', bookSST: false, type: 'binary' };
    let wbout = XLSX.write(workbook, wopts);
    function s2ab(s: any) {
        let buf = new ArrayBuffer(s.length);
        let view = new Uint8Array(buf);
        for (let i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }
    // 导出表格
    download(s2ab(wbout), filename + ".xlsx");
}

export const columns2XlsxHeader = (_columns: any) => {
    let header: any = {};
    if (!isArray(_columns)) return false;
    for (const [index, item] of _columns.entries()) {
        if (item.dataIndex && !isEmpty(item.dataIndex)) {
            header[item.dataIndex] = {
                text: item.title,
                width: !item.width ? 25 : parseInt(item.width) / 8
            }
        }
    }
    return header;
}
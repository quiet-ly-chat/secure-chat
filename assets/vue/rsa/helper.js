export function textToArrayBuffer(str) {
    // var buf = unescape(encodeURIComponent(str)) // 2 bytes for each char
    // var bufView = new Uint8Array(buf.length)
    // for (var i = 0; i < buf.length; i++) {
    //     bufView[i] = buf.charCodeAt(i)
    // }
    // return bufView
    return new TextEncoder("utf-8").encode(str);
}

export function arrayBufferToText(arrayBuffer) {
    var byteArray = new Uint8Array(arrayBuffer)
    var str = ''
    // for (var i = 0; i < byteArray.byteLength; i++) {
    //     str += String.fromCharCode(byteArray[i])
    // }
    return new TextDecoder().decode(byteArray);
    // return str
}

export default {textToArrayBuffer, arrayBufferToText};
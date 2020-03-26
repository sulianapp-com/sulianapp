//update 2018-12-03 16:00:24
let popularize = {"wechat":{"vue_route":[],"url":""},"mini":{"vue_route":[],"url":""},"wap":{"vue_route":[],"url":""},"app":{"vue_route":[],"url":""},"alipay":{"vue_route":[],"url":""}};
if (typeof define === "function") {
    define(popularize)
} else {
    window.$popularize = popularize;
}
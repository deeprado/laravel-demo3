
import Vue from 'vue';
import Vuex from 'vuex';
import news from './news';
Vue.use(Vuex);
export default new Vuex.Store({
    // 可以设置多个模块
    modules: {
        news
    }
});

// resource/assets/js/store/news.js
import api from '../api';
export default{
    state: {
        recommend: [], // 推荐
        lists: [],  // 列表
        detail: {}  // 详情
    },
    mutations: {
        // 注意，这里可以设置 state 属性，但是不能异步调用，异步操作写到 actions 中
        SETRECOMMEND(state, lists) {
            state.recommend = lists;
        },
        SETLISTS(state, lists) {
            state.lists = lists;
        },
        SETDETAIL(state, detail) {
            state.detail = detail;
        }
    },
    actions: {
        getNewsDetail({commit}, id) {
            // 获取详情，并调用 mutations 设置 detail
            api.getNewsDetail(id).then(function(res) {
                commit('SETDETAIL', res.data);
                document.body.scrollTop = 0;
            });
        },
        getNewsRecommend({commit}) {
            api.getNewsRecommend().then(function(res) {
                commit('SETRECOMMEND', res.data);
            });
        },
        getNewsLists({commit}) {
            api.getNewsLists().then(function(res) {
                commit('SETLISTS', res.data);
            });
        }
    }
}
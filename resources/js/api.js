
import axios from 'axios'
export default {
    // 首页推荐接口
    getNewsRecommend: function (params) {
        return axios.get('api/news/recommend', {
            params: params
        })
    },
    // 列表接口
    getNewsLists: function (params) {
        return axios.get('api/news', {
            params: params
        })
    },
    // 详情接口
    getNewsDetail: function (id) {
        return axios.get('api/news/' + id)
    }
}
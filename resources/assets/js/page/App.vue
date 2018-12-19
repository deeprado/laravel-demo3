
<template>
    <div class="panel panel-default">
        <div class="panel-heading">新闻推荐
            <router-link to="/list" class="pull-right">更多</router-link>
        </div>
        <ul class="list-group">
            <li class="list-group-item"
                v-for="row in recommend">
                <router-link :to="{path:'/detail/' + row.id}">
                    {{ row.title }}
                </router-link>
                <span class="pull-right">{{ row.created }}</span>
            </li>
        </ul>
    </div>
</template>
<script>
    import { mapState, mapActions } from 'vuex';
    export default({
        // 映射 vuex 上面的属性
        computed: mapState({
            recommend: state => state.news.recommend
        }),
        created() {
            // 获取推荐列表
            this.getNewsRecommend();
        },
        methods: {
            // 映射 vuex 对象上的方法
            ...mapActions([
                'getNewsRecommend'
            ])
        }
    });
</script>

# resource/assets/js/page/List.vue
<template>
    <div class="panel panel-default">
        <div class="panel-heading">新闻列表</div>
        <ul class="list-group">
            <li class="list-group-item"
                v-for="row in lists">
                <router-link :to="{path:'/detail/' + row.id}">
                    <span class="label label-success" v-if="row.is_recommend">推荐</span>
                    {{ row.title }}
                </router-link>
                <span class="pull-right">{{ row.created }}</span>
            </li>
        </ul>
    </div>
</template>
<script>
    import { mapState, mapActions } from 'vuex';
    export default({
        computed: mapState({
            lists: state => state.news.lists
        }),
        created() {
            this.getNewsLists();
        },
        methods: {
            ...mapActions([
                'getNewsLists'
            ])
        }
    });
</script>

# resource/assets/js/page/Detail.vue
<template>
    <div>
        <ol class="breadcrumb">
            <li><a href="/">首页</a></li>
            <li><router-link to="/list" class="pull-right">新闻</router-link></li>
            <li class="active">{{ detail.title }}</li>
        </ol>
        <h3><span class="label label-success" v-if="detail.is_recommend">推荐</span> {{ detail.title }}</h3>
        <p>创建时间：{{ detail.created_at }}</p>
        <div>
            {{ detail.content }}
        </div>
    </div>
</template>
<style>
    .breadcrumb{
        padding: 8px 0;
    }
</style>
<script>
    import { mapState, mapActions } from 'vuex';
    export default({
        computed: mapState({
            detail: state => state.news.detail
        }),
        created() {
            // 获取路由参数id
            // js 中用 this.$route 获取当前路由，用 this.$router 获路由对象，全部路由信息
            // 在模板中用 $router  和 $router 直接调用
            var id = this.$route.params.id;
            this.getNewsDetail(id);
        },
        methods: {
            ...mapActions([
                'getNewsDetail'
            ])
        }
    });
</script>
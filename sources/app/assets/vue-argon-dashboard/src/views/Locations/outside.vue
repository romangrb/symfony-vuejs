<template>
    <div v-html="html">

    </div>
</template>

<script>
    import http from '../../services/httpClient';

    export default {
        data() {
            return {
                html: ''
            }
        },
        mounted() {
            console.log(1);

            let urlPath = 'place/' + this.$route.params.id + '/template';
            let _this = this;

            http.get(urlPath).then((data) => {
                _this.html = data['gjs-html'];
            });
        }
    };
</script>

<style></style>

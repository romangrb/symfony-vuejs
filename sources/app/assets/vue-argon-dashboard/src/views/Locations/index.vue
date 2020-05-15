<template>
    <div v-if="this.$route.name != 'location-builder'">
        <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
        </base-header>

        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                     <router-view></router-view>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <router-view></router-view>
    </div>
</template>

<script>
    export default {

    };
</script>

<style></style>

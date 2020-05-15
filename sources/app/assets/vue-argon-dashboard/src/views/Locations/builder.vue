<template>
    <div id="gjs"></div>
</template>
<script>
  import grapesjs from 'grapesjs'
  import grapesjsPpresetWebpage from 'grapesjs-preset-webpage'

  export default {
    name: 'page-builder',
    components: {
        grapesjs,
        grapesjsPpresetWebpage
    },
    mounted() {
        let editor = grapesjs.init({
            container : '#gjs',
            plugins: ['gjs-preset-webpage'],
            pluginsOpts: {
                'gjs-preset-webpage': {
                    // options
                }
            }
        });
    }
  };
</script>

<style></style>

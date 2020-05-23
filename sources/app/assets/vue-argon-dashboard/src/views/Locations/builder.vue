<template>
    <div id="gjs"></div>
</template>

<script>
    import grapesjs from 'grapesjs'
    import grapesjsPpresetWebpage from 'grapesjs-preset-webpage'
    import http from "../../services/httpClient";

    export default {
        name: 'page-builder',
        components: {
            grapesjs,
            grapesjsPpresetWebpage
        },
        mounted() {
            let urlPath = 'place/' + this.$route.params.id + '/template';
            let assetUrlPath = 'place/' + this.$route.params.id + '/attachment';
            let _this = this;

            let editor = grapesjs.init({
                container: '#gjs',
                plugins: ['gjs-preset-webpage'],
                pluginsOpts: {
                    'gjs-preset-webpage': {
                        // options
                    }
                },
                storageManager: {
                    autosave: false,
                    setStepsBeforeSave: 1,
                    type: 'remote',
                    urlLoad: process.env.API_URL + urlPath,
                    contentTypeJson: true
                },
                assetManager: {
                    uploadName: 'attachment',
                    headers: {
                        'Authorization' : localStorage.getItem('token')
                    },
                    multiUpload: false,
                    upload: process.env.API_URL + assetUrlPath
                }
            });

            // Add save button
            editor.Panels.addButton('options',
                [{
                    id: 'save',
                    className: 'fa fa-floppy-o',
                    command: 'save-template',
                    attributes: {title: 'Save'}
                }]
            );

            // Add the command
            editor.Commands.add('save-template', {
                run: function (editor, sender) {
                    let message = 'Are you sure you want to save the template?';

                    if (sender && confirm(message)) {
                        sender.set('active', false); // turn off the button

                        let data = {
                          'html': editor.getHtml() + '<style>' + editor.getCss() + '</style>'
                        };

                        http.post(urlPath, data).then((data) => {
                            _this.$router.push({name: 'locations'});
                        });
                    }
                }
            });
        }
    };
</script>

<style></style>

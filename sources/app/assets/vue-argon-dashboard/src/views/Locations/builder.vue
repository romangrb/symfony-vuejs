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
                    urlStore: process.env.API_URL + 'place/' + this.$route.params.id + '/template',
                    urlLoad: process.env.API_URL + 'place/' + this.$route.params.id + '/template',
                    contentTypeJson: true,
                    headers: { Authorization: localStorage.getItem('token') }
                },
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

                    sender && confirm(message) && sender.set('active', false); // turn off the button
                    editor.store();
                }
            });
        }
    };
</script>

<style></style>

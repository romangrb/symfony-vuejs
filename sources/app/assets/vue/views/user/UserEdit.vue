<template>
    <div class="component">
        <h3>using event bus</h3>
        <p>Click to change!</p>
        <p>User Age: {{ age }}</p>
        <button @click="editAge">Click me</button>
    </div>
</template>

<script>
    import {eventBus} from '../../index';

    export default {
        data: function () {
            return {
                age: this._age
            };
        },
        props: {
            _age: Number
        },
        watch: {
            // for bind prop with data on changes
            _age: function () {
                this.age= this._age
            }
        },
        methods: {
            editAge() {
               this.age = 30;
               //  using event bus (global)
               //  this.$emit('ageWasEdited', this.age);
               //  eventBus.changeAge(this.age);
                eventBus.$emit('ageWasEdited', this.age);
            },
        }
    }
</script>

<style scoped>
    div {
        background-color: lightgreen;
    }
</style>

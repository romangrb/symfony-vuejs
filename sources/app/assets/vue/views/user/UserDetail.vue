<template>
    <div class="component">
        <h3>Using event</h3>
        <p>User Name: {{ _name }}</p>
        <p>User Age: {{ _age }}</p>
        <button @click="resetName">Reset Name</button>
        <button @click="resetFn()">Reset Name (using parent function)</button>
    </div>
</template>

<script>
    import { eventBus } from '../../index';

    export default {
        data: function () {
            return {
                name: this._name,
                age: this._age
            };
        },
        props: {
            _name: {
                type: String
            },
            _age: Number,
            resetFn: Function
        },
        methods: {
            resetName() {
                this.name = 'Max';
                //  using event $emit to notify parent
                this.$emit('nameWasReset', this.name);
            },
        },
        created() {
            // using bus listener (global)
            eventBus.$on('ageWasEdited', (age) => {
                this.age = age;
            });
        }
    }
</script>

<style scoped>
    div {
        background-color: lightcoral;
    }
</style>

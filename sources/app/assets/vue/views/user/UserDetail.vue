<template>
    <div class="component">
        <h3>You may view the User Details here</h3>
        <p>Many Details</p>
        <p>User Name: {{ myName }}</p>
        <p>User Age: {{ userAge }}</p>
        <!--<button @click="resetName">Reset Name</button>-->
        <button @click="resetFn()">Reset Name</button>
        <button @click="resetAge">Reset Age</button>
    </div>
</template>

<script>
    import { eventBus } from '../../index';

    export default {
        props: {
            myName: {
                type: String
            },
            resetFn: Function,
            userAge: Number
        },
        methods: {

            resetName() {
                this.myName = 'Max';
                this.$emit('nameWasReset', this.myName);
            },
            resetAge() {
                this.userAge = 10;
                eventBus.changeAge(this.userAge);
                // this.$emit('ageWasEdited', this.userAge);
            }
        },
        created() {
            eventBus.$on('ageWasEdited', (age) => {
                this.userAge = age;
            });
        }
    }
</script>

<style scoped>
    div {
        background-color: lightcoral;
    }
</style>

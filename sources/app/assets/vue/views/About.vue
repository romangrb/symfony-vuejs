<template>
  <div class="component">
    <h1>The User Component</h1>
    <p>I'm an awesome User!</p>
    <button @click="ResetUserData">ResetUserData</button>
    <p>Name is {{ name }}</p>
    <p>Age is {{ age }}</p>
    <hr>
    <div class="row">
      <div class="col-xs-12 col-sm-6">
        <app-user-detail
          :_name="name"
          :resetFn="resetName"
          @nameWasReset="name = $event"
          :_age="age"
        >
        </app-user-detail>
      </div>
      <div class="col-xs-12 col-sm-6">
        <app-user-edit
          :_age="age"
          @ageWasEdited="age = $event"
        >
        </app-user-edit>
      </div>
    </div>
  </div>
</template>

<script>
  import UserDetail from './user/UserDetail.vue';
  import UserEdit from './user/UserEdit.vue';
  import {eventBus} from '../index';

  export default {
    data: function () {
      return {
        name: 'Max . from parent',
        age: 27
      };
    },
    methods: {
      ResetUserData() {
        this.name = 'Anna';
        this.age = 27;
      },
      resetName() {
        this.name = 'Max . from parent';
      },
    },
    components: {
      appUserDetail: UserDetail,
      appUserEdit: UserEdit
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
    background-color: lightblue;
  }
</style>

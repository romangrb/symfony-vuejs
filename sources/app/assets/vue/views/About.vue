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
          :myName="name"
          :resetFn="resetName"
          @nameWasReset="name = $event"
          :userAge="age"
        >
        </app-user-detail>
      </div>
      <div class="col-xs-12 col-sm-6">
        <app-user-edit
          :userAge="age"
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
    }
  }
</script>

<style scoped>
  div {
    background-color: lightblue;
  }
</style>

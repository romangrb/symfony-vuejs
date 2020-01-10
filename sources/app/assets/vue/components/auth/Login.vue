<template>
  <div>
    <form 
      class="login" 
      @submit.prevent="login">
      <h1>Sign in</h1>
      <label>Email</label>
      <input 
        v-model="username" 
        required 
        type="username" 
        placeholder="Username">
      <label>Password</label>
      <input 
        v-model="password" 
        required 
        type="password" 
        placeholder="Password">
      <hr>
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: ""
    };
  },
  methods: {
    login: function() {
      let username = this.username;
      let password = this.password;
      this.$store
        .dispatch("login", { username, password })
        .then(() => this.$router.push("/"))
        .catch(err => console.warn(err));
    }
  }
};
</script>
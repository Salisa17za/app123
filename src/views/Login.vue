<template>
  <div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center mb-4">Login </h3>
    <form @submit.prevent="login">
      <div class="mb-3">
        <label>Username</label>
        <input v-model="username" type="text" class="form-control" required />
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input v-model="password" type="password" class="form-control" required />
      </div>
      <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>
  </div>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      username: "",
      password: "",
    };
  },
  methods: {
    login() {
      // ระบบ login แบบง่าย: กรอกอะไรก็ได้
      localStorage.setItem("adminLogin", "true");
      localStorage.setItem("username", this.username);
      alert("Login สำเร็จ!");
      this.$router.push("/asset_crud"); // ไปหน้าจัดการครุภัณฑ์
    },
  },
};
</script>

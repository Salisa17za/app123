<template>
  <div>
    <nav class="navbar navbar-expand-lg" style="background-color: #86bfe7ff;">
      <div class="container">
        <a class="navbar-brand fw-bold text-white" href="/show">App 123 . com </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <!-- แสดงเฉพาะเมื่อ Login แล้ว -->
            <template v-if="isLoggedIn">
              <li class="nav-item">
                <router-link class="nav-link" to="/man">categories edit</router-link>
              </li>

              <li class="nav-item">
                <a class="nav-link text-danger" href="/log" @click="logout">Logout</a>
              </li>
            </template>

            <!-- แสดงเฉพาะเมื่อยังไม่ได้ Login -->
            <template v-else>
              <li class="nav-item">
                <router-link class="nav-link" to="/show">categories</router-link>
              </li>

              <li class="nav-item">
                <router-link class="nav-link" to="/log">Login</router-link>
              </li>
            </template>

          </ul>

          <!-- ช่องค้นหาเฉพาะตอน Login -->
          <form class="d-flex" role="search" v-if="isLoggedIn">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-light" type="submit">Search</button>
          </form>
          
        </div>
      </div>
    </nav>

    <!-- เนื้อหาแต่ละหน้า -->
    <router-view />
  </div>
</template>

<script>
export default {
  name: "Navbar",
  data() {
    return {
      isLoggedIn: false,
    };
  },
  mounted() {
    this.checkLogin();
  },
  methods: {
    checkLogin() {
      this.isLoggedIn = localStorage.getItem("adminLogin") === "true";
    },
    logout() {
      if (confirm("ต้องการออกจากระบบหรือไม่?")) {
        localStorage.removeItem("adminLogin");
        localStorage.removeItem("username");
        localStorage.removeItem("token");

        this.isLoggedIn = false;
        this.$router.push("/");
      }
    },
  },
  watch: {
    $route() {
      this.checkLogin();
    },
  },
};
</script>

<style scoped>
.navbar {
  background-color: #86bfe7ff !important;
}
.nav-link {
  color: white !important;
  font-weight: 500;
}
.nav-link:hover {
  text-decoration: underline;
}
</style>

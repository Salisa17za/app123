<template>
  <nav class="navbar navbar-expand-lg" style="background-color: #86bfe7ff;">
    <div class="container">
      <a class="navbar-brand fw-bold text-white" href="/">App 123 .com</a>
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

          <!-- แสดงเฉพาะเมื่อเข้าสู่ระบบแล้ว -->
          <template v-if="isLoggedIn">  
            <li class="nav-item">
              <router-link class="nav-link" to="/man">
                <i class="bi bi-box-seam"></i> office
              </router-link>
            </li>
            <li class="nav-item">
              <span class="nav-link">
                <i class="bi bi-person-circle"></i> สวัสดี, {{ username }}
              </span>
            </li>
          </template>

          <!-- แสดงเฉพาะเมื่อยังไม่ได้เข้าสู่ระบบ -->
          <template v-else>
            
            <li class="nav-item">
              <router-link class="nav-link" to="/">
                <i class="bi bi-box-arrow-in-right"></i> รายการ
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/log">
                <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
              </router-link>
            </li>
          </template>

        </ul>

        <!-- ปุ่ม Logout แสดงเฉพาะเมื่อล็อกอินแล้ว -->
        <div v-if="isLoggedIn">
          <button class="btn btn-outline-light" @click="logout">
            <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- แสดงหน้าต่างเนื้อหา -->
  <router-view @login-success="handleLoginSuccess" />
</template>

<script>
export default {
  name: "Navbar",
  data() {
    return {
      isLoggedIn: false,
      username: "",
    };
  },
  mounted() {
    // ตรวจสอบสถานะเมื่อโหลดหน้า
    this.checkLogin();
    
    // ฟัง event จาก window เมื่อมีการ login
    window.addEventListener('storage', this.checkLogin);
  },
  beforeUnmount() {
    window.removeEventListener('storage', this.checkLogin);
  },
  methods: {
    checkLogin() {
      // ✅ ตรวจสอบทั้ง isLoggedIn และ adminLogin
      this.isLoggedIn = 
        localStorage.getItem("isLoggedIn") === "true" || 
        localStorage.getItem("adminLogin") === "true";
      
      this.username = localStorage.getItem("username") || "ผู้ใช้";
    },
    handleLoginSuccess() {
      // เมื่อ login สำเร็จ ให้อัปเดตสถานะ
      this.checkLogin();
    },
    logout() {
      if (confirm("ต้องการออกจากระบบหรือไม่?")) {
        // เคลียร์ข้อมูลทั้งหมดที่เกี่ยวข้องกับการล็อกอิน
        localStorage.removeItem("isLoggedIn");
        localStorage.removeItem("adminLogin");
        localStorage.removeItem("username");
        localStorage.removeItem("customer_id");
        localStorage.removeItem("firstName");
        localStorage.removeItem("lastName");
        localStorage.removeItem("token");
        
        this.isLoggedIn = false;
        this.username = "";

        // แสดงข้อความและกลับไปหน้าเมนูหลัก
        alert("ออกจากระบบเรียบร้อยแล้ว");
        this.$router.push("/");
      }
    },
  },
  watch: {
    // เมื่อเปลี่ยนเส้นทาง ให้ตรวจสอบสถานะการล็อกอินใหม่
    $route() {
      this.checkLogin();
    },
  },
};
</script>

<style scoped>
.navbar {
  background-color: #86bfe7ff !important;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.nav-link {
  color: white !important;
  font-weight: 500;
  transition: all 0.3s ease;
}
.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 5px;
  transform: translateY(-2px);
}
.btn-outline-light {
  font-weight: 500;
  transition: all 0.3s ease;
}
.btn-outline-light:hover {
  background-color: white;
  color: #86bfe7ff;
  transform: scale(1.05);
}
.navbar-brand {
  font-size: 1.5rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}
</style>
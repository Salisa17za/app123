<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">📋 รายการครุภัณฑ์สำนักงาน</h2>

    <input
      type="text"
      class="form-control mb-3"
      placeholder="🔍 ค้นหาครุภัณฑ์..."
      v-model="search"
    />

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>รหัสครุภัณฑ์</th>
          <th>ชื่อครุภัณฑ์</th>
          <th>หมวดหมู่</th>
          <th>วันที่ซื้อ</th>
          <th>ราคา</th>
          <th>รูปภาพ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredAssets" :key="item.asset_id">
          <td>{{ item.asset_code }}</td>
          <td>{{ item.asset_name }}</td>
          <td>{{ item.category_name }}</td>
          <td>{{ item.purchase_date }}</td>
          <td>{{ item.price }} บาท</td>
          <td>
            <img
              v-if="item.image"
              :src="'http://localhost/app123/api_php/uploads/' + item.image"
              class="img-thumbnail"
              style="width: 80px;"
            />
            <span v-else>ไม่มีรูป</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

export default {
  name: "AssetShow",
  setup() {
    const search = ref("");
    const assets = ref([]);
    const loading = ref(true);
    const error = ref(null);

    // ฟังก์ชันดึงข้อมูลจาก API
    const fetchAssets = async () => {
      try {
        const response = await axios.get('http://localhost/app123/api_php/get_assets.php');
        if (response.data.success) {
          assets.value = response.data.data; // เก็บข้อมูลจาก API
        } else {
          error.value = "ไม่สามารถโหลดข้อมูลได้";
        }
      } catch (err) {
        error.value = "เกิดข้อผิดพลาด: " + err.message;
      } finally {
        loading.value = false;
      }
    };

    // คำนวณการกรองข้อมูลจากการค้นหาของผู้ใช้
    const filteredAssets = computed(() =>
      assets.value.filter((a) =>
        a.asset_name.toLowerCase().includes(search.value.toLowerCase())
      )
    );

    // เมื่อหน้าเพจถูกโหลด ให้ดึงข้อมูลจาก API
    onMounted(fetchAssets);

    return {
      search,
      filteredAssets,
      loading,
      error,
    };
  },
};
</script>

<style scoped>
/* ใส่ CSS สำหรับการจัดรูปแบบเพิ่มเติม */
</style>

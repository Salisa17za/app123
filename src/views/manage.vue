<template>
  <div class="container mt-4">
    <h2 class="mb-3">ตารางครุภัณฑ์</h2>

    <!-- 🔹 ปุ่มเพิ่ม + ตัวเลือกจำนวนแถว -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <button class="btn btn-primary" @click="openAddModal">เพิ่มครุภัณฑ์</button>

      <div class="d-flex align-items-center">
        <label class="me-2">แสดงแถวต่อหน้า:</label>
        <select v-model.number="itemsPerPage" class="form-select w-auto">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="20">20</option>
        </select>
      </div>
    </div>

    <!-- 🏷️ ปุ่มกรองหมวดหมู่ -->
    <div class="mb-3">
      <label class="fw-bold mb-2">หมวดหมู่:</label>
      <div class="d-flex flex-wrap gap-2">
        <button 
          class="btn btn-sm"
          :class="categoryFilter === '' ? 'btn-primary' : 'btn-outline-primary'"
          @click="categoryFilter = ''"
        >
          ทั้งหมด
        </button>
        <button 
          v-for="category in categories" 
          :key="category"
          class="btn btn-sm"
          :class="categoryFilter === category ? 'btn-success' : 'btn-outline-success'"
          @click="categoryFilter = category"
        >
          {{ category }}
        </button>
      </div>
    </div>

    <!-- ✅ ตารางครุภัณฑ์ -->
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>รหัส</th>
          <th>ชื่อครุภัณฑ์</th>
          <th>หมวดหมู่</th>
          <th>วันที่ซื้อ</th>
          <th>ราคา</th>
          <th>รูปภาพ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="asset in paginatedAssets" :key="asset.asset_id">
          <td class="text-center">{{ asset.asset_code }}</td>
          <td>{{ asset.asset_name }}</td>
          <td class="text-center">
            <span class="badge bg-info">{{ asset.category_id }}</span>
          </td>
          <td class="text-center">{{ formatDate(asset.purchase_date) }}</td>
          <td class="text-end">{{ formatPrice(asset.price) }}</td>
          <td class="text-center">
            <img
              v-if="asset.image"
              :src="'http://localhost/app123/api_php/uploads/' + asset.image"
              width="80"
              class="rounded"
              alt="รูปครุภัณฑ์"
            />
            <span v-else class="text-muted">ไม่มีรูป</span>
          </td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(asset)">
              <i class="bi bi-pencil-square"></i> แก้ไข
            </button>
            <button class="btn btn-danger btn-sm" @click="deleteAsset(asset.asset_id)">
              <i class="bi bi-trash3"></i> ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- ✅ ระบบแบ่งหน้า -->
    <nav v-if="totalPages > 1" class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="prevPage">ก่อนหน้า</button>
        </li>

        <li
          class="page-item"
          v-for="page in totalPages"
          :key="page"
          :class="{ active: currentPage === page }"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>

        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="nextPage">ถัดไป</button>
        </li>
      </ul>
    </nav>

    <!-- ✅ Modal ใช้ทั้งเพิ่ม / แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขครุภัณฑ์" : "เพิ่มครุภัณฑ์ใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div>
              <div class="mb-3">
                <label class="form-label">รหัสครุภัณฑ์</label>
                <input v-model="editForm.asset_code" type="text" class="form-control" required maxlength="50" />
              </div>

              <div class="mb-3">
                <label class="form-label">ชื่อครุภัณฑ์</label>
                <input v-model="editForm.asset_name" type="text" class="form-control" required maxlength="255" />
              </div>

              <div class="mb-3">
                <label class="form-label">หมวดหมู่</label>
                <select v-model="editForm.category_id" class="form-select" required>
                  <option value="">-- เลือกหมวดหมู่ --</option>
                  <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">วันที่ซื้อ</label>
                <input v-model="editForm.purchase_date" type="date" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">ราคา (บาท)</label>
                <input v-model="editForm.price" type="number" step="0.01" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">รูปภาพ</label>
                <input
                  type="file"
                  @change="handleFileUpload"
                  class="form-control"
                  accept="image/*"
                />
                <div v-if="isEditMode && editForm.image">
                  <p class="mt-2">รูปเดิม:</p>
                  <img
                    :src="'http://localhost/app123/api_php/uploads/' + editForm.image"
                    width="100"
                    class="rounded"
                    alt="รูปครุภัณฑ์เดิม"
                  />
                </div>
              </div>

              <button @click="saveAsset" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกครุภัณฑ์ใหม่" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from "vue";

export default {
  name: "AssetList",
  setup() {
    const assets = ref([]);
    const categories = ref([
      'อุปกรณ์คอมพิวเตอร์',
      'อุปกรณ์สำนักงาน',
      'เครื่องใช้ไฟฟ้า',
      'เฟอร์นิเจอร์',
      'อื่นๆ'
    ]);
    const loading = ref(true);
    const error = ref(null);
    const isEditMode = ref(false);
    const categoryFilter = ref("");
    const editForm = ref({
      asset_id: null,
      asset_code: "",
      asset_name: "",
      category_id: "",
      purchase_date: "",
      price: "",
      image: ""
    });
    const newImageFile = ref(null);
    let modalInstance = null;

    // ✅ Pagination
    const currentPage = ref(1);
    const itemsPerPage = ref(5);

    // จัดรูปแบบวันที่
    const formatDate = (dateString) => {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    // จัดรูปแบบราคา
    const formatPrice = (price) => {
      return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
      }).format(price);
    };

    // กรองตามหมวดหมู่
    const filteredAssets = computed(() => {
      if (categoryFilter.value === "") return assets.value;
      return assets.value.filter(a => a.category_id === categoryFilter.value);
    });

    const totalPages = computed(() =>
      Math.ceil(filteredAssets.value.length / itemsPerPage.value)
    );

    const paginatedAssets = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return filteredAssets.value.slice(start, start + itemsPerPage.value);
    });

    const goToPage = (page) => {
      currentPage.value = page;
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) currentPage.value++;
    };

    const prevPage = () => {
      if (currentPage.value > 1) currentPage.value--;
    };

    // รีเซ็ตหน้ากลับไปหน้า 1
    watch([itemsPerPage, categoryFilter], () => {
      currentPage.value = 1;
    });

    // โหลดข้อมูล
    const fetchAssets = async () => {
      try {
        const res = await fetch("http://localhost/app123/api_php/api_assets.php");
        const data = await res.json();
        assets.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    const openAddModal = () => {
      isEditMode.value = false;
      editForm.value = {
        asset_id: null,
        asset_code: "",
        asset_name: "",
        category_id: "",
        purchase_date: "",
        price: "",
        image: ""
      };
      newImageFile.value = null;
      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
      const fileInput = modalEl.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = "";
    };

    const openEditModal = (asset) => {
      isEditMode.value = true;
      editForm.value = { ...asset };
      newImageFile.value = null;
      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (event) => {
      newImageFile.value = event.target.files[0];
    };

    const saveAsset = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("asset_id", editForm.value.asset_id);
      formData.append("asset_code", editForm.value.asset_code);
      formData.append("asset_name", editForm.value.asset_name);
      formData.append("category_id", editForm.value.category_id);
      formData.append("purchase_date", editForm.value.purchase_date);
      formData.append("price", editForm.value.price);
      if (newImageFile.value) formData.append("image", newImageFile.value);

      try {
        const res = await fetch("http://localhost/app123/api_php/api_assets.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.success) {
          alert(result.message);
          fetchAssets();
          modalInstance.hide();
        } else {
          alert(result.message || result.error);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    const deleteAsset = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("asset_id", id);

      try {
        const res = await fetch("http://localhost/app123/api_php/api_assets.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.success) {
          alert(result.message);
          fetchAssets();
        } else {
          alert(result.message || result.error);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    onMounted(fetchAssets);

    return {
      assets,
      categories,
      loading,
      error,
      editForm,
      isEditMode,
      categoryFilter,
      formatDate,
      formatPrice,
      openAddModal,
      openEditModal,
      handleFileUpload,
      saveAsset,
      deleteAsset,

      // Pagination
      currentPage,
      totalPages,
      paginatedAssets,
      itemsPerPage,
      goToPage,
      nextPage,
      prevPage
    };
  }
};
</script>

<style scoped>
.badge { font-size: 0.85rem; }
.rounded { border-radius: 8px; }
</style>
<template>
  <div class="container mt-5">
    <h2>จัดการครุภัณฑ์</h2>
    <button class="btn btn-primary mb-3" @click="openAddModal">เพิ่มครุภัณฑ์</button>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>รหัส</th>
          <th>ชื่อครุภัณฑ์</th>
          <th>หมวดหมู่</th>
          <th>วันที่ซื้อ</th>
          <th>ราคา</th>
          <th>รูปภาพ</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in assets" :key="item.asset_id">
          <td>{{ item.asset_code }}</td>
          <td>{{ item.asset_name }}</td>
          <td>{{ item.category_name }}</td>
          <td>{{ item.purchase_date }}</td>
          <td>{{ item.price }}</td>
          <td>
            <img v-if="item.image" :src="'http://localhost/app123/api_php/uploads/' + item.image" alt="asset image" class="img-thumbnail" style="width: 100px;" />
            <span v-else>ไม่มีรูป</span>
          </td>
          <td>
            <button class="btn btn-warning btn-sm" @click="openEditModal(item)">แก้ไข</button>
            <button class="btn btn-danger btn-sm" @click="deleteAsset(item.asset_id)">ลบ</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal แบบ Vue -->
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal-dialog">
        <div class="modal-content p-3">
          <h5>{{ isEditMode ? 'แก้ไขครุภัณฑ์' : 'เพิ่มครุภัณฑ์' }}</h5>
          <form @submit.prevent="saveAsset" enctype="multipart/form-data">
            <input v-model="editForm.asset_code" placeholder="รหัสครุภัณฑ์" required class="form-control mb-2"/>
            <input v-model="editForm.asset_name" placeholder="ชื่อครุภัณฑ์" required class="form-control mb-2"/>
            <input v-model="editForm.category_name" placeholder="หมวดหมู่" required class="form-control mb-2"/>
            <input v-model="editForm.purchase_date" type="date" required class="form-control mb-2"/>
            <input v-model="editForm.price" type="number" required class="form-control mb-2"/>
            
            <!-- เพิ่มช่องสำหรับอัปโหลดรูปภาพ -->
            <div class="mb-3">
              <label for="image" class="form-label">เลือกรูปภาพ</label>
              <input type="file" id="image" @change="handleImageUpload" class="form-control"/>
              <div v-if="imagePreview" class="mt-2">
                <p>ตัวอย่างรูปภาพ:</p>
                <img :src="imagePreview" alt="Image Preview" class="img-thumbnail" style="width: 150px;">
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-success me-2">บันทึก</button>
              <button type="button" class="btn btn-secondary" @click="showModal = false">ยกเลิก</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const assets = ref([]);
    const showModal = ref(false);
    const isEditMode = ref(false);
    const editForm = ref({
      asset_id: null,
      asset_code: '',
      asset_name: '',
      category_name: '',
      purchase_date: '',
      price: '',
      image: null  // เพิ่มฟิลด์สำหรับเก็บข้อมูลรูปภาพ
    });

    const imagePreview = ref(null);  // สำหรับแสดงตัวอย่างรูปภาพที่อัปโหลด

    const fetchAssets = async () => {
      const res = await axios.get('http://localhost/app123/api_php/get_assets.php');
      assets.value = res.data.data;
    };

    const openAddModal = () => {
      isEditMode.value = false;
      editForm.value = {
        asset_id: null,
        asset_code: '',
        asset_name: '',
        category_name: '',
        purchase_date: '',
        price: '',
        image: null
      };
      imagePreview.value = null;  // เคลียร์การแสดงตัวอย่างรูปภาพ
      showModal.value = true;
    };

    const openEditModal = (asset) => {
      isEditMode.value = true;
      editForm.value = { ...asset };
      imagePreview.value = asset.image ? `http://localhost/app123/api_php/uploads/` + asset.image : null;  // แสดงตัวอย่างรูปภาพเดิม
      showModal.value = true;
    };

    const handleImageUpload = (event) => {
      const file = event.target.files[0];
      if (file) {
        imagePreview.value = URL.createObjectURL(file);  // แสดงตัวอย่างรูปภาพที่อัปโหลด
        editForm.value.image = file;  // เก็บข้อมูลไฟล์ภาพใน form
      }
    };

    const saveAsset = async () => {
      const formData = new FormData();
      formData.append('asset_code', editForm.value.asset_code);
      formData.append('asset_name', editForm.value.asset_name);
      formData.append('category_name', editForm.value.category_name);
      formData.append('purchase_date', editForm.value.purchase_date);
      formData.append('price', editForm.value.price);

      // ถ้ามีการอัปโหลดรูปภาพ ก็จะส่งไฟล์ไปด้วย
      if (editForm.value.image) {
        formData.append('image', editForm.value.image);
      }

      try {
        if (isEditMode.value) {
          formData.append('asset_id', editForm.value.asset_id);  // ถ้าเป็นโหมดแก้ไข ก็ต้องส่ง ID ด้วย
          await axios.post('http://localhost/app123/api_php/update_asset.php', formData);
        } else {
          await axios.post('http://localhost/app123/api_php/add_asset.php', formData);
        }
        showModal.value = false;
        fetchAssets();
      } catch (err) {
        alert('เกิดข้อผิดพลาด: ' + err.message);
      }
    };

    const deleteAsset = async (id) => {
      if (!confirm('คุณแน่ใจว่าจะลบ?')) return;
      try {
        await axios.post('http://localhost/app123/api_php/delete_asset.php', { asset_id: id });
        fetchAssets();
      } catch (err) {
        alert('เกิดข้อผิดพลาด: ' + err.message);
      }
    };

    onMounted(fetchAssets);

    return { assets, showModal, isEditMode, editForm, openAddModal, openEditModal, saveAsset, deleteAsset, handleImageUpload, imagePreview };
  }
};
</script>

<style>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-dialog { background: white; border-radius: 6px; width: 400px; }
</style>

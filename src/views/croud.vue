<script>
import axios from "axios";
import { ref, reactive, onMounted } from "vue";

export default {
  setup() {
    const assets = ref([]);
    const form = reactive({
      asset_id: null,
      asset_code: "",
      asset_name: "",
      category_id: 1,
      purchase_date: "",
      price: 0,
    });
    const editIndex = ref(null);

    const fetchAssets = async () => {
      const res = await axios.get("http://localhost/your_api/show_assets.php");
      assets.value = res.data.data;
    };

    const saveAsset = async () => {
      let action = editIndex.value !== null ? "edit" : "add";
      await axios.post("http://localhost/your_api/crud_assets.php", {...form, action});
      fetchAssets();
      // เคลียร์ฟอร์ม
      form.asset_id = null;
      form.asset_code = "";
      form.asset_name = "";
      form.category_id = 1;
      form.purchase_date = "";
      form.price = 0;
      editIndex.value = null;
    };

    const editAsset = (item, index) => {
      Object.assign(form, item);
      editIndex.value = index;
    };

    const deleteAsset = async (asset_id) => {
      if(confirm("ลบครุภัณฑ์นี้หรือไม่?")){
        await axios.post("http://localhost/your_api/crud_assets.php", {asset_id, action:"delete"});
        fetchAssets();
      }
    };

    onMounted(fetchAssets);

    return { assets, form, editIndex, saveAsset, editAsset, deleteAsset };
  }
};
</script>

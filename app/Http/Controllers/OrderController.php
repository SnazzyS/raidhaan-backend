<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { getOrders, updateOrder } from "@/services/orderService";
import usePrinter from "@/composables/usePrinter"; // make sure this path is correct

const { printText } = usePrinter();
const router = useRouter();
const orders = ref([]);
const loading = ref(true);

// Load orders
onMounted(async () => {
  try {
    const res = await getOrders();
    orders.value = res.data;
  } catch (e) {
    console.error("Failed to load orders", e);
  } finally {
    loading.value = false;
  }

  window.addEventListener("print-receipt", (e) => {
    printText(e.detail); // e.detail contains the raw receipt text
  });

  qz.printers.find().then(console.log);
});

// API call to get receipt text
async function printOrder(orderId) {
  try {
    const { data } = await axios.get(`/api/orders/${orderId}/receipt`);
    window.dispatchEvent(new CustomEvent("print-receipt", { detail: data.receipt }));
  } catch (e) {
    alert("Failed to print");
  }
}

const handleEdit = (order) => {
  router.push({ name: "order-edit", params: { id: order.id } });
};

const handleComplete = async (order) => {
  if (order.status === "completed") return;
  try {
    await updateOrder(order.id, { ...order, status: "completed" });
    order.status = "completed";
  } catch (err) {
    console.error("Complete failed", err);
  }
};
</script>
